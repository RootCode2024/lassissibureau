<?php

use App\Http\Controllers\CustomerReturnController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductModelController;
use App\Http\Controllers\ResellerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze default)
    Route::view('profile', 'profile')->name('profile');

    /*
    |--------------------------------------------------------------------------
    | Product Models Management (Admin only)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('product-models')->name('product-models.')->group(function () {
        Route::get('/', [ProductModelController::class, 'index'])->name('index');
        Route::get('/create', [ProductModelController::class, 'create'])->name('create');
        Route::post('/', [ProductModelController::class, 'store'])->name('store');
        Route::get('/{productModel}', [ProductModelController::class, 'show'])->name('show');
        Route::get('/{productModel}/edit', [ProductModelController::class, 'edit'])->name('edit');
        Route::put('/{productModel}', [ProductModelController::class, 'update'])->name('update');
        Route::delete('/{productModel}', [ProductModelController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Products Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('products')->name('products.')->group(function () {
        // Lecture (tous)
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');

        // Recherche par IMEI
        Route::get('/search/imei', [ProductController::class, 'searchByImei'])->name('search.imei');

        // Création et modification (Admin only)
        Route::middleware('admin')->group(function () {
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');

            // Changement de prix
            Route::put('/{product}/prices', [ProductController::class, 'updatePrices'])->name('update-prices');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Sales Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('sales')->name('sales.')->group(function () {
        // Lecture (tous, avec filtrage selon rôle)
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');

        // Création (Vendeurs et Admin)
        Route::middleware('vendeur')->group(function () {
            Route::get('/create', [SaleController::class, 'create'])->name('create');
            Route::post('/', [SaleController::class, 'store'])->name('store');
        });

        // Confirmation ventes revendeurs (Admin only)
        Route::middleware('admin')->group(function () {
            Route::post('/{sale}/confirm', [SaleController::class, 'confirm'])->name('confirm');
            Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Stock Movements
    |--------------------------------------------------------------------------
    */
    Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
        // Lecture (tous)
        Route::get('/', [StockMovementController::class, 'index'])->name('index');
        Route::get('/{stockMovement}', [StockMovementController::class, 'show'])->name('show');

        // Création (selon type de mouvement)
        Route::post('/', [StockMovementController::class, 'store'])->name('store');

        // Mouvements spécifiques admin
        Route::middleware('admin')->group(function () {
            Route::get('/create/reception', [StockMovementController::class, 'createReception'])->name('create.reception');
            Route::get('/create/adjustment', [StockMovementController::class, 'createAdjustment'])->name('create.adjustment');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Resellers Management (Admin only)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('resellers')->name('resellers.')->group(function () {
        Route::get('/', [ResellerController::class, 'index'])->name('index');
        Route::get('/create', [ResellerController::class, 'create'])->name('create');
        Route::post('/', [ResellerController::class, 'store'])->name('store');
        Route::get('/{reseller}', [ResellerController::class, 'show'])->name('show');
        Route::get('/{reseller}/edit', [ResellerController::class, 'edit'])->name('edit');
        Route::put('/{reseller}', [ResellerController::class, 'update'])->name('update');
        Route::delete('/{reseller}', [ResellerController::class, 'destroy'])->name('destroy');

        // Statistiques revendeur
        Route::get('/{reseller}/statistics', [ResellerController::class, 'statistics'])->name('statistics');
    });

    /*
    |--------------------------------------------------------------------------
    | Customer Returns (Admin only)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('returns')->name('returns.')->group(function () {
        Route::get('/', [CustomerReturnController::class, 'index'])->name('index');
        Route::get('/create', [CustomerReturnController::class, 'create'])->name('create');
        Route::post('/', [CustomerReturnController::class, 'store'])->name('store');
        Route::get('/{customerReturn}', [CustomerReturnController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Reports & Statistics
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        // Rapports de base (Vendeurs peuvent voir leurs propres stats)
        Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('/weekly', [ReportController::class, 'weekly'])->name('weekly');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');

        // Rapports avancés (Admin only)
        Route::middleware('admin')->group(function () {
            Route::get('/overview', [ReportController::class, 'overview'])->name('overview');
            Route::get('/products', [ReportController::class, 'products'])->name('products');
            Route::get('/resellers', [ReportController::class, 'resellers'])->name('resellers');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');

            // Exports
            Route::post('/export/sales', [ReportController::class, 'exportSales'])->name('export.sales');
            Route::post('/export/inventory', [ReportController::class, 'exportInventory'])->name('export.inventory');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Users Management (Admin only)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

        // Gestion des rôles
        Route::put('/{user}/role', [UserController::class, 'updateRole'])->name('update-role');
    });
});

/*
|--------------------------------------------------------------------------
| Auth Routes (from Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
