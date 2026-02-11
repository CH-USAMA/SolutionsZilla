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
        // Global HTTP client configuration for ngrok compatibility
        Http::globalRequestMiddleware(function ($request) {
            return $request->withHeader('ngrok-skip-browser-warning', 'true')
                ->withHeader('User-Agent', 'ClinicFlow-App/1.0');
        });
    }
}
