<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Reseller;
use App\Models\Sale;
use App\Models\User;
use App\Policies\ProductModelPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ResellerPolicy;
use App\Policies\SalePolicy;
use App\Policies\UserPolicy;
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
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
