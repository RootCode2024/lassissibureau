<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\Telescope::class)) {
            // Telescope est disponible en local, on peut l'utiliser
        } elseif ($this->app->environment('production') && class_exists(\Laravel\Telescope\Telescope::class)) {
            \Laravel\Telescope\Telescope::stopRecording();
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