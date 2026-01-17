<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Telescope;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('production')) {
            Telescope::stopRecording();
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('fr');

        // Enregistrer l'observer pour invalider le cache des conditions
        Product::observe(ProductObserver::class);
    }
}
