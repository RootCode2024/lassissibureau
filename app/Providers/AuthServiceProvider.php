<?php

namespace App\Providers;

use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use App\Models\TradeIn;
use App\Models\Reseller;
use App\Models\ProductModel;
use App\Policies\SalePolicy;
use App\Policies\UserPolicy;
use App\Policies\ProductPolicy;
use App\Policies\TradeInPolicy;
use App\Policies\ResellerPolicy;
use App\Policies\ProductModelPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ProductModel::class => ProductModelPolicy::class,
        Product::class => ProductPolicy::class,
        Reseller::class => ResellerPolicy::class,
        Sale::class => SalePolicy::class,
        User::class => UserPolicy::class,
        TradeIn::class => TradeInPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
