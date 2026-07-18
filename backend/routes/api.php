<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\CmsController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PermissionController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\SeoController;
use App\Http\Controllers\Api\V1\SocialActionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\WebsiteConfigController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Configuration Routes
    Route::get('/settings', [WebsiteConfigController::class, 'settings']);
    Route::get('/theme', [WebsiteConfigController::class, 'theme']);
    Route::get('/header', [WebsiteConfigController::class, 'header']);
    Route::get('/footer', [WebsiteConfigController::class, 'footer']);
    Route::get('/navigation', [WebsiteConfigController::class, 'navigation']);
    Route::get('/homepage', [WebsiteConfigController::class, 'homepage']);
    Route::get('/page/{slug}', [WebsiteConfigController::class, 'getPage']);

    // Catalog Routes
    Route::get('/products', [CatalogController::class, 'index']);
    Route::get('/products/{id_or_slug}', [CatalogController::class, 'show']);
    Route::get('/categories', [CatalogController::class, 'categories']);

    // Social & Interactive Action Routes
    Route::post('/wishlist', [SocialActionController::class, 'toggleWishlist']);
    Route::post('/compare', [SocialActionController::class, 'compare']);
    Route::post('/recently-viewed', [SocialActionController::class, 'trackRecentlyViewed']);
    Route::get('/recently-viewed', [SocialActionController::class, 'getRecentlyViewed']);
    Route::post('/reviews', [SocialActionController::class, 'postReview']);

    // Cart routes
    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::put('/cart/items/{variant_id}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{variant_id}', [CartController::class, 'removeItem']);
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon']);
    Route::post('/cart/merge', [CartController::class, 'merge'])->middleware('auth:sanctum');

    // Checkout & Order Tracking Routes
    Route::post('/checkout', [CheckoutController::class, 'checkout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order_number}', [OrderController::class, 'show']);

    // CMS & Blog Routes
    Route::get('/posts', [CmsController::class, 'posts']);
    Route::get('/posts/{slug}', [CmsController::class, 'post']);
    Route::get('/faqs', [CmsController::class, 'faqs']);
    Route::get('/testimonials', [CmsController::class, 'testimonials']);
    Route::get('/lookbooks', [CmsController::class, 'lookbooks']);

    // SEO Crawlers Routes
    Route::get('/sitemap.xml', [SeoController::class, 'sitemap']);
    Route::get('/robots.txt', [SeoController::class, 'robots']);

    // Guest Auth Routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/otp/send', [AuthController::class, 'sendOtp']);
    Route::post('/auth/otp/verify', [AuthController::class, 'verifyOtp']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/auth/check-user', [AuthController::class, 'checkUser']);
    Route::post('/auth/register-complete', [AuthController::class, 'registerComplete']);

    // Authenticated Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // User Management (Guarded by role:Super Admin)
        Route::middleware('role:Super Admin')->group(function () {
            Route::apiResource('/users', UserController::class);
            Route::post('/users/{id}/restore', [UserController::class, 'restore']);

            Route::get('/roles', [RoleController::class, 'index']);
            Route::get('/permissions', [PermissionController::class, 'index']);
        });
    });
});
