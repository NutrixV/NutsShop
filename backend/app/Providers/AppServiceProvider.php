<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register UrlService as a singleton
        $this->app->singleton('url.service', function ($app) {
            return new \App\Services\UrlService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Override storage_path helper to use our proxy to prevent CORS issues
        $urlService = $this->app->make('url.service');
        
        // Use secure protocol in production
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
        
        // Override the asset url generator
        \URL::macro('storage', function ($path) use ($urlService) {
            return $urlService->storageUrl($path);
        });
        
        // Override the normal url generator
        \URL::macro('normalize', function ($url) use ($urlService) {
            return $urlService->normalizeUrl($url);
        });
    }
} 