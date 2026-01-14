<?php

use Illuminate\Foundation\Application;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsAdmin; // N'oubliez pas l'import

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // 1. Définir un alias (Recommandé)
        // Cela vous permet d'utiliser ->middleware('admin') dans vos routes
        $middleware->alias([
            'admin' => IsAdmin::class,
        ]);

        // 🌍 CORS global
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        // 🔐 Groupe API public
        $middleware->group('api-public', [
            SubstituteBindings::class,
            'throttle:api',
        ]);

        // 🔐 Groupe API protégé
        $middleware->group('api-auth', [
            EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            // Optionnel : Vous pourriez l'ajouter ici si TOUTES les routes
            // de ce groupe doivent être admin, mais c'est rarement le cas.
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
