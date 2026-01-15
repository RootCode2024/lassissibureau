<?php

namespace App\Livewire;

use App\Models\Sale;
use App\Models\Product;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardOptimized extends Component
{
    public $period = '30';
    public $chartData = [];
    public $chartLabels = [];

    // Pour le rafraîchissement automatique
    public $refreshInterval = 30000; // 30 secondes

    protected $listeners = ['refreshDashboard' => '$refresh'];

    public function mount()
    {
        $this->loadChartData();
    }

    public function updatedPeriod()
    {
        $this->loadChartData();
        $this->dispatch('chartDataUpdated');
    }

    /**
     * Charge les données du graphique avec cache intelligent
     */
    public function loadChartData()
    {
        $days = (int) $this->period;
        $cacheKey = "dashboard_chart_{$days}_" . auth()->id();

        // Cache de 5 minutes pour les données du graphique
        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($days) {
            return $this->fetchChartData($days);
        });

        $this->chartData = $data['chartData'];
        $this->chartLabels = $data['chartLabels'];
    }

    /**
     * Récupère les données brutes de la base de données
     */
    private function fetchChartData($days)
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Requête optimisée - Ventes confirmées uniquement
        $salesByDay = Sale::whereBetween('date_vente_effective', [$startDate, $endDate])
            ->where('is_confirmed', true)
            ->select(
                DB::raw('DATE(date_vente_effective) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(prix_vente) as revenue'),
                DB::raw('SUM(benefice) as profit')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $salesData = [];
        $revenueData = [];
        $profitData = [];

        // Générer tous les jours de la période
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateKey = $date->format('Y-m-d');

            // Format d'affichage adapté à la période
            if ($days <= 7) {
                $chartLabels[] = $date->locale('fr')->isoFormat('ddd D');
            } elseif ($days <= 30) {
                $chartLabels[] = $date->format('d/m');
            } else {
                $chartLabels[] = $date->format('d/m');
            }

            $dayData = $salesByDay->get($dateKey);

            $salesData[] = $dayData ? (int) $dayData->count : 0;
            $revenueData[] = $dayData ? (float) $dayData->revenue : 0;
            $profitData[] = $dayData ? (float) ($dayData->profit ?? 0) : 0;
        }

        return [
            'chartData' => [
                'sales' => $salesData,
                'revenue' => $revenueData,
                'profit' => $profitData,
            ],
            'chartLabels' => $chartLabels,
        ];
    }

    /**
     * Récupère les statistiques avec cache
     */
    private function getStats()
    {
        return Cache::remember('dashboard_stats_' . auth()->id(), now()->addMinutes(5), function () {
            return [
                'sales_today' => Sale::whereDate('date_vente_effective', today())
                    ->where('is_confirmed', true)
                    ->count(),

                'revenue_today' => Sale::whereDate('date_vente_effective', today())
                    ->where('is_confirmed', true)
                    ->sum('prix_vente') ?? 0,

                'profit_today' => Sale::whereDate('date_vente_effective', today())
                    ->where('is_confirmed', true)
                    ->sum('benefice') ?? 0,

                'sales_month' => Sale::whereMonth('date_vente_effective', now()->month)
                    ->whereYear('date_vente_effective', now()->year)
                    ->where('is_confirmed', true)
                    ->count(),

                'revenue_month' => Sale::whereMonth('date_vente_effective', now()->month)
                    ->whereYear('date_vente_effective', now()->year)
                    ->where('is_confirmed', true)
                    ->sum('prix_vente') ?? 0,

                'profit_month' => Sale::whereMonth('date_vente_effective', now()->month)
                    ->whereYear('date_vente_effective', now()->year)
                    ->where('is_confirmed', true)
                    ->sum('benefice') ?? 0,

                'total_products_in_stock' => Product::count(),

                'products_low_stock' => Product::whereColumn('stock_quantity', '<=', 'stock_minimum')
                    ->count(),

                'products_chez_revendeur' => Sale::where('is_confirmed', false)
                    ->whereNotNull('reseller_id')
                    ->count(),
            ];
        });
    }

    /**
     * Rafraîchit les données du dashboard
     */
    public function refresh()
    {
        // Vider le cache
        Cache::forget('dashboard_stats_' . auth()->id());
        Cache::forget("dashboard_chart_{$this->period}_" . auth()->id());

        $this->loadChartData();
        $this->dispatch('chartDataUpdated');

        session()->flash('message', 'Dashboard actualisé !');
    }

    public function render()
    {
        $stats = $this->getStats();

        // Produits en stock bas avec cache
        $lowStockProducts = Cache::remember('low_stock_products', now()->addMinutes(10), function () {
            return Product::with('productModel')
                ->whereColumn('stock_quantity', '<=', 'stock_minimum')
                ->limit(10)
                ->get();
        });

        // Ventes récentes confirmées
        $recentSales = Sale::with(['product.productModel', 'seller'])
            ->where('is_confirmed', true)
            ->latest('date_vente_effective')
            ->limit(8)
            ->get();

        // Ventes en attente
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

        return view('livewire.dashboard-optimized', compact(
            'stats',
            'lowStockProducts',
            'recentSales',
            'pendingSales'
        ));
    }
}

