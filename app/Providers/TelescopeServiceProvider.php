<?php

// ============================================
// App\Providers\TelescopeServiceProvider.php
// ============================================

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Activer le mode sombre (optionnel)
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        // ✅ Filtrer ce qui est enregistré
        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            // En local: tout enregistrer
            if ($isLocal) {
                return true;
            }

            // En production: uniquement les erreurs critiques
            return $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });

        // ✅ Limiter la taille de la base de données Telescope
        Telescope::tag(function (IncomingEntry $entry) {
            // Ajouter des tags personnalisés pour faciliter le filtrage
            $tags = [];

            if ($entry->type === 'request') {
                $tags[] = 'route:' . $entry->content['uri'] ?? 'unknown';
            }

            if ($this->app->environment('production')) {
                $tags[] = 'production';
            }

            return $tags;
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        // ✅ Masquer les données sensibles
        Telescope::hideRequestParameters([
            '_token',
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            'card_number',
            'cvv',
            'ssn',
        ]);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
            'authorization',
            'php-auth-pw',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return $user->hasRole('admin') && 
                   in_array($user->email, [
                       'admin@lassissi.com',
                       // Ajoutez d'autres admins si nécessaire
                   ]);
        });
    }
}