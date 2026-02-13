<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Authorize Super Admins for all actions
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        // Global HTTP client configuration for ngrok compatibility
        Http::globalRequestMiddleware(function ($request) {
            return $request->withHeader('ngrok-skip-browser-warning', 'true')
                ->withHeader('User-Agent', 'ClinicFlow-App/1.0');
        });

        if (config('app.env') !== 'local' || str_contains(config('app.url'), 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
