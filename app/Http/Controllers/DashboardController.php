<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Statistiques générales
        $stats = [
            // Stock
            'total_products_in_stock' => Product::inStock()->count(),
            'products_low_stock' => $this->getProductsLowStock(),
            'products_chez_revendeur' => Product::chezRevendeur()->count(),
            'products_a_reparer' => Product::aReparer()->count(),

            // Ventes du jour
            'sales_today' => $this->getSalesToday($user),
            'revenue_today' => $this->getRevenueToday($user),
            'profit_today' => $this->getProfitToday($user),

            // Ventes du mois
            'sales_month' => $this->getSalesMonth($user),
            'revenue_month' => $this->getRevenueMonth($user),
            'profit_month' => $this->getProfitMonth($user),
        ];

        // Produits en stock bas (alertes)
        $lowStockProducts = Product::inStock()
            ->with('productModel')
            ->get()
            ->filter(function ($product) {
                return $product->productModel->isLowStock();
            })
            ->take(5);

        // Ventes récentes
        $recentSales = Sale::with(['product.productModel', 'seller', 'reseller'])
            ->when($user->isVendeur(), function ($query) use ($user) {
                return $query->where('sold_by', $user->id);
            })
            ->latest()
            ->take(10)
            ->get();

        // Mouvements de stock récents
        $recentMovements = StockMovement::with(['product.productModel', 'user'])
            ->latest()
            ->take(10)
            ->get();

        // Ventes en attente (revendeurs)
        $pendingSales = null;
        if ($user->isAdmin()) {
            $pendingSales = Sale::with(['product.productModel', 'reseller'])
                ->pending()
                ->latest()
                ->get();
        }

        return view('dashboard', compact(
            'stats',
            'lowStockProducts',
            'recentSales',
            'recentMovements',
            'pendingSales'
        ));
    }

    /**
     * Get products with low stock count.
     */
    private function getProductsLowStock(): int
    {
        return DB::table('products')
            ->join('product_models', 'products.product_model_id', '=', 'product_models.id')
            ->whereIn('products.status', [
                ProductStatus::STOCK_BOUTIQUE->value,
                ProductStatus::REPARE->value,
            ])
            ->whereNull('products.deleted_at')
            ->select('product_models.id', DB::raw('COUNT(products.id) as stock_count'), 'product_models.stock_minimum')
            ->groupBy('product_models.id', 'product_models.stock_minimum')
            ->havingRaw('COUNT(products.id) <= product_models.stock_minimum')
            ->count();
    }

    /**
     * Get sales count for today.
     */
    private function getSalesToday($user): int
    {
        return Sale::today()
            ->confirmed()
            ->when($user->isVendeur(), fn($q) => $q->where('sold_by', $user->id))
            ->count();
    }

    /**
     * Get revenue for today.
     */
    private function getRevenueToday($user): float
    {
        return Sale::today()
            ->confirmed()
            ->when($user->isVendeur(), fn($q) => $q->where('sold_by', $user->id))
            ->sum('prix_vente');
    }

    /**
     * Get profit for today.
     */
    private function getProfitToday($user): float
    {
        return Sale::today()
            ->confirmed()
            ->when($user->isVendeur(), fn($q) => $q->where('sold_by', $user->id))
            ->get()
            ->sum(fn($sale) => $sale->benefice);
    }

    /**
     * Get sales count for this month.
     */
    private function getSalesMonth($user): int
    {
        return Sale::thisMonth()
            ->confirmed()
            ->when($user->isVendeur(), fn($q) => $q->where('sold_by', $user->id))
            ->count();
    }

    /**
     * Get revenue for this month.
     */
    private function getRevenueMonth($user): float
    {
        return Sale::thisMonth()
            ->confirmed()
            ->when($user->isVendeur(), fn($q) => $q->where('sold_by', $user->id))
            ->sum('prix_vente');
    }

    /**
     * Get profit for this month.
     */
    private function getProfitMonth($user): float
    {
        return Sale::thisMonth()
            ->confirmed()
            ->when($user->isVendeur(), fn($q) => $q->where('sold_by', $user->id))
            ->get()
            ->sum(fn($sale) => $sale->benefice);
    }
}
