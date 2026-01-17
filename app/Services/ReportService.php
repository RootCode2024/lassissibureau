<?php

namespace App\Services;

use App\Enums\ProductLocation;
use App\Enums\ProductState;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Reseller;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function __construct(
        private SaleService $saleService,
        private StockService $stockService
    ) {}

    /**
     * Rapport quotidien - VERSION CORRIGÉE
     * Retourne les ventes complètes avec leurs relations
     */
    public function getDailyReport($date, ?int $userId = null): array
    {
        $startDate = $date.' 00:00:00';
        $endDate = $date.' 23:59:59';

        // ✅ Pas de filtrage par userId
        $sales = Sale::with(['product.productModel', 'seller', 'reseller', 'tradeIn'])
            ->confirmed()
            ->whereBetween('date_vente_effective', [$startDate, $endDate])
            ->get();

        // Calculer les statistiques
        return [
            'date' => $date,
            'sales' => $sales,
            'sales_count' => $sales->count(),
            'revenue' => $sales->sum('prix_vente'),
            'profit' => $sales->sum('benefice'),
            'payments_received' => $sales->sum('amount_paid'),
            'average_sale' => $sales->count() > 0 ? $sales->avg('prix_vente') : 0,
            'average_profit' => $sales->count() > 0 ? $sales->avg('benefice') : 0,
            'by_type' => $sales->groupBy('sale_type')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'revenue' => $group->sum('prix_vente'),
                    'profit' => $group->sum('benefice'),
                ];
            }),
            'by_seller' => $sales->groupBy('sold_by')->map(function ($group) {
                return [
                    'seller' => $group->first()->seller->name ?? 'N/A',
                    'count' => $group->count(),
                    'revenue' => $group->sum('prix_vente'),
                    'profit' => $group->sum('benefice'),
                ];
            })->values(),
            'movements' => $this->stockService->getMovementStats($startDate, $endDate),
        ];
    }

    /**
     * Rapport complet pour une période (pour export PDF).
     */
    public function getFullPeriodReport(string $startDate, string $endDate): array
    {
        // PAGE 1: Ventes confirmées de la période
        $sales = Sale::with(['product.productModel', 'reseller', 'seller', 'tradeIn'])
            ->confirmed()
            ->whereBetween('date_vente_effective', [$startDate, $endDate])
            ->orderBy('created_at')
            ->get();

        // PAGE 2: Sorties vers revendeurs
        $resellerSales = Sale::with(['product.productModel', 'reseller'])
            ->whereNotNull('reseller_id')
            ->whereBetween('date_depot_revendeur', [$startDate, $endDate])
            ->orderBy('date_depot_revendeur')
            ->get();

        // PAGE 3: Stocks disponibles actuellement
        $stocks = Product::with('productModel')
            ->whereIn('state', [ProductState::DISPONIBLE->value, ProductState::REPARE->value])
            ->where('location', ProductLocation::BOUTIQUE->value)
            ->orderBy('product_model_id')
            ->get();

        // Statistiques globales
        $stats = [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('prix_vente'),
            'total_profit' => $sales->sum('benefice'),
            'total_reseller_sales' => $resellerSales->count(),
            'total_stock_available' => $stocks->count(),
            'total_stock_value' => $stocks->sum('prix_vente'),
        ];

        return [
            'sales' => $sales,
            'reseller_sales' => $resellerSales,
            'stocks' => $stocks,
            'stats' => $stats,
        ];
    }

    /**
     * Rapport hebdomadaire.
     */
    public function getWeeklyReport($startDate, $endDate, ?int $userId = null): array
    {
        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'sales' => $this->saleService->getSalesStats($startDate, $endDate),
            'movements' => $this->stockService->getMovementStats($startDate, $endDate),
            'daily_breakdown' => $this->getDailyBreakdown($startDate, $endDate),
        ];
    }

    /**
     * Rapport mensuel.
     */
    public function getMonthlyReport($year, $month, ?int $userId = null): array
    {
        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        return [
            'year' => $year,
            'month' => $month,
            'sales' => $this->saleService->getSalesStats($startDate, $endDate),
            'movements' => $this->stockService->getMovementStats($startDate, $endDate),
            'daily_breakdown' => $this->getDailyBreakdown($startDate, $endDate),
        ];
    }

    /**
     * Vue d'ensemble (dashboard admin).
     */
    public function getOverview(): array
    {
        return [
            'stock' => $this->getStockOverview(),
            'sales_today' => $this->saleService->getSalesStats(now()->startOfDay(), now()->endOfDay()),
            'sales_week' => $this->saleService->getSalesStats(now()->startOfWeek(), now()->endOfWeek()),
            'sales_month' => $this->saleService->getSalesStats(now()->startOfMonth(), now()->endOfMonth()),
            'top_selling_products' => $this->getTopSellingProducts(),
            'pending_reseller_sales' => $this->saleService->getPendingSales(),
        ];
    }

    /**
     * Rapport produits.
     */
    public function getProductsReport(): array
    {
        $productModels = ProductModel::withCount(['products', 'productsInStock', 'productsSold'])
            ->get();

        return [
            'total_models' => $productModels->count(),
            'total_products' => Product::count(),
            'products_in_stock' => Product::inStock()->count(),
            'products_sold' => Product::where('state', ProductState::VENDU->value)->count(),
            'products_chez_revendeur' => Product::where('location', ProductLocation::CHEZ_REVENDEUR->value)->count(),
            'products_en_reparation' => Product::where('location', ProductLocation::EN_REPARATION->value)->count(),
            'products_a_reparer' => Product::where('state', ProductState::A_REPARER->value)->count(),
            'low_stock_models' => $productModels->filter(fn ($m) => $m->isLowStock()),
            'by_category' => $this->getProductsByCategory(),
            'by_state' => $this->getProductsByState(),
            'by_location' => $this->getProductsByLocation(),
        ];
    }

    /**
     * Rapport revendeurs.
     */
    public function getResellersReport($startDate = null, $endDate = null): array
    {
        $resellers = Reseller::withCount(['sales', 'confirmedSales', 'pendingSales'])->get();

        return [
            'total_resellers' => $resellers->count(),
            'active_resellers' => $resellers->where('is_active', true)->count(),
            'resellers_with_pending' => $resellers->where('pending_sales_count', '>', 0)->count(),
            'total_products_at_resellers' => Product::where('location', ProductLocation::CHEZ_REVENDEUR->value)->count(),
            'resellers_details' => $resellers->map(function ($reseller) use ($startDate, $endDate) {
                $data = [
                    'reseller' => $reseller,
                    'total_sales' => $reseller->total_sales,
                    'total_benefice' => $reseller->total_benefice,
                    'products_count' => Product::where('location', ProductLocation::CHEZ_REVENDEUR->value)
                        ->whereHas('currentSale', fn ($q) => $q->where('reseller_id', $reseller->id))
                        ->count(),
                ];

                if ($startDate && $endDate) {
                    $data['period_sales'] = $reseller->salesAmountBetweenDates($startDate, $endDate);
                    $data['period_benefice'] = $reseller->beneficeBetweenDates($startDate, $endDate);
                }

                return $data;
            }),
        ];
    }

    /**
     * Rapport inventaire.
     */
    public function getInventoryReport(): array
    {
        $products = Product::with('productModel')->get();

        return [
            'total_value_cost' => $products->sum('prix_achat'),
            'total_value_sale' => $products->sum('prix_vente'),
            'potential_profit' => $products->sum(fn ($p) => $p->benefice_potentiel),
            'by_state' => $products->groupBy('state')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total_cost' => $group->sum('prix_achat'),
                    'total_sale_value' => $group->sum('prix_vente'),
                    'potential_profit' => $group->sum(fn ($p) => $p->benefice_potentiel),
                ];
            }),
            'by_location' => $products->groupBy('location')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total_cost' => $group->sum('prix_achat'),
                    'total_sale_value' => $group->sum('prix_vente'),
                    'potential_profit' => $group->sum(fn ($p) => $p->benefice_potentiel),
                ];
            }),
            'by_model' => $this->getInventoryByModel(),
            'stock_health' => $this->getStockHealth(),
        ];
    }

    /**
     * Rapport des réparations.
     */
    public function getRepairsReport($startDate = null, $endDate = null): array
    {
        $query = Product::where(function ($q) {
            $q->where('state', ProductState::A_REPARER->value)
                ->orWhere('state', ProductState::REPARE->value)
                ->orWhere('location', ProductLocation::EN_REPARATION->value);
        });

        if ($startDate && $endDate) {
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        }

        $products = $query->with('productModel')->get();

        return [
            'total_repairs' => $products->count(),
            'awaiting_repair' => $products->where('state', ProductState::A_REPARER->value)->count(),
            'in_repair' => $products->where('location', ProductLocation::EN_REPARATION->value)->count(),
            'repaired' => $products->where('state', ProductState::REPARE->value)->count(),
            'by_model' => $products->groupBy('product_model_id')->map(function ($group) {
                return [
                    'model' => $group->first()->productModel->name ?? 'N/A',
                    'count' => $group->count(),
                ];
            })->values(),
        ];
    }

    /**
     * Détail journalier.
     */
    private function getDailyBreakdown($startDate, $endDate, ?int $userId = null): array
    {
        $sales = Sale::confirmed()
            ->betweenDates($startDate, $endDate)
            ->get()
            ->groupBy(fn ($sale) => $sale->date_vente_effective->format('Y-m-d'));

        $breakdown = [];
        $current = new \DateTime($startDate);
        $end = new \DateTime($endDate);

        while ($current <= $end) {
            $dateKey = $current->format('Y-m-d');
            $daySales = $sales->get($dateKey, collect());

            $breakdown[$dateKey] = [
                'count' => $daySales->count(),
                'revenue' => $daySales->sum('prix_vente'),
                'profit' => $daySales->sum('benefice'),
            ];

            $current->modify('+1 day');
        }

        return $breakdown;
    }

    /**
     * Vue d'ensemble du stock.
     */
    private function getStockOverview(): array
    {
        return [
            'total_products' => Product::count(),
            'in_stock' => Product::inStock()->count(),
            'available_for_sale' => Product::where('state', ProductState::DISPONIBLE->value)
                ->where('location', ProductLocation::BOUTIQUE->value)
                ->count(),
            'sold' => Product::where('state', ProductState::VENDU->value)->count(),
            'chez_revendeur' => Product::where('location', ProductLocation::CHEZ_REVENDEUR->value)->count(),
            'chez_client' => Product::where('location', ProductLocation::CHEZ_CLIENT->value)->count(),
            'a_reparer' => Product::where('state', ProductState::A_REPARER->value)->count(),
            'en_reparation' => Product::where('location', ProductLocation::EN_REPARATION->value)->count(),
            'repare' => Product::where('state', ProductState::REPARE->value)->count(),
            'low_stock_count' => ProductModel::all()->filter(fn ($m) => $m->isLowStock())->count(),
        ];
    }

    /**
     * Produits les plus vendus.
     */
    private function getTopSellingProducts(int $limit = 10): array
    {
        return DB::table('sales')
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->join('product_models', 'products.product_model_id', '=', 'product_models.id')
            ->select(
                'product_models.id',
                'product_models.name',
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('SUM(sales.prix_vente) as total_revenue'),
                DB::raw('SUM(sales.benefice) as total_profit')
            )
            ->where('sales.is_confirmed', true)
            ->whereNull('sales.deleted_at')
            ->groupBy('product_models.id', 'product_models.name')
            ->orderByDesc('sales_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Produits par catégorie.
     */
    private function getProductsByCategory(): array
    {
        return DB::table('products')
            ->join('product_models', 'products.product_model_id', '=', 'product_models.id')
            ->select('product_models.category', DB::raw('COUNT(*) as count'))
            ->whereNull('products.deleted_at')
            ->groupBy('product_models.category')
            ->get()
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Produits par état.
     */
    private function getProductsByState(): array
    {
        return Product::select('state', DB::raw('COUNT(*) as count'))
            ->groupBy('state')
            ->get()
            ->mapWithKeys(function ($item) {
                $state = ProductState::from($item->state);

                return [$state->label() => $item->count];
            })
            ->toArray();
    }

    /**
     * Produits par localisation.
     */
    private function getProductsByLocation(): array
    {
        return Product::select('location', DB::raw('COUNT(*) as count'))
            ->groupBy('location')
            ->get()
            ->mapWithKeys(function ($item) {
                $location = ProductLocation::from($item->location);

                return [$location->label() => $item->count];
            })
            ->toArray();
    }

    /**
     * Inventaire par modèle.
     */
    private function getInventoryByModel(): array
    {
        return ProductModel::withCount('productsInStock')
            ->with(['productsInStock' => function ($query) {
                $query->select('product_model_id', DB::raw('SUM(prix_achat) as total_cost'), DB::raw('SUM(prix_vente) as total_sale_value'))
                    ->groupBy('product_model_id');
            }])
            ->get()
            ->map(function ($model) {
                return [
                    'model' => $model->name,
                    'quantity' => $model->products_in_stock_count,
                    'total_cost' => $model->productsInStock->sum('prix_achat'),
                    'total_sale_value' => $model->productsInStock->sum('prix_vente'),
                    'potential_profit' => $model->productsInStock->sum('prix_vente') - $model->productsInStock->sum('prix_achat'),
                ];
            })
            ->toArray();
    }

    /**
     * Santé du stock (analyse avancée).
     */
    private function getStockHealth(): array
    {
        $products = Product::with('productModel')->get();

        return [
            'available_for_sale' => $products->where('state', ProductState::DISPONIBLE->value)
                ->where('location', ProductLocation::BOUTIQUE->value)
                ->count(),
            'needs_attention' => $products->whereIn('state', [
                ProductState::A_REPARER->value,
                ProductState::RETOUR->value,
            ])->count(),
            'out_for_repair' => $products->where('location', ProductLocation::EN_REPARATION->value)->count(),
            'with_resellers' => $products->where('location', ProductLocation::CHEZ_REVENDEUR->value)->count(),
            'turnover_rate' => $this->calculateTurnoverRate(),
        ];
    }

    /**
     * Calculer le taux de rotation du stock.
     */
    private function calculateTurnoverRate(): float
    {
        $soldLast30Days = Product::where('state', ProductState::VENDU->value)
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

        $averageStock = Product::whereIn('state', [
            ProductState::DISPONIBLE->value,
            ProductState::REPARE->value,
        ])->count();

        return $averageStock > 0 ? round(($soldLast30Days / $averageStock) * 100, 2) : 0;
    }
}
