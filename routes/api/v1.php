<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controller Gruplama
use App\Http\Controllers\{
    AuthController,
    ProductController,
    OrderController,
    CartController,
    CategoryController,
    DashboardController,
    SellerController,
    SellerDashboardController,
    BuyerDashboardController,
    NotificationController,
    FavoriteController,
    BulkProductController,
};

use App\Http\Controllers\Api\{
    C2CDashboardController,
    PaymentController as ApiPaymentController,
    TurboCompetitionController
};

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
| Version: 1.0.0
| All routes are prefixed with /api/v1
*/

// ============================================
// Authentication
// ============================================
Route::post('/login', [AuthController::class, 'login'])->name('v1.login');
Route::post('/register', [AuthController::class, 'register'])->name('v1.register');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('v1.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('v1.me');
    Route::post('/apply-seller', [AuthController::class, 'applySeller'])->name('v1.apply-seller');
});

// ============================================
// User Info
// ============================================
Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user())
    ->name('v1.user');

// ============================================
// C2C Marketplace Dashboard
// ============================================
Route::prefix('c2c')->middleware('auth:sanctum')->name('v1.c2c.')->group(function () {
    Route::get('/dashboard', [C2CDashboardController::class, 'index'])->name('dashboard');
    Route::post('/workflow', [C2CDashboardController::class, 'executeWorkflow'])->name('workflow');
    Route::post('/quick-action', [C2CDashboardController::class, 'quickAction'])->name('quick-action');
});

// ============================================
// Public Marketplace Stats
// ============================================
Route::get('/marketplace/stats', [DashboardController::class, 'marketplaceStats'])
    ->name('v1.marketplace.stats');

// ============================================
// Categories (Public)
// ============================================
Route::get('/categories', [CategoryController::class, 'index'])
    ->name('v1.categories')
    ->middleware('cache.response:7200'); // 2 hours cache

// ============================================
// Products
// ============================================
Route::prefix('products')->name('v1.products.')->group(function () {
    // Public routes with caching
    Route::get('/', [ProductController::class, 'index'])
        ->name('index')
        ->middleware('cache.response:3600'); // 1 hour cache
        
    Route::get('/search/autocomplete', [ProductController::class, 'autocomplete'])
        ->name('autocomplete')
        ->middleware('cache.response:1800'); // 30 min cache
        
    Route::get('/{product}', [ProductController::class, 'show'])
        ->name('show')
        ->middleware('cache.response:3600'); // 1 hour cache
    
    // Product reviews
    Route::get('/{product}/reviews', [\App\Http\Controllers\ReviewController::class, 'productReviews'])
        ->name('reviews');
    
    // Protected routes (sellers only)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        
        // Bulk operations
        Route::post('/bulk-upload-csv', [BulkProductController::class, 'uploadCsv'])->name('bulk.csv');
        Route::post('/bulk-upload-excel', [BulkProductController::class, 'uploadExcel'])->name('bulk.excel');
        Route::put('/bulk-update', [BulkProductController::class, 'bulkUpdate'])->name('bulk.update');
        Route::delete('/bulk-delete', [BulkProductController::class, 'bulkDelete'])->name('bulk.delete');
        Route::get('/check-limit', [BulkProductController::class, 'checkProductLimit'])->name('check-limit');
    });
});

// ============================================
// Cart
// ============================================
Route::prefix('cart')->middleware('auth:sanctum')->name('v1.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/', [CartController::class, 'add'])->name('add');
    Route::put('/', [CartController::class, 'updateQuantity'])->name('update');
    Route::delete('/', [CartController::class, 'remove'])->name('remove');
});

// ============================================
// Favorites
// ============================================
Route::prefix('favorites')->middleware('auth:sanctum')->name('v1.favorites.')->group(function () {
    Route::get('/', [FavoriteController::class, 'index'])->name('index');
    Route::post('/', [FavoriteController::class, 'store'])->name('store');
    Route::delete('/{productId}', [FavoriteController::class, 'destroy'])->name('destroy');
});

// ============================================
// Addresses
// ============================================
Route::prefix('addresses')->middleware('auth:sanctum')->name('v1.addresses.')->group(function () {
    $controller = \App\Http\Controllers\Api\AddressController::class;
    
    Route::get('/', [$controller, 'index'])->name('index');
    Route::get('/cities', [$controller, 'getCities'])->name('cities');
    Route::get('/{id}', [$controller, 'show'])->name('show');
    Route::post('/', [$controller, 'store'])->name('store');
    Route::put('/{id}', [$controller, 'update'])->name('update');
    Route::delete('/{id}', [$controller, 'destroy'])->name('destroy');
    Route::post('/{id}/set-default', [$controller, 'setDefault'])->name('set-default');
});

// ============================================
// Orders
// ============================================
Route::prefix('orders')->middleware('auth:sanctum')->name('v1.orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::put('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
});

// ============================================
// Payments
// ============================================
Route::prefix('payments')->name('v1.payments.')->group(function () {
    Route::post('/initialize', [ApiPaymentController::class, 'initialize'])->name('initialize');
    Route::post('/callback', [ApiPaymentController::class, 'callback'])->name('callback');
    Route::get('/status/{orderId}', [ApiPaymentController::class, 'status'])->name('status');
});

// ============================================
// Notifications
// ============================================
Route::prefix('notifications')->middleware('auth:sanctum')->name('v1.notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
});

// ============================================
// Seller Dashboard
// ============================================
Route::prefix('seller')->middleware(['auth:sanctum'])->name('v1.seller.')->group(function () {
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [SellerDashboardController::class, 'stats'])->name('stats');
});

// ============================================
// Buyer Dashboard
// ============================================
Route::prefix('buyer')->middleware(['auth:sanctum'])->name('v1.buyer.')->group(function () {
    Route::get('/dashboard', [BuyerDashboardController::class, 'index'])->name('dashboard');
});
