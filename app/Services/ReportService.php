<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Reseller;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function __construct(
        private SaleService $saleService,
        private StockService $stockService
    ) {}

    /**
     * Rapport quotidien.
     */
    public function getDailyReport($date, ?int $userId = null): array
    {
        $startDate = $date . ' 00:00:00';
        $endDate = $date . ' 23:59:59';

        return [
            'date' => $date,
            'sales' => $this->saleService->getSalesStats($startDate, $endDate, $userId),
            'movements' => $this->stockService->getMovementStats($startDate, $endDate),
        ];
    }

    /**
     * Rapport hebdomadaire.
     */
    public function getWeeklyReport($startDate, $endDate, ?int $userId = null): array
    {
        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'sales' => $this->saleService->getSalesStats($startDate, $endDate, $userId),
            'movements' => $this->stockService->getMovementStats($startDate, $endDate),
            'daily_breakdown' => $this->getDailyBreakdown($startDate, $endDate, $userId),
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
            'sales' => $this->saleService->getSalesStats($startDate, $endDate, $userId),
            'movements' => $this->stockService->getMovementStats($startDate, $endDate),
            'daily_breakdown' => $this->getDailyBreakdown($startDate, $endDate, $userId),
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
            'products_sold' => Product::where('status', ProductStatus::VENDU)->count(),
            'products_chez_revendeur' => Product::chezRevendeur()->count(),
            'low_stock_models' => $productModels->filter(fn($m) => $m->isLowStock()),
            'by_category' => $this->getProductsByCategory(),
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
            'resellers_details' => $resellers->map(function ($reseller) use ($startDate, $endDate) {
                $data = [
                    'reseller' => $reseller,
                    'total_sales' => $reseller->total_sales,
                    'total_benefice' => $reseller->total_benefice,
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
            'potential_profit' => $products->sum(fn($p) => $p->benefice_potentiel),
            'by_status' => $products->groupBy('status')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total_cost' => $group->sum('prix_achat'),
                    'total_sale_value' => $group->sum('prix_vente'),
                ];
            }),
            'by_model' => $this->getInventoryByModel(),
        ];
    }

    /**
     * Détail journalier.
     */
    private function getDailyBreakdown($startDate, $endDate, ?int $userId = null): array
    {
        $sales = Sale::confirmed()
            ->betweenDates($startDate, $endDate)
            ->when($userId, fn($q) => $q->where('sold_by', $userId))
            ->get()
            ->groupBy(fn($sale) => $sale->date_vente_effective->format('Y-m-d'));

        $breakdown = [];
        $current = new \DateTime($startDate);
        $end = new \DateTime($endDate);

        while ($current <= $end) {
            $dateKey = $current->format('Y-m-d');
            $daySales = $sales->get($dateKey, collect());

            $breakdown[$dateKey] = [
                'count' => $daySales->count(),
                'revenue' => $daySales->sum('prix_vente'),
                'profit' => $daySales->sum(fn($s) => $s->benefice),
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
            'sold' => Product::where('status', ProductStatus::VENDU)->count(),
            'chez_revendeur' => Product::chezRevendeur()->count(),
            'a_reparer' => Product::aReparer()->count(),
            'low_stock_count' => ProductModel::all()->filter(fn($m) => $m->isLowStock())->count(),
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
                DB::raw('SUM(sales.prix_vente) as total_revenue')
            )
            ->where('sales.is_confirmed', true)
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
            ->groupBy('product_models.category')
            ->get()
            ->pluck('count', 'category')
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
                ];
            })
            ->toArray();
    }
}
