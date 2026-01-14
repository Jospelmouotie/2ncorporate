<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- CETTE LIGNE EST INDISPENSABLE

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
     */public function boot(): void
{
    \Illuminate\Pagination\Paginator::useBootstrapFive();

    // Si l'URL contient "onrender.com", on force le HTTPS quoi qu'il arrive
    if (str_contains(request()->getHost(), 'onrender.com')) {
        URL::forceScheme('https');
    }
}
}