// ----------------------------------------------------------
// Migration pour optimiser les index
// ----------------------------------------------------------

/*
php artisan make:migration optimize_sales_table_indexes

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ces index existent déjà dans votre migration mais on s'assure qu'ils sont bien là
        // Si besoin d'ajouter d'autres index optimisés
        Schema::table('sales', function (Blueprint $table) {
            // Index composite pour les requêtes fréquentes du dashboard
            if (!$this->hasIndex('sales', 'sales_is_confirmed_date_vente_effective_index')) {
                $table->index(['is_confirmed', 'date_vente_effective']);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            // Index pour les alertes de stock
            if (!$this->hasIndex('products', 'products_stock_quantity_stock_minimum_index')) {
                $table->index(['stock_quantity', 'stock_minimum']);
            }
        });
    }

    private function hasIndex($table, $indexName): bool
    {
        $indexes = DB::select("SELECT indexname FROM pg_indexes WHERE tablename = ? AND indexname = ?", [$table, $indexName]);
        return count($indexes) > 0;
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['is_confirmed', 'date_vente_effective']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['stock_quantity', 'stock_minimum']);
        });
    }
};
*/

// ----------------------------------------------------------
// Service pour gérer le cache du dashboard
// app/Services/DashboardCacheService.php
// ----------------------------------------------------------

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DashboardCacheService
{
    /**
     * Vide tout le cache du dashboard
     */
    public static function clearAll()
    {
        $pattern = 'dashboard_*';
        // Pour Redis/Memcached, utilisez Cache::tags(['dashboard'])->flush();
        Cache::flush(); // ou implémentez une logique de pattern matching
    }

    /**
     * Vide le cache pour un utilisateur spécifique
     */
    public static function clearForUser($userId)
    {
        Cache::forget("dashboard_stats_{$userId}");
        Cache::forget("dashboard_chart_7_{$userId}");
        Cache::forget("dashboard_chart_30_{$userId}");
        Cache::forget("dashboard_chart_90_{$userId}");
    }

    /**
     * Vide le cache des produits en stock bas
     */
    public static function clearLowStock()
    {
        Cache::forget('low_stock_products');
    }
}

// ----------------------------------------------------------
// Observer pour vider le cache automatiquement
// app/Observers/SaleObserver.php
// ----------------------------------------------------------

namespace App\Observers;

use App\Models\Sale;
use App\Services\DashboardCacheService;

class SaleObserver
{
    /**
     * Appelé après la création d'une vente
     */
    public function created(Sale $sale)
    {
        $this->clearCaches($sale);
    }

    /**
     * Appelé après la mise à jour d'une vente
     */
    public function updated(Sale $sale)
    {
        $this->clearCaches($sale);
    }

    /**
     * Appelé après la suppression d'une vente
     */
    public function deleted(Sale $sale)
    {
        $this->clearCaches($sale);
    }

    /**
     * Vide les caches concernés
     */
    private function clearCaches(Sale $sale)
    {
        // Vider le cache du vendeur
        DashboardCacheService::clearForUser($sale->sold_by);

        // Si admin, vider aussi les caches globaux
        if ($sale->seller && $sale->seller->isAdmin()) {
            DashboardCacheService::clearAll();
        }
    }
}

// ----------------------------------------------------------
// Enregistrer l'observer dans AppServiceProvider
// app/Providers/AppServiceProvider.php
// ----------------------------------------------------------

/*
use App\Models\Sale;
use App\Observers\SaleObserver;

public function boot(): void
{
    Sale::observe(SaleObserver::class);
}
*/

