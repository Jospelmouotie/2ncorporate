<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // Import obligatoire

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Force le HTTPS dès qu'on est sur Render
        if (str_contains(request()->getHost(), 'onrender.com') || config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
