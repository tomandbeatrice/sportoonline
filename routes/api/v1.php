<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controller Gruplama
use App\Http\Controllers\{
    AuthController,
    ProductController,
    OrderController,
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
    CartController,
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
});

// ============================================
// Banners & Campaigns & Services (Public)
// ============================================
Route::get('/banners', [\App\Http\Controllers\Api\BannerController::class, 'index']);
Route::get('/campaigns/active', [\App\Http\Controllers\Api\PublicCampaignController::class, 'index']);
Route::get('/services', [\App\Http\Controllers\Api\ServiceModuleController::class, 'index']);

// ============================================
// Orders (Active)
// ============================================
Route::get('/orders/active', [OrderController::class, 'active'])->middleware('auth:sanctum')->name('v1.orders.active');

// ============================================
// Segments (Admin)
// ============================================
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->name('v1.admin.')->group(function () {
    Route::get('/segments', [SellerController::class, 'segments'])->name('segments');
    Route::post('/segments/update', [SellerController::class, 'updateSegments'])->name('segments.update');
    Route::get('/segment-success-predictions', [SellerController::class, 'segmentSuccessPredictions'])->name('segment-predictions');
});

// ============================================
// Products (Protected)
// ============================================
Route::prefix('products')->name('v1.products.')->group(function () {
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

// ============================================
// AI Recommendations
// ============================================
Route::prefix('ai')->name('v1.ai.')->group(function () {
    Route::get('/recommendations', [\App\Http\Controllers\AIRecommendationController::class, 'getPersonalizedRecommendations'])->name('recommendations');
    Route::get('/recommendations/refresh', [\App\Http\Controllers\AIRecommendationController::class, 'refreshRecommendations'])->middleware('auth:sanctum')->name('recommendations.refresh');
    Route::get('/products/{productId}/similar', [\App\Http\Controllers\AIRecommendationController::class, 'getSimilarProducts'])->name('similar');
    Route::get('/products/{productId}/bought-together', [\App\Http\Controllers\AIRecommendationController::class, 'getFrequentlyBoughtTogether'])->name('bought-together');
    Route::get('/dashboard', [\App\Http\Controllers\AIRecommendationController::class, 'dashboard'])->middleware('auth:sanctum')->name('dashboard');
});

// ============================================
// User Behavior Tracking
// ============================================
Route::prefix('tracking')->name('v1.tracking.')->group(function () {
    // Görüntüleme takibi
    Route::post('/product-view', [\App\Http\Controllers\UserBehaviorController::class, 'trackProductView'])->name('product-view');
    Route::post('/view-duration', [\App\Http\Controllers\UserBehaviorController::class, 'updateViewDuration'])->name('view-duration');
    
    // Kampanya takibi
    Route::post('/campaign-view', [\App\Http\Controllers\UserBehaviorController::class, 'trackCampaignView'])->name('campaign-view');
    Route::post('/campaign-click', [\App\Http\Controllers\UserBehaviorController::class, 'trackCampaignClick'])->name('campaign-click');
    
    // Arama takibi
    Route::post('/search', [\App\Http\Controllers\UserBehaviorController::class, 'trackSearch'])->name('search');
    Route::post('/search-click', [\App\Http\Controllers\UserBehaviorController::class, 'trackSearchClick'])->name('search-click');
    
    // Sepet takibi
    Route::post('/cart-event', [\App\Http\Controllers\UserBehaviorController::class, 'trackCartEvent'])->name('cart-event');
    
    // Veri okuma
    Route::get('/recent-views', [\App\Http\Controllers\UserBehaviorController::class, 'getRecentViews'])->name('recent-views');
    Route::get('/popular-searches', [\App\Http\Controllers\UserBehaviorController::class, 'getPopularSearches'])->name('popular-searches');
    Route::get('/trending', [\App\Http\Controllers\UserBehaviorController::class, 'getTrendingProducts'])->name('trending');
});

// ============================================
// Discovery & Maps
// ============================================
Route::get('/discovery/places', [\App\Http\Controllers\Api\DiscoveryController::class, 'index'])->name('v1.discovery.places');


// ============================================
// Gamification
// ============================================
Route::middleware('auth:sanctum')->get('/gamification/status', [\App\Http\Controllers\Api\GamificationController::class, 'status'])->name('v1.gamification.status');


// ============================================
// AI Chat Assistant
// ============================================
Route::post('/chat/message', [\App\Http\Controllers\Api\ChatController::class, 'sendMessage'])->name('v1.chat.message');

