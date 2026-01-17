<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $period = '30'; // 7, 30, 90 jours

    public $chartData = [];

    public $chartLabels = [];

    public function mount()
    {
        $this->loadChartData();
    }

    public function updatedPeriod()
    {
        $this->loadChartData();
        $this->dispatch('chartDataUpdated');
    }

    public function loadChartData()
    {
        $days = (int) $this->period;
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Récupérer les ventes confirmées par jour
        $salesByDay = Sale::whereBetween('date_vente_effective', [$startDate, $endDate])
            ->where('is_confirmed', true)
            ->select(
                DB::raw('DATE(date_vente_effective) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(prix_vente) as revenue'),
                DB::raw('SUM(prix_vente - prix_achat_produit) as profit')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Préparer les données pour le chart
        $this->chartLabels = [];
        $salesData = [];
        $revenueData = [];
        $profitData = [];

        // Créer un tableau de tous les jours dans la période
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateKey = $date->format('Y-m-d');

            // Format d'affichage adapté à la période
            if ($days <= 7) {
                $this->chartLabels[] = $date->locale('fr')->isoFormat('ddd D');
            } else {
                $this->chartLabels[] = $date->format('d/m');
            }

            // Trouver les données pour cette date
            $dayData = $salesByDay->get($dateKey);

            $salesData[] = $dayData ? (int) $dayData->count : 0;
            $revenueData[] = $dayData ? (float) $dayData->revenue : 0;
            $profitData[] = $dayData ? (float) ($dayData->profit ?? 0) : 0;
        }

        $this->chartData = [
            'sales' => $salesData,
            'revenue' => $revenueData,
            'profit' => $profitData,
        ];
    }

    public function render()
    {
        // Stats générales - Ventes confirmées uniquement
        // Récupérer les ventes du jour pour calculer le bénéfice (attribut calculé)
        $salesToday = Sale::whereDate('date_vente_effective', today())
            ->where('is_confirmed', true)
            ->get();

        // Récupérer les ventes du mois pour calculer le bénéfice
        $salesMonth = Sale::whereMonth('date_vente_effective', now()->month)
            ->whereYear('date_vente_effective', now()->year)
            ->where('is_confirmed', true)
            ->get();

        $stats = [
            'sales_today' => $salesToday->count(),
            'revenue_today' => $salesToday->sum('prix_vente'),
            'profit_today' => $salesToday->sum(fn ($sale) => $sale->benefice),

            'sales_month' => $salesMonth->count(),
            'revenue_month' => $salesMonth->sum('prix_vente'),
            'profit_month' => $salesMonth->sum(fn ($sale) => $sale->benefice),

            'total_products_in_stock' => Product::whereIn('location', ['boutique', 'en_reparation'])
                ->count(),

            'products_low_stock' => $this->getLowStockCountOptimized(),

            'products_chez_revendeur' => Product::where('location', 'chez_revendeur')->where('state', 'disponible')
                ->count(),
        ];

        // Produits en stock bas (modèles) - VERSION OPTIMISÉE
        $lowStockProducts = $this->getLowStockProductsOptimized();

        // Ventes récentes confirmées
        $recentSales = Sale::with(['product.productModel', 'seller'])
            ->where('is_confirmed', true)
            ->latest('date_vente_effective')
            ->limit(8)
            ->get();

        // Ventes en attente (chez revendeurs)
        $pendingSales = null;
        if (auth()->user()->isAdmin()) {
            $pendingSales = Sale::with(['product.productModel', 'reseller'])
                ->where('is_confirmed', false)
                ->whereNotNull('reseller_id')
                ->whereNotNull('date_depot_revendeur')
                ->latest('date_depot_revendeur')
                ->limit(10)
                ->get();
        }

        return view('livewire.dashboard', compact(
            'stats',
            'lowStockProducts',
            'recentSales',
            'pendingSales'
        ))->layout('layouts.app')->title('Tableau de bord');
    }

    /**
     * VERSION OPTIMISÉE avec CTE - Obtenir le nombre de modèles en stock bas
     * Une seule requête ultra-performante
     */
    private function getLowStockCountOptimized(): int
    {
        $sql = "
            WITH stock_counts AS (
                SELECT 
                    pm.id,
                    pm.stock_minimum,
                    COUNT(p.id) as current_stock
                FROM product_models pm
                LEFT JOIN products p ON p.product_model_id = pm.id 
                    AND p.location IN ('boutique', 'en_reparation')
                    AND p.deleted_at IS NULL
                WHERE pm.is_active = true 
                    AND pm.deleted_at IS NULL
                GROUP BY pm.id, pm.stock_minimum
            )
            SELECT COUNT(*) as count
            FROM stock_counts
            WHERE current_stock <= stock_minimum
        ";

        $result = DB::select($sql);

        return $result[0]->count ?? 0;
    }

    /**
     * VERSION OPTIMISÉE avec CTE - Obtenir les modèles en stock bas
     * Une seule requête ultra-performante
     */
    private function getLowStockProductsOptimized()
    {
        $sql = "
            WITH stock_counts AS (
                SELECT 
                    pm.*,
                    COUNT(p.id) as computed_stock_quantity
                FROM product_models pm
                LEFT JOIN products p ON p.product_model_id = pm.id 
                    AND p.location IN ('boutique', 'en_reparation')
                    AND p.deleted_at IS NULL
                WHERE pm.is_active = true 
                    AND pm.deleted_at IS NULL
                GROUP BY pm.id
            )
            SELECT *
            FROM stock_counts
            WHERE computed_stock_quantity <= stock_minimum
            ORDER BY computed_stock_quantity ASC
            LIMIT 10
        ";

        $results = DB::select($sql);

        // Convertir les résultats en collection de modèles ProductModel
        return collect($results)->map(function ($row) {
            $model = new ProductModel;
            $model->exists = true;
            $model->setRawAttributes((array) $row, true);

            return $model;
        });
    }

    /**
     * VERSION ALTERNATIVE - Si vous préférez Eloquent pur
     * Utilise fromSub() pour une sous-requête
     */
    private function getLowStockProductsEloquent()
    {
        // Créer la sous-requête
        $subQuery = ProductModel::query()
            ->select('product_models.*')
            ->selectRaw('(
                SELECT COUNT(*) 
                FROM products 
                WHERE products.product_model_id = product_models.id 
                AND products.location IN (?, ?)
                AND products.deleted_at IS NULL
            ) as computed_stock_quantity', ['boutique', 'en_reparation'])
            ->where('is_active', true);

        // Utiliser la sous-requête avec fromSub
        return DB::table(DB::raw("({$subQuery->toSql()}) as sub"))
            ->mergeBindings($subQuery->getQuery())
            ->whereRaw('computed_stock_quantity <= stock_minimum')
            ->orderBy('computed_stock_quantity')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $model = new ProductModel;
                $model->exists = true;
                $model->setRawAttributes((array) $row, true);

                return $model;
            });
    }
}
