<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import des Controllers (Identiques à votre code...)
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AdvertisementController as ApiAdController;
use App\Http\Controllers\Admin\ProductManagementController;
use App\Http\Controllers\Admin\PromoManagemetnController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AdvertisementController as AdminAdController;
use App\Http\Controllers\Api\CartController;
/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES (Client)
|--------------------------------------------------------------------------
*/

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/sale', [ProductController::class, 'onSale']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/promotions', [PromoController::class, 'index']);
Route::get('/categories', [CategoryManagementController::class, 'index']);
Route::get('/ads', [ApiAdController::class, 'index']);
Route::get('/search', [SearchController::class, 'search']);

// Routes nécessitant d'être connecté (Client standard)




// Route::middleware('auth:sanctum')->group(function () {
    Route::post('/promotions/check', [PromoController::class, 'check']);


    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/add', [WishlistController::class, 'add']);
    Route::delete('/wishlist/remove/{productId}', [WishlistController::class, 'remove']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    // Avis
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
// });

/*
|--------------------------------------------------------------------------
| ROUTES ADMINISTRATEUR (Protégées)
|--------------------------------------------------------------------------
*/

// On applique l'authentification ET le middleware admin à tout le groupe
Route::prefix('admin')->group(function () {

    // Produits Admin
    Route::get('/products', [ProductManagementController::class, 'index']);
    Route::post('/products', [ProductManagementController::class, 'store']);
    Route::put('/products/{id}', [ProductManagementController::class, 'update']);
    Route::delete('/products/{id}', [ProductManagementController::class, 'destroy']);
    Route::delete('/products/images/{id}', [ProductManagementController::class, 'destroyImage']);

    // Promotions Admin
    Route::get('/promotions', [PromoManagemetnController::class, 'index']);
    Route::post('/promotions', [PromoManagemetnController::class, 'store']);
    Route::put('/promotions/{id}', [PromoManagemetnController::class, 'update']);
    Route::delete('/promotions/{id}', [PromoManagemetnController::class, 'destroy']);

    // Catégories Admin
    Route::get('/categories', [CategoryManagementController::class, 'index']);
    Route::post('/categories', [CategoryManagementController::class, 'store']);
    Route::get('/categories/{id}', [CategoryManagementController::class, 'show']);
    Route::put('/categories/{id}', [CategoryManagementController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryManagementController::class, 'destroy']);

    // Publicités Admin
    Route::get('/advertisements', [AdminAdController::class, 'index']);
    Route::post('/advertisements', [AdminAdController::class, 'store']);
    Route::post('/advertisements/{id}', [AdminAdController::class, 'update']);
    Route::delete('/advertisements/{id}', [AdminAdController::class, 'destroy']);

    // Dashboard & Analytics
    Route::get('/dashboard/stats', [DashboardController::class, 'index']);
    Route::get('/analytics/sales-by-day', [AnalyticsController::class, 'salesByDay']);
    Route::get('/analytics/top-products', [AnalyticsController::class, 'topProducts']);
    Route::get('/analytics/orders-by-status', [AnalyticsController::class, 'ordersByStatus']);
});

/*
|--------------------------------------------------------------------------
| DEBUG
|--------------------------------------------------------------------------
*/
Route::get('/debug/products', [DebugController::class, 'products']);
