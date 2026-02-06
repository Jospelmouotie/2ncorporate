<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Import des Controllers
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AdvertisementController as ApiAdController;

// Import des Controllers Admin
use App\Http\Controllers\Admin\ProductManagementController;
use App\Http\Controllers\Admin\PromoManagemetnController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AdvertisementController as AdminAdController;

/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES (Ouvertes à tous les visiteurs)
|--------------------------------------------------------------------------
*/

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/sale', [ProductController::class, 'onSale']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/promotions', [PromoController::class, 'index']);
Route::get('/categories', [CategoryManagementController::class, 'index']);
Route::get('/ads', [ApiAdController::class, 'index']);
Route::get('/search', [SearchController::class, 'search']);

// CONNEXION - Important: utilise le middleware 'web' pour la session
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (auth()->attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        return response()->json([
            'user' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'is_admin' => (bool) auth()->user()->is_admin,
            ]
        ]);
    }

    return response()->json([
        'message' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.'
    ], 401);
})->middleware('web'); // ← Utilise le middleware web pour la session

// DÉCONNEXION
Route::post('/logout', function (Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json(['message' => 'Déconnexion réussie']);
})->middleware(['auth', 'web']); // ← Utilise les middlewares auth et web

// UTILISATEUR CONNECTÉ
Route::middleware(['auth', 'web'])->get('/user', function (Request $request) {
    return response()->json([
        'id' => $request->user()->id,
        'name' => $request->user()->name,
        'email' => $request->user()->email,
        'is_admin' => (bool) $request->user()->is_admin,
    ]);
});

// INSCRIPTION (AuthController)
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| ROUTES CLIENTS CONNECTÉS (Panier, Wishlist, Commandes)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'web'])->group(function () {
    Route::post('/promotions/check', [PromoController::class, 'check']);

    // Panier
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/add', [WishlistController::class, 'add']);
    Route::delete('/wishlist/remove/{productId}', [WishlistController::class, 'remove']);

    // Commandes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // Avis
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| ROUTES ADMINISTRATEUR (Uniquement is_admin = true)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'web', 'admin'])->prefix('admin')->group(function () {
    // Dashboard & Stats
    Route::get('/dashboard/stats', [DashboardController::class, 'index']);
    Route::get('/analytics/sales-by-day', [AnalyticsController::class, 'salesByDay']);
    Route::get('/analytics/top-products', [AnalyticsController::class, 'topProducts']);

    // Gestion Produits
    Route::get('/products', [ProductManagementController::class, 'index']);
    Route::post('/products', [ProductManagementController::class, 'store']);
    Route::put('/products/{id}', [ProductManagementController::class, 'update']);
    Route::delete('/products/{id}', [ProductManagementController::class, 'destroy']);
    Route::delete('/products/images/{id}', [ProductManagementController::class, 'destroyImage']);

    // Gestion Promotions
    Route::get('/promotions', [PromoManagemetnController::class, 'index']);
    Route::post('/promotions', [PromoManagemetnController::class, 'store']);
    Route::put('/promotions/{id}', [PromoManagemetnController::class, 'update']);
    Route::delete('/promotions/{id}', [PromoManagemetnController::class, 'destroy']);

    // Gestion Catégories
    Route::get('/categories', [CategoryManagementController::class, 'index']);
    Route::post('/categories', [CategoryManagementController::class, 'store']);
    Route::get('/categories/{id}', [CategoryManagementController::class, 'show']);
    Route::put('/categories/{id}', [CategoryManagementController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryManagementController::class, 'destroy']);

    // Gestion Publicités
    Route::get('/advertisements', [AdminAdController::class, 'index']);
    Route::post('/advertisements', [AdminAdController::class, 'store']);
    Route::post('/advertisements/{id}', [AdminAdController::class, 'update']);
    Route::delete('/advertisements/{id}', [AdminAdController::class, 'destroy']);
});
