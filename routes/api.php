<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UnifiedDashboardController;
use App\Http\Controllers\Admin\AdminTourController;
use App\Http\Controllers\Admin\AdminCarRentalController;
use App\Http\Controllers\Admin\AdminInsuranceController;
use App\Http\Controllers\Admin\AdminActivityController;
use App\Http\Controllers\Api\NearbyBusinessController;
use App\Http\Controllers\Api\MarketplaceController;
use App\Http\Controllers\Api\PublicServiceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| All API routes are versioned for backward compatibility.
|
*/

// ============================================
// API Version 1 (Current Stable)
// ============================================
Route::prefix('v1')->group(base_path('routes/api/v1.php'));

// ============================================
// Legacy Routes (Backward Compatibility)
// ============================================
// Redirect old non-versioned routes to v1 for backward compatibility
// These will be deprecated in future versions

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use App\Helpers\ExportLogHelper;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\ExportLogController;
use App\Http\Controllers\Admin\PaymentLogController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\FeatureController;

// Controller Gruplama
use App\Http\Controllers\{
    AuthController,
    ProductController,
    OrderController,
    CartController,
    ThemeController,
    BannerController,
    CategoryController,
    DashboardController,
    AdminController,
    AdminStatsController,
    AdminSellerController,
    AdminOrderController,
    AdminCustomerController,
    AdminCategoryController,
    AdminBannerController,
    AdminPageController,
    AdminReportController,
    AdminSettingsController,
    AdminNotificationController,
    AdminThemeController,
    SellerController,
    SellerDashboardController,
    SellerSettingsController,
    SellerApplicationController,
    BuyerDashboardController,
    InvitationController,
    WebhookController,
    ShippingController,
    CockpitController,
    ExportController,
    IncentiveCampaignController,
    NotificationController,
    FavoriteController,
    CampaignAIController,
    BulkProductController,
    SubscriptionController
};

use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Seller\SellerCommissionController;
use App\Http\Controllers\Admin\AdminCommissionController;
use App\Http\Controllers\Seller\ShippingFeeController;
use App\Http\Controllers\Admin\WithholdingTaxController;
use App\Http\Controllers\Api\TurboCompetitionController;
use App\Http\Controllers\Seller\TurboCouponController;

// Return/Refund Controllers
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\Admin\AdminReturnController;
use App\Http\Controllers\Seller\SellerReturnController;
use App\Http\Controllers\Seller\SellerOrderController;

// C2C Marketplace
use App\Http\Controllers\Api\C2CDashboardController;

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// v1 prefix aliases for frontend compatibility
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });
});

// Public Seller Application (no auth required)
Route::post('/seller-application/register', [SellerApplicationController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/apply-seller', [AuthController::class, 'applySeller']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // User Tasks
    Route::apiResource('user-tasks', \App\Http\Controllers\UserTaskController::class);
});

// Kullanıcı Bilgisi
Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());

// C2C Marketplace Dashboard (Role-based)
Route::prefix('c2c')->middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [C2CDashboardController::class, 'index']);
    Route::post('/workflow', [C2CDashboardController::class, 'executeWorkflow']);
    Route::post('/quick-action', [C2CDashboardController::class, 'quickAction']);
});

// Marketplace Stats (Public)
Route::get('/marketplace/stats', [DashboardController::class, 'marketplaceStats']);

// Marketplace Home API (Public)
Route::prefix('marketplace')->group(function () {
    Route::get('/', [MarketplaceController::class, 'index']);
    Route::get('/campaigns', [MarketplaceController::class, 'campaigns']);
    Route::get('/banners', [MarketplaceController::class, 'banners']);
    Route::get('/tasks', [MarketplaceController::class, 'tasks']);
    Route::get('/bundles', [MarketplaceController::class, 'bundles']);
});

// Categories (Public)
Route::get('/categories', [CategoryController::class, 'index']);

// Public Services (Tours, Cars, Insurance, Activities)
Route::prefix('services')->group(function () {
    Route::get('/tours', [PublicServiceController::class, 'tours']);
    Route::get('/tours/{id}', [PublicServiceController::class, 'showTour']);
    Route::get('/cars', [PublicServiceController::class, 'cars']);
    Route::get('/cars/{id}', [PublicServiceController::class, 'showCar']);
    Route::get('/insurance', [PublicServiceController::class, 'insurance']);
    Route::get('/insurance/{id}', [PublicServiceController::class, 'showInsurance']);
    Route::get('/activities', [PublicServiceController::class, 'activities']);
    Route::get('/activities/{id}', [PublicServiceController::class, 'showActivity']);
    Route::get('/search', [PublicServiceController::class, 'search']);
});

// Ürünler (Herkese açık: listeleme ve detay)
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/search/autocomplete', [ProductController::class, 'autocomplete']);
    Route::get('/{product}', [ProductController::class, 'show']);
    
    // Product reviews (public read, auth write)
    Route::get('/{product}/reviews', [\App\Http\Controllers\ReviewController::class, 'productReviews']);
    
    // Sadece satıcılar için
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{product}', [ProductController::class, 'update']);
        Route::delete('/{product}', [ProductController::class, 'destroy']);
        
        // Toplu işlemler (Bulk Operations)
        Route::post('/bulk-upload-csv', [BulkProductController::class, 'uploadCsv']);
        Route::post('/bulk-upload-excel', [BulkProductController::class, 'uploadExcel']);
        Route::put('/bulk-update', [BulkProductController::class, 'bulkUpdate']);
        Route::delete('/bulk-delete', [BulkProductController::class, 'bulkDelete']);
        Route::get('/check-limit', [BulkProductController::class, 'checkProductLimit']);
    });
});

// Mock Ürün Testi
Route::get('/mock/products/{id}', fn($id) => [
    'id' => $id,
    'name' => 'Segment Export',
    'description' => 'Canlı veriyle test edilebilir ürün.',
    'price' => 149.99,
    'campaign' => ['title' => 'Eylül İndirimi'],
]);

// Sepet
Route::prefix('cart')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/', [CartController::class, 'add']);
    Route::put('/', [CartController::class, 'updateQuantity']);
    Route::delete('/', [CartController::class, 'remove']);
});

// Favoriler
Route::prefix('favorites')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [FavoriteController::class, 'index']);
    Route::post('/', [FavoriteController::class, 'store']);
    Route::delete('/{productId}', [FavoriteController::class, 'destroy']);
});

// Adresler
Route::prefix('addresses')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\AddressController::class, 'index']);
    Route::get('/cities', [App\Http\Controllers\Api\AddressController::class, 'getCities']);
    Route::get('/{id}', [App\Http\Controllers\Api\AddressController::class, 'show']);
    Route::post('/', [App\Http\Controllers\Api\AddressController::class, 'store']);
    Route::put('/{id}', [App\Http\Controllers\Api\AddressController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Api\AddressController::class, 'destroy']);
    Route::post('/{id}/set-default', [App\Http\Controllers\Api\AddressController::class, 'setDefault']);
});

// Ödeme & Sipariş Zinciri
Route::post('/checkout', [OrderController::class, 'store'])->middleware('auth:sanctum');

Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/active', [OrderController::class, 'active']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::post('/', [OrderController::class, 'store']);
    Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
    Route::put('/{id}/ship', [OrderController::class, 'ship']);
    Route::post('/{id}/complete', [OrderController::class, 'complete']);
    Route::get('/seller/{sellerId}/stats', [OrderController::class, 'sellerStats']);
    
    // New buyer order endpoints
    Route::get('/{id}/invoice', [OrderController::class, 'downloadInvoice']);
    Route::post('/{id}/cancel-request', [OrderController::class, 'requestCancellation']);
});

Route::prefix('payments')->group(function () {
    // Start payment (requires authentication)
    Route::post('/start', [PaymentController::class, 'startPayment'])->middleware('auth:sanctum');
    
    // Iyzico callback (no auth needed - public endpoint)
    Route::post('/callback', [PaymentController::class, 'paymentCallback'])->name('iyzico.callback');
    
    // PayTR callbacks
    Route::get('/paytr/success', [PaymentController::class, 'paytrSuccess'])->name('payment.paytr.success');
    Route::get('/paytr/failure', [PaymentController::class, 'paytrFailure'])->name('payment.paytr.failure');
    Route::post('/paytr/callback', [PaymentController::class, 'paytrCallback'])->name('paytr.callback');
    
    // Stripe callbacks
    Route::get('/stripe/success', [PaymentController::class, 'stripeSuccess'])->name('payment.stripe.success');
    Route::get('/stripe/cancel', [PaymentController::class, 'stripeCancel'])->name('payment.stripe.cancel');
    Route::post('/stripe/webhook', [PaymentController::class, 'stripeWebhook'])->name('stripe.webhook');
});

// New Multi-Gateway Payment System
Route::prefix('payment')->group(function () {
    // Public endpoints - get available gateways
    Route::get('/gateways', [ApiPaymentController::class, 'gateways']);
    
    // Initiate payment (requires auth)
    Route::post('/initiate', [ApiPaymentController::class, 'initiate'])->middleware('auth:sanctum');
    
    // Gateway callbacks (public)
    Route::post('/callback/iyzico', [ApiPaymentController::class, 'callbackIyzico'])->name('payment.callback.iyzico');
    Route::post('/callback/paytr', [ApiPaymentController::class, 'callbackPaytr'])->name('payment.callback.paytr');
    Route::get('/callback/paytr', [ApiPaymentController::class, 'callbackPaytr']);
    Route::post('/callback/mokapos', [ApiPaymentController::class, 'callbackMokapos'])->name('payment.callback.mokapos');
    Route::post('/callback/ziraatpay', [ApiPaymentController::class, 'callbackZiraatpay'])->name('payment.callback.ziraatpay');
    
    // Fail URLs
    Route::get('/fail/paytr', fn() => redirect('/payment/failed'))->name('payment.fail.paytr');
    Route::get('/fail/ziraatpay', fn() => redirect('/payment/failed'))->name('payment.fail.ziraatpay');
    
    // Refund (admin only)
    Route::post('/refund', [ApiPaymentController::class, 'refund'])->middleware('auth:sanctum');
});

// Tema & Banner
Route::post('/theme', [ThemeController::class, 'store']);
Route::post('/banners', [BannerController::class, 'store']);

// Kategoriler
Route::get('/kategoriler', [CategoryController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);

// Subscription / Abonelik Sistemi
Route::prefix('subscriptions')->group(function () {
    // Public - view plans
    Route::get('/plans', [SubscriptionController::class, 'getPlans']);
    
    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/my-subscription', [SubscriptionController::class, 'mySubscription']);
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
        Route::post('/cancel', [SubscriptionController::class, 'cancel']);
        Route::post('/upgrade', [SubscriptionController::class, 'upgrade']);
        Route::post('/downgrade', [SubscriptionController::class, 'downgrade']);
        Route::post('/renew', [SubscriptionController::class, 'renew']);
        Route::get('/payment-history', [SubscriptionController::class, 'paymentHistory']);
        Route::get('/check-limits', [SubscriptionController::class, 'checkLimits']);
    });
});

// Seller Commissions
Route::prefix('seller/commissions')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [SellerCommissionController::class, 'index']);
    Route::get('/current', [SellerCommissionController::class, 'currentMonth']);
    Route::get('/forecast', [SellerCommissionController::class, 'forecast']);
    Route::get('/compare-plans', [SellerCommissionController::class, 'comparePlans']);
    Route::get('/{month}', [SellerCommissionController::class, 'show']);
});

// Admin Commissions
Route::prefix('admin/commissions')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [AdminCommissionController::class, 'index']);
    Route::get('/statistics', [AdminCommissionController::class, 'statistics']);
    Route::post('/calculate-month', [AdminCommissionController::class, 'calculateMonth']);
    Route::post('/{commissionId}/mark-paid', [AdminCommissionController::class, 'markAsPaid']);
    Route::get('/{userId}/{month}', [AdminCommissionController::class, 'show']);
});

// Admin Subscription Plans Management
Route::prefix('admin/subscription-plans')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [SubscriptionController::class, 'getPlans']);
    Route::put('/{id}', [SubscriptionController::class, 'updatePlan']);
    Route::post('/', [SubscriptionController::class, 'createPlan']);
});

// Seller Shipping Fees
Route::prefix('seller/shipping')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ShippingFeeController::class, 'index']);
    Route::post('/upsert', [ShippingFeeController::class, 'upsert']);
    Route::post('/set-defaults', [ShippingFeeController::class, 'setDefaults']);
    Route::post('/{id}/toggle', [ShippingFeeController::class, 'toggle']);
    Route::delete('/{id}', [ShippingFeeController::class, 'destroy']);
    Route::post('/calculate', [ShippingFeeController::class, 'calculate']);
});

// ════════════════════════════════════════════════════════════════════════════
// İADE / RETURN - REFUND ROUTES
// ════════════════════════════════════════════════════════════════════════════

// Customer Returns (Müşteri İade İşlemleri)
Route::prefix('returns')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ReturnController::class, 'index']);
    Route::post('/', [ReturnController::class, 'store']);
    Route::get('/reasons', [ReturnController::class, 'getReasons']);
    Route::get('/check-eligibility/{orderId}', [ReturnController::class, 'checkEligibility']);
    Route::get('/{id}', [ReturnController::class, 'show']);
    Route::post('/{id}/ship', [ReturnController::class, 'markAsShipped']);
    Route::post('/{id}/cancel', [ReturnController::class, 'cancel']);
});

// Seller Returns (Satıcı İade Yönetimi)
Route::prefix('seller/returns')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [SellerReturnController::class, 'index']);
    Route::get('/statistics', [SellerReturnController::class, 'statistics']);
    Route::get('/{id}', [SellerReturnController::class, 'show']);
    Route::post('/{id}/approve', [SellerReturnController::class, 'approve']);
    Route::post('/{id}/reject', [SellerReturnController::class, 'reject']);
    Route::post('/{id}/shipping-code', [SellerReturnController::class, 'sendShippingCode']);
    Route::post('/{id}/receive', [SellerReturnController::class, 'markAsReceived']);
    Route::post('/{id}/inspect', [SellerReturnController::class, 'markAsInspected']);
    Route::post('/{id}/note', [SellerReturnController::class, 'addNote']);
});

// Admin Returns (Admin İade Yönetimi)
Route::prefix('admin/returns')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [AdminReturnController::class, 'index']);
    Route::get('/statistics', [AdminReturnController::class, 'statistics']);
    Route::get('/export', [AdminReturnController::class, 'export']);
    Route::get('/{id}', [AdminReturnController::class, 'show']);
    Route::post('/{id}/approve', [AdminReturnController::class, 'approve']);
    Route::post('/{id}/reject', [AdminReturnController::class, 'reject']);
    Route::post('/{id}/receive', [AdminReturnController::class, 'markAsReceived']);
    Route::post('/{id}/inspect', [AdminReturnController::class, 'markAsInspected']);
    Route::post('/{id}/refund', [AdminReturnController::class, 'initiateRefund']);
    Route::post('/{id}/refund-complete', [AdminReturnController::class, 'completeRefund']);
    Route::post('/{id}/complete', [AdminReturnController::class, 'complete']);
    Route::post('/{id}/note', [AdminReturnController::class, 'addNote']);
});

// Admin Withholding Tax Reports
Route::prefix('admin/withholding-tax')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/monthly-summary', [WithholdingTaxController::class, 'monthlySummary']);
    Route::get('/annual-report', [WithholdingTaxController::class, 'annualReport']);
    Route::get('/export', [WithholdingTaxController::class, 'export']);
});

// Turbo Competition (Public Leaderboards)
Route::prefix('turbo')->group(function () {
    Route::get('/current', [TurboCompetitionController::class, 'currentCompetition']);
    Route::get('/leaderboard/customers', [TurboCompetitionController::class, 'customerLeaderboard']);
    Route::get('/leaderboard/sellers', [TurboCompetitionController::class, 'sellerLeaderboard']);
    Route::get('/history', [TurboCompetitionController::class, 'history']);
});

// Turbo Competition (Authenticated User)
Route::prefix('turbo')->middleware('auth:sanctum')->group(function () {
    Route::get('/my-position', [TurboCompetitionController::class, 'myPosition']);
    Route::get('/my-winnings', [TurboCompetitionController::class, 'myWinnings']);
});

// Turbo Coupons (Seller Management)
Route::prefix('seller/turbo-coupons')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TurboCouponController::class, 'index']);
    Route::post('/{id}/toggle', [TurboCouponController::class, 'toggle']);
    Route::get('/{id}/usage', [TurboCouponController::class, 'usage']);
});

// Admin Turbo Winners Management
Route::prefix('admin/turbo-winners')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\TurboWinnerController::class, 'index']);
    Route::get('/by-month', [\App\Http\Controllers\Admin\TurboWinnerController::class, 'getByMonth']);
    Route::get('/{turboWinner}', [\App\Http\Controllers\Admin\TurboWinnerController::class, 'show']);
    Route::put('/{turboWinner}', [\App\Http\Controllers\Admin\TurboWinnerController::class, 'update']);
    Route::post('/bulk-update', [\App\Http\Controllers\Admin\TurboWinnerController::class, 'bulkUpdate']);
});
    Route::post('/validate', [TurboCouponController::class, 'validateCoupon']);
});

// Vendors / Satıcılar
Route::prefix('vendors')->group(function () {
    Route::get('/', [\App\Http\Controllers\VendorController::class, 'index']);
    Route::get('/compare', [\App\Http\Controllers\VendorController::class, 'compare']);
    Route::get('/{vendor}', [\App\Http\Controllers\VendorController::class, 'show']);
    Route::get('/{vendor}/reviews', [\App\Http\Controllers\ReviewController::class, 'vendorReviews']);
});

// Dashboard
Route::get('/module-logs', [DashboardController::class, 'getModuleLogs']);
Route::get('/dashboard/realtime-metrics', [DashboardController::class, 'getRealtimeMetrics']);

// Notifications (for users)
Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/unread', [NotificationController::class, 'unread']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
});

// Kargo
Route::get('/kargo', function () {
    try {
        $response = Http::withToken(env('GELIVER_API_KEY'))
            ->get('https://api.geliver.io/v1/shipments');

        return $response->successful()
            ? $response->json()
            : response()->json([
                'error' => 'Kargo verisi alınamadı',
                'status' => $response->status()
            ], $response->status());
    } catch (\Throwable $e) {
        return response()->json([
            'error' => 'API bağlantı hatası',
            'message' => $e->getMessage()
        ], 500);
    }
});

// 📦 Kargo / Shipping
Route::prefix('shipping')->group(function () {
    Route::get('/providers', [ShippingController::class, 'getProviders']);
    Route::post('/quotes', [ShippingController::class, 'getQuotes']);
    Route::post('/create', [ShippingController::class, 'createShipment'])->middleware('auth:sanctum');
    Route::get('/track/{trackingCode}', [ShippingController::class, 'track']);
    Route::post('/cancel', [ShippingController::class, 'cancel'])->middleware('auth:sanctum');
});

// 🧹 Export Loglar
Route::prefix('exports')->group(function () {
    Route::get('/logs', [ExportLogController::class, 'index']);
    Route::get('/cleanup-log', fn() => ExportLogHelper::getLatestCleanupLog());
    Route::get('/cleanup-history', fn() => ExportLogHelper::getCleanupLogHistory());
    Route::get('/scheduled', [ExportController::class, 'getScheduledExports']);
});

// 🧠 Cockpit
Route::prefix('cockpit')->group(function () {
    Route::post('/asset-log', [CockpitController::class, 'logAssetPerformance']);
    Route::get('/asset-performance', [CockpitController::class, 'getAssetPerformance']);
    Route::get('/asset-performance/export', [CockpitController::class, 'exportAssetPerformanceCsv']);
    Route::get('/exports/scheduled', [ExportController::class, 'getScheduledExports']);
});

// 🧠 Satıcı Paneli
Route::prefix('seller')->middleware('auth:sanctum')->group(function () {
    Route::get('/stats', [SellerDashboardController::class, 'stats']);
    Route::get('/products', [SellerDashboardController::class, 'products']);
    Route::get('/orders', [SellerDashboardController::class, 'orders']);
    Route::get('/financial-report', [SellerController::class, 'financialReport']);
    
    // Satıcı Sipariş Yönetimi
    Route::patch('/order-items/{orderItem}/status', [SellerOrderController::class, 'updateStatus']);
    
    // Satıcı Ayarları
    Route::get('/settings', [SellerSettingsController::class, 'index']);
    Route::put('/settings/profile', [SellerSettingsController::class, 'updateProfile']);
    Route::put('/settings/payment', [SellerSettingsController::class, 'updatePayment']);
    Route::put('/settings/shipping', [SellerSettingsController::class, 'updateShipping']);
    Route::put('/settings/notifications', [SellerSettingsController::class, 'updateNotifications']);
    Route::put('/settings/tax', [SellerSettingsController::class, 'updateTax']);
    Route::post('/settings/api-keys', [SellerSettingsController::class, 'createApiKey']);
    Route::delete('/settings/api-keys/{id}', [SellerSettingsController::class, 'deleteApiKey']);
});

// Satıcı Başvuru (Public)
Route::post('/seller/register', [SellerApplicationController::class, 'register']);

// 🛒 Alıcı Paneli
Route::prefix('buyer')->middleware('auth:sanctum')->group(function () {
    Route::get('/stats', [BuyerDashboardController::class, 'stats']);
    Route::get('/orders', [BuyerDashboardController::class, 'orders']);
    Route::get('/favorites', [BuyerDashboardController::class, 'favorites']);
});

// 🛡️ Admin Panel
// PRODUCTION: Admin auth ve role kontrolü aktif
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Dashboard Stats
    Route::get('/stats', [AdminController::class, 'stats']);
    Route::get('/realtime-metrics', [AdminController::class, 'realtimeMetrics']);
    Route::get('/financial-report', [AdminController::class, 'financialReport']);
    
    // Seller Application Management (supports ?service_type=food|hotel|transport|services|career)
    Route::get('/seller-applications', [SellerApplicationController::class, 'index']);
    Route::get('/seller-applications/stats', [SellerApplicationController::class, 'stats']);
    Route::get('/seller-applications/stats-by-type', [SellerApplicationController::class, 'statsByServiceType']);
    Route::get('/seller-applications/{application}', [SellerApplicationController::class, 'show']);
    Route::post('/seller-applications/{application}/approve', [SellerApplicationController::class, 'approve']);
    Route::post('/seller-applications/{application}/reject', [SellerApplicationController::class, 'reject']);
    Route::patch('/vendors/{vendor}/status', [AdminController::class, 'updateSellerStatus']);

    // Payment Gateway Management
    Route::prefix('payment-gateways')->group(function () {
        Route::get('/', [PaymentGatewayController::class, 'index']);
        Route::get('/available', [PaymentGatewayController::class, 'available']);
        Route::post('/', [PaymentGatewayController::class, 'store']);
        Route::put('/{id}', [PaymentGatewayController::class, 'update']);
        Route::post('/{id}/toggle-active', [PaymentGatewayController::class, 'toggleActive']);
        Route::post('/{id}/toggle-test', [PaymentGatewayController::class, 'toggleTestMode']);
        Route::delete('/{id}', [PaymentGatewayController::class, 'destroy']);
    });

    // Eski Rotalar (temizlenebilir)
    // Route::get('/seller-applications', [AdminController::class, 'sellerApplications']);
    // Route::put('/seller-applications/{id}/approve', [AdminController::class, 'approveSellerApplication']);
    // Route::put('/seller-applications/{id}/reject', [AdminController::class, 'rejectSellerApplication']);

    // Diğer Admin Rotaları...
    Route::get('/campaign-stats', [AdminController::class, 'campaignStats']);
    Route::get('/campaign-trend', [AdminController::class, 'campaignTrend']);
    Route::get('/export-campaign-trend', [AdminController::class, 'exportCampaignTrend']);
    Route::get('/invitation-analytics', [InvitationController::class, 'getAnalytics']);
    Route::get('/campaign-scatter-data', [SellerController::class, 'campaignScatterData']);
    Route::get('/segment-heatmap', [SellerController::class, 'segmentHeatmapData']);
    Route::get('/segment-learning-summary', [SellerController::class, 'segmentLearningSummary']);
    Route::get('/segment-accuracy-breakdown', [SellerController::class, 'segmentAccuracyBreakdown']);
    Route::get('/campaign-score-dashboard', [IncentiveCampaignController::class, 'campaignScoreDashboard']);
    Route::get('/campaign-type-analytics', [IncentiveCampaignController::class, 'campaignTypeAnalytics']);
    Route::get('/export-campaign-suggestions', [IncentiveCampaignController::class, 'getExportHistory']);
    Route::get('/suggestion-rules', fn() => Cache::get('suggestion_rules'));
    Route::put('/suggestion-rules', [IncentiveCampaignController::class, 'updateSuggestionRules']);
    Route::post('/refresh-campaign-strategy', [SellerController::class, 'refreshCampaignStrategy']);
    
    // 📋 Campaign Management (Admin)
    Route::get('/campaigns', [\App\Http\Controllers\CampaignController::class, 'adminIndex']);
    Route::post('/campaigns/{id}/apply-segment', [\App\Http\Controllers\CampaignController::class, 'applySegment']);
    
    Route::get('/payment-logs', [PaymentLogController::class, 'index']); // 💳 Ödeme logları
    Route::get('/payment-logs/export', [PaymentLogController::class, 'exportCsv']); // 🧾 CSV export
    Route::post('/payment-logs/{id}/approve', [PaymentLogController::class, 'approve']); // ✅ Onayla
    Route::post('/payment-logs/{id}/reject', [PaymentLogController::class, 'reject']); // ❌ Reddet
    Route::get('/export-logs', [ExportLogController::class, 'index']);
    Route::get('/export-logs/export', [ExportLogController::class, 'export']);
    
    // 🤖 AI Campaign Optimization
    Route::prefix('campaign-ai')->group(function() {
        Route::get('/analyze/{id}', [CampaignAIController::class, 'analyzeCampaign']);
        Route::post('/compare', [CampaignAIController::class, 'compareCampaigns']);
        Route::post('/apply/{id}', [CampaignAIController::class, 'applyRecommendation']);
        Route::get('/predict/{id}', [CampaignAIController::class, 'predictPerformance']);
    });
    
    // 📊 Dynamic Dashboard Modules
    Route::prefix('dashboard-modules')->group(function() {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardModuleController::class, 'index']);
        Route::get('/all-data', [\App\Http\Controllers\Admin\DashboardModuleController::class, 'getAllData']);
        Route::get('/{moduleName}/data', [\App\Http\Controllers\Admin\DashboardModuleController::class, 'getData']);
        Route::put('/{moduleId}/config', [\App\Http\Controllers\Admin\DashboardModuleController::class, 'updateConfig']);
    });
    
    // 🌍 Tour Management
    Route::get('/tours', [AdminTourController::class, 'index']);
    Route::get('/tours/stats', [AdminTourController::class, 'stats']);

    // 🚗 Car Rental Management
    Route::get('/cars', [AdminCarRentalController::class, 'index']);
    Route::get('/cars/stats', [AdminCarRentalController::class, 'stats']);

    // 🛡️ Insurance Management
    Route::get('/insurance', [AdminInsuranceController::class, 'index']);
    Route::get('/insurance/stats', [AdminInsuranceController::class, 'stats']);

    // 🏃 Activity Management
    Route::get('/activities', [AdminActivityController::class, 'index']);
    Route::get('/activities/stats', [AdminActivityController::class, 'stats']);

    // 📈 Admin Dashboard Stats
    Route::get('/stats', [AdminController::class, 'stats']);
    Route::get('/financial-report', [AdminController::class, 'financialReport']);
});

// 🛒 Satıcı Paneli (C2C Multivendor)
// PRODUCTION: Seller auth ve role kontrolü aktif
Route::prefix('seller')->middleware(['auth:sanctum', 'role:seller'])->group(function () {
    Route::get('/stats', [\App\Http\Controllers\SellerController::class, 'stats']);
    Route::get('/products', [\App\Http\Controllers\SellerController::class, 'products']);
    Route::get('/orders', [\App\Http\Controllers\SellerController::class, 'orders']);
});

// 🛍️ Satıcı Ürün Yönetimi (ProductController ile CRUD)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    
    // Stock Management
    Route::post('/products/{product}/stock', [ProductController::class, 'updateStock']);
    Route::post('/products/bulk-stock-update', [ProductController::class, 'bulkUpdateStock']);
    Route::get('/products/{product}/stock-history', [ProductController::class, 'stockHistory']);
    
    // Order Management
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::put('/orders/{order}/ship', [OrderController::class, 'addShipping']);
    
    // Campaign Management
    Route::get('/campaigns', [\App\Http\Controllers\CampaignController::class, 'index']);
    Route::post('/campaigns', [\App\Http\Controllers\CampaignController::class, 'store']);
    Route::get('/campaigns/{campaign}', [\App\Http\Controllers\CampaignController::class, 'show']);
    Route::put('/campaigns/{campaign}', [\App\Http\Controllers\CampaignController::class, 'update']);
    Route::delete('/campaigns/{campaign}', [\App\Http\Controllers\CampaignController::class, 'destroy']);
    Route::post('/campaigns/{campaign}/toggle-active', [\App\Http\Controllers\CampaignController::class, 'toggleActive']);
    Route::get('/campaigns/{campaign}/stats', [\App\Http\Controllers\CampaignController::class, 'stats']);
    
    // Coupon Validation (for customers)
    Route::post('/validate-coupon', [\App\Http\Controllers\CampaignController::class, 'validateCoupon']);
});

// 📨 Mesajlaşma (C2C Multivendor)
Route::middleware(['auth:sanctum'])->prefix('messages')->group(function () {
    Route::get('/', [\App\Http\Controllers\MessageController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\MessageController::class, 'store']);
    Route::put('/{message}/read', [\App\Http\Controllers\MessageController::class, 'markRead']);
});

// ⭐ Değerlendirmeler (C2C Multivendor)
Route::prefix('reviews')->group(function () {
    Route::get('/', [\App\Http\Controllers\ReviewController::class, 'index']);
    
    Route::middleware(['auth:sanctum'])->group(function() {
        Route::post('/', [\App\Http\Controllers\ReviewController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\ReviewController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\ReviewController::class, 'destroy']);
        
        // Review interactions
        Route::post('/{id}/helpful', [\App\Http\Controllers\ReviewController::class, 'markHelpful']);
        Route::post('/{id}/not-helpful', [\App\Http\Controllers\ReviewController::class, 'markNotHelpful']);
        Route::post('/{id}/report', [\App\Http\Controllers\ReviewController::class, 'report']);
    });
});

// ❤️ Favoriler
Route::middleware(['auth:sanctum'])->prefix('favorites')->group(function () {
    Route::get('/', function() {
        return auth()->user()->favorites()->with('product')->get();
    });
    Route::post('/', function(\Illuminate\Http\Request $request) {
        auth()->user()->favorites()->create(['product_id' => $request->product_id]);
        return response()->json(['message' => 'Favorilere eklendi'], 201);
    });
    Route::delete('/{productId}', function($productId) {
        auth()->user()->favorites()->where('product_id', $productId)->delete();
        return response()->json(['message' => 'Favorilerden kaldırıldı']);
    });
});

// 📍 Adresler
Route::middleware(['auth:sanctum'])->prefix('addresses')->group(function () {
    Route::get('/', function() {
        return auth()->user()->addresses;
    });
    Route::post('/', function(\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'full_address' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string'
        ]);
        $address = auth()->user()->addresses()->create($validated);
        return response()->json($address, 201);
    });
    Route::delete('/{id}', function($id) {
        auth()->user()->addresses()->where('id', $id)->delete();
        return response()->json(['message' => 'Adres silindi']);
    });
});

// 👤 Profil & User Management
Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::get('/profile', fn() => response()->json(auth()->user()));
    Route::put('/profile', function(\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . auth()->id(),
            'phone' => 'sometimes|string|max:20',
            'birth_date' => 'sometimes|date'
        ]);
        auth()->user()->update($validated);
        return response()->json(auth()->user());
    });
    
    Route::put('/change-password', function(\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);
        
        if (!\Hash::check($validated['current_password'], auth()->user()->password)) {
            return response()->json(['error' => 'Mevcut şifre hatalı'], 400);
        }
        
        auth()->user()->update(['password' => \Hash::make($validated['new_password'])]);
        return response()->json(['message' => 'Şifre başarıyla değiştirildi']);
    });
    
    Route::get('/notification-preferences', function() {
        return response()->json(auth()->user()->notification_preferences ?? [
            'email_enabled' => true,
            'sms_enabled' => false,
            'campaigns_enabled' => true
        ]);
    });
    
    Route::put('/notification-preferences', function(\Illuminate\Http\Request $request) {
        auth()->user()->update(['notification_preferences' => $request->all()]);
        return response()->json(['message' => 'Bildirim tercihleri güncellendi']);
    });
    
    // Addresses (already exists above, but we add set-default here)
    Route::get('/addresses', function() {
        return response()->json(auth()->user()->addresses ?? []);
    });
    
    Route::post('/addresses', function(\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'full_address' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
            'is_default' => 'boolean'
        ]);
        
        if ($validated['is_default'] ?? false) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }
        
        $address = auth()->user()->addresses()->create($validated);
        return response()->json($address, 201);
    });
    
    Route::put('/addresses/{id}', function(\Illuminate\Http\Request $request, $id) {
        $address = auth()->user()->addresses()->findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'full_address' => 'sometimes|string',
            'city' => 'sometimes|string',
            'district' => 'sometimes|string',
            'postal_code' => 'nullable|string|max:10',
            'is_default' => 'boolean'
        ]);
        
        if ($validated['is_default'] ?? false) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }
        
        $address->update($validated);
        return response()->json($address);
    });
    
    Route::delete('/addresses/{id}', function($id) {
        auth()->user()->addresses()->where('id', $id)->delete();
        return response()->json(['message' => 'Adres silindi']);
    });
    
    Route::post('/addresses/{id}/set-default', function($id) {
        auth()->user()->addresses()->update(['is_default' => false]);
        auth()->user()->addresses()->where('id', $id)->update(['is_default' => true]);
        return response()->json(['message' => 'Varsayılan adres güncellendi']);
    });
});

// 📨 Konuşma Detayı
Route::middleware(['auth:sanctum'])->get('/messages/{userId}', [\App\Http\Controllers\MessageController::class, 'getConversation']);
Route::middleware(['auth:sanctum'])->get('/messages/unread/count', [\App\Http\Controllers\MessageController::class, 'unreadCount']);

// 📁 Kategoriler (C2C Multivendor)
Route::prefix('categories')->group(function () {
    Route::get('/', [\App\Http\Controllers\CategoryController::class, 'index']);
    Route::middleware(['auth:sanctum', 'role:admin'])->post('/', [\App\Http\Controllers\CategoryController::class, 'store']);
});

// 🔧 Admin Dashboard Stats API
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/stats', [AdminStatsController::class, 'getStats']);
    Route::get('/activities', [AdminStatsController::class, 'getActivities']);
    Route::get('/orders/recent', [AdminStatsController::class, 'getRecentOrders']);
    Route::get('/alerts', [AdminStatsController::class, 'getSystemAlerts']);
    Route::get('/revenue-chart', [AdminStatsController::class, 'getRevenueChart']);
    Route::get('/top-sellers', [AdminStatsController::class, 'getTopSellers']);
    Route::get('/top-products', [AdminStatsController::class, 'getTopProducts']);
    
    // Seller Management
    Route::get('/sellers', [AdminSellerController::class, 'index']);
    Route::get('/sellers/stats', [AdminSellerController::class, 'stats']);
    Route::get('/sellers/{vendor}', [AdminSellerController::class, 'show']);
    Route::put('/sellers/{vendor}', [AdminSellerController::class, 'update']);
    Route::post('/sellers/{vendor}/suspend', [AdminSellerController::class, 'suspend']);
    Route::post('/sellers/{vendor}/activate', [AdminSellerController::class, 'activate']);
    Route::post('/sellers/bulk-activate', [AdminSellerController::class, 'bulkActivate']);
    Route::post('/sellers/bulk-suspend', [AdminSellerController::class, 'bulkSuspend']);
    
    // Order Management
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/stats', [AdminOrderController::class, 'stats']);
    Route::get('/orders/{order}', [AdminOrderController::class, 'show']);
    Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
    Route::post('/orders/bulk-update-status', [AdminOrderController::class, 'bulkUpdateStatus']);
    
    // Customer Management
    Route::get('/customers', [AdminCustomerController::class, 'index']);
    Route::get('/customers/stats', [AdminCustomerController::class, 'stats']);
    Route::get('/customers/{customer}', [AdminCustomerController::class, 'show']);
    Route::post('/customers/{customer}/block', [AdminCustomerController::class, 'block']);
    Route::post('/customers/{customer}/unblock', [AdminCustomerController::class, 'unblock']);
    Route::post('/customers/bulk-block', [AdminCustomerController::class, 'bulkBlock']);
    
    // Category Management
    Route::get('/categories', [AdminCategoryController::class, 'index']);
    Route::get('/categories/stats', [AdminCategoryController::class, 'stats']);
    Route::post('/categories', [AdminCategoryController::class, 'store']);
    Route::get('/categories/{category}', [AdminCategoryController::class, 'show']);
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update']);
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy']);
    
    // Banner Management
    Route::get('/banners', [AdminBannerController::class, 'index']);
    Route::get('/banners/stats', [AdminBannerController::class, 'stats']);
    Route::post('/banners', [AdminBannerController::class, 'store']);
    Route::get('/banners/{banner}', [AdminBannerController::class, 'show']);
    Route::post('/banners/{banner}', [AdminBannerController::class, 'update']);
    Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy']);
    Route::post('/banners/{banner}/track-view', [AdminBannerController::class, 'trackView']);
    Route::post('/banners/{banner}/track-click', [AdminBannerController::class, 'trackClick']);
    
    // Page Management
    Route::get('/pages', [AdminPageController::class, 'index']);
    Route::get('/pages/stats', [AdminPageController::class, 'stats']);
    Route::post('/pages', [AdminPageController::class, 'store']);
    Route::get('/pages/by-slug/{slug}', [AdminPageController::class, 'showBySlug']);
    Route::get('/pages/{page}', [AdminPageController::class, 'show']);
    Route::put('/pages/{page}', [AdminPageController::class, 'update']);
    Route::delete('/pages/{page}', [AdminPageController::class, 'destroy']);
    Route::post('/pages/{page}/track-view', [AdminPageController::class, 'trackView']);
    
    // Reports & Analytics
    Route::get('/reports/metrics', [AdminReportController::class, 'metrics']);
    Route::get('/reports/top-products', [AdminReportController::class, 'topProducts']);
    Route::get('/reports/top-sellers', [AdminReportController::class, 'topSellers']);
    Route::get('/reports/daily', [AdminReportController::class, 'daily']);
    Route::get('/reports/summary', [AdminReportController::class, 'summary']);
    Route::get('/reports/export', [AdminReportController::class, 'export']);
    
    // Advanced Reports (new)
    Route::get('/reports/sales', [AdminReportController::class, 'sales']);
    Route::get('/reports/vendor', [AdminReportController::class, 'vendor']);
    Route::get('/reports/product', [AdminReportController::class, 'product']);
    Route::get('/reports/customer', [AdminReportController::class, 'customer']);
    Route::get('/reports/sales/export', [AdminReportController::class, 'export']);
    Route::get('/reports/vendor/export', [AdminReportController::class, 'export']);
    Route::get('/reports/product/export', [AdminReportController::class, 'export']);
    Route::get('/reports/customer/export', [AdminReportController::class, 'export']);
    
    // System Settings
    Route::get('/settings', [AdminSettingsController::class, 'index']);
    Route::post('/settings', [AdminSettingsController::class, 'update']);
    Route::post('/settings/upload-logo', [AdminSettingsController::class, 'uploadLogo']);
    Route::post('/settings/upload-favicon', [AdminSettingsController::class, 'uploadFavicon']);
    Route::post('/settings/test-email', [AdminSettingsController::class, 'testEmail']);
    
    // Notifications
    Route::get('/notifications/email-queue', [AdminNotificationController::class, 'getEmailQueue']);
    Route::post('/notifications/email-queue/{id}/retry', [AdminNotificationController::class, 'retryEmail']);
    Route::get('/notifications/sms-config', [AdminNotificationController::class, 'getSmsConfig']);
    Route::post('/notifications/sms-config', [AdminNotificationController::class, 'saveSmsConfig']);
    Route::get('/notifications/sms-templates', [AdminNotificationController::class, 'getSmsTemplates']);
    Route::post('/notifications/sms-templates', [AdminNotificationController::class, 'storeSmsTemplate']);
    Route::put('/notifications/sms-templates/{id}', [AdminNotificationController::class, 'updateSmsTemplate']);
    Route::post('/notifications/sms-test', [AdminNotificationController::class, 'sendTestSms']);
    Route::get('/notifications/sms-history', [AdminNotificationController::class, 'getSmsHistory']);
    Route::get('/notifications/push-config', [AdminNotificationController::class, 'getPushConfig']);
    Route::post('/notifications/push-config', [AdminNotificationController::class, 'savePushConfig']);
    Route::post('/notifications/push-send', [AdminNotificationController::class, 'sendPushNotification']);
    Route::get('/notifications/push-history', [AdminNotificationController::class, 'getPushHistory']);
    Route::post('/notifications/types', [AdminNotificationController::class, 'updateNotificationType']);
    Route::get('/notifications/recent', [AdminNotificationController::class, 'getRecentNotifications']);
    Route::post('/notifications/test', [AdminNotificationController::class, 'sendTestNotification']);
    
    // Theme Management
    Route::get('/theme', [AdminThemeController::class, 'index']);
    Route::post('/theme', [AdminThemeController::class, 'update']);
});

// 💳 Sanal POS Webhook
Route::post('/webhook/payment', [WebhookController::class, 'handle']);
Route::get('/logs', [LogController::class, 'index']);

// ✅ Sprint Refleksi Durumu
Route::get('/sprint/status', function () {
    $logPath = storage_path('logs/sprint.log');
    $content = File::exists($logPath) ? File::get($logPath) : '';

    return response()->json([
        'ok' => !str_contains($content, '❌'),
        'log' => $content,
    ]);
});
Route::middleware(['auth:sanctum', 'can.viewExportLogs'])->get('/api/admin/payment-export-logs', function (Request $request) {
    $query = \App\Models\PaymentExportLog::query();

    if ($request->filled('search')) {
        $query->where('filename', 'like', "%{$request->search}%")
              ->orWhere('admin_id', $request->search);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('exported_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('exported_at', '<=', $request->date_to);
    }

    return $query->orderByDesc('exported_at')->paginate(50);
});

// ============================================
// API Version 1 (Current Stable)

// Unified Dashboard Routes
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/recent-activities', [UnifiedDashboardController::class, 'recentActivities']);
    Route::get('/active-campaigns', [UnifiedDashboardController::class, 'activeCampaigns']);
    Route::get('/performance', [UnifiedDashboardController::class, 'performance']);
    Route::get('/top-sellers', [UnifiedDashboardController::class, 'topSellers']);
    Route::get('/pending-orders', [UnifiedDashboardController::class, 'pendingOrders']);
});

// Nearby Businesses
Route::get('/nearby-businesses', [NearbyBusinessController::class, 'index']);

// ============================================
// Food, Hotel, Transport, Blog Management Routes
// ============================================

use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\TransportController;
use App\Http\Controllers\Admin\BlogController;

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    
    // 🍕 Restaurant/Food Management
    Route::prefix('restaurants')->group(function () {
        Route::get('/', [RestaurantController::class, 'index']);
        Route::get('/stats', [RestaurantController::class, 'stats']);
        Route::post('/', [RestaurantController::class, 'store']);
        Route::get('/{restaurant}', [RestaurantController::class, 'show']);
        Route::put('/{restaurant}', [RestaurantController::class, 'update']);
        Route::delete('/{restaurant}', [RestaurantController::class, 'destroy']);
        Route::post('/{restaurant}/toggle-status', [RestaurantController::class, 'toggleStatus']);
        Route::post('/{restaurant}/toggle-featured', [RestaurantController::class, 'toggleFeatured']);
        
        // Menu Items
        Route::get('/{restaurant}/menu-items', [RestaurantController::class, 'menuItems']);
        Route::post('/{restaurant}/menu-items', [RestaurantController::class, 'storeMenuItem']);
        Route::put('/menu-items/{menuItem}', [RestaurantController::class, 'updateMenuItem']);
        Route::delete('/menu-items/{menuItem}', [RestaurantController::class, 'destroyMenuItem']);
    });
    
    // Food Orders
    Route::prefix('food-orders')->group(function () {
        Route::get('/', [RestaurantController::class, 'orders']);
        Route::get('/stats', [RestaurantController::class, 'orderStats']);
        Route::get('/{order}', [RestaurantController::class, 'showOrder']);
        Route::put('/{order}/status', [RestaurantController::class, 'updateOrderStatus']);
    });

    // 🏨 Hotel Management
    Route::prefix('hotels')->group(function () {
        Route::get('/', [HotelController::class, 'index']);
        Route::get('/stats', [HotelController::class, 'stats']);
        Route::post('/', [HotelController::class, 'store']);
        Route::get('/{hotel}', [HotelController::class, 'show']);
        Route::put('/{hotel}', [HotelController::class, 'update']);
        Route::delete('/{hotel}', [HotelController::class, 'destroy']);
        Route::post('/{hotel}/toggle-status', [HotelController::class, 'toggleStatus']);
        Route::post('/{hotel}/toggle-featured', [HotelController::class, 'toggleFeatured']);
        
        // Rooms
        Route::get('/{hotel}/rooms', [HotelController::class, 'rooms']);
        Route::post('/{hotel}/rooms', [HotelController::class, 'storeRoom']);
        Route::put('/rooms/{room}', [HotelController::class, 'updateRoom']);
        Route::delete('/rooms/{room}', [HotelController::class, 'destroyRoom']);
        
        // Availability
        Route::get('/{hotel}/availability', [HotelController::class, 'availability']);
    });
    
    // Reservations
    Route::prefix('reservations')->group(function () {
        Route::get('/', [HotelController::class, 'reservations']);
        Route::get('/stats', [HotelController::class, 'reservationStats']);
        Route::get('/calendar', [HotelController::class, 'reservationCalendar']);
        Route::get('/{reservation}', [HotelController::class, 'showReservation']);
        Route::put('/{reservation}/status', [HotelController::class, 'updateReservationStatus']);
        Route::post('/{reservation}/cancel', [HotelController::class, 'cancelReservation']);
    });

    // 🚗 Transport Management
    Route::prefix('vehicles')->group(function () {
        Route::get('/', [TransportController::class, 'vehicles']);
        Route::post('/', [TransportController::class, 'storeVehicle']);
        Route::get('/{vehicle}', [TransportController::class, 'showVehicle']);
        Route::put('/{vehicle}', [TransportController::class, 'updateVehicle']);
        Route::delete('/{vehicle}', [TransportController::class, 'destroyVehicle']);
        Route::post('/{vehicle}/toggle-availability', [TransportController::class, 'toggleVehicleAvailability']);
    });
    
    Route::prefix('drivers')->group(function () {
        Route::get('/', [TransportController::class, 'drivers']);
        Route::post('/', [TransportController::class, 'storeDriver']);
        Route::get('/{driver}', [TransportController::class, 'showDriver']);
        Route::put('/{driver}', [TransportController::class, 'updateDriver']);
        Route::delete('/{driver}', [TransportController::class, 'destroyDriver']);
        Route::post('/{driver}/verify', [TransportController::class, 'verifyDriver']);
        Route::post('/{driver}/toggle-availability', [TransportController::class, 'toggleDriverAvailability']);
    });
    
    Route::prefix('transfers')->group(function () {
        Route::get('/', [TransportController::class, 'transfers']);
        Route::get('/stats', [TransportController::class, 'transferStats']);
        Route::get('/today', [TransportController::class, 'todayTransfers']);
        Route::get('/{transfer}', [TransportController::class, 'showTransfer']);
        Route::put('/{transfer}/status', [TransportController::class, 'updateTransferStatus']);
        Route::post('/{transfer}/assign-driver', [TransportController::class, 'assignDriver']);
        Route::post('/{transfer}/cancel', [TransportController::class, 'cancelTransfer']);
    });
    
    Route::prefix('routes')->group(function () {
        Route::get('/', [TransportController::class, 'routes']);
        Route::post('/', [TransportController::class, 'storeRoute']);
        Route::get('/{route}', [TransportController::class, 'showRoute']);
        Route::put('/{route}', [TransportController::class, 'updateRoute']);
        Route::delete('/{route}', [TransportController::class, 'destroyRoute']);
        Route::post('/{route}/toggle-active', [TransportController::class, 'toggleRouteActive']);
        Route::post('/{route}/toggle-popular', [TransportController::class, 'toggleRoutePopular']);
    });
    
    // Transport Stats
    Route::get('/transport/stats', [TransportController::class, 'stats']);

    // 📝 Blog Management
    Route::prefix('blog')->group(function () {
        // Categories
        Route::get('/categories', [BlogController::class, 'categories']);
        Route::post('/categories', [BlogController::class, 'storeCategory']);
        Route::put('/categories/{category}', [BlogController::class, 'updateCategory']);
        Route::delete('/categories/{category}', [BlogController::class, 'destroyCategory']);
        
        // Posts
        Route::get('/posts', [BlogController::class, 'posts']);
        Route::get('/posts/stats', [BlogController::class, 'stats']);
        Route::post('/posts', [BlogController::class, 'storePost']);
        Route::get('/posts/{post}', [BlogController::class, 'showPost']);
        Route::put('/posts/{post}', [BlogController::class, 'updatePost']);
        Route::delete('/posts/{post}', [BlogController::class, 'destroyPost']);
        Route::post('/posts/{post}/publish', [BlogController::class, 'publishPost']);
        Route::post('/posts/{post}/unpublish', [BlogController::class, 'unpublishPost']);
        Route::post('/posts/{post}/toggle-featured', [BlogController::class, 'toggleFeatured']);
    });
});

// Public Blog API (no auth required)
Route::prefix('blog')->group(function () {
    Route::get('/categories', [BlogController::class, 'publicCategories']);
    Route::get('/posts', [BlogController::class, 'publicPosts']);
    Route::get('/posts/featured', [BlogController::class, 'featuredPosts']);
    Route::get('/posts/{slug}', [BlogController::class, 'publicShowPost']);
});

// Public Food/Restaurant API
Route::prefix('restaurants')->group(function () {
    Route::get('/', [RestaurantController::class, 'publicIndex']);
    Route::get('/{slug}', [RestaurantController::class, 'publicShow']);
    Route::get('/{restaurant}/menu', [RestaurantController::class, 'publicMenu']);
});

// Public Hotel API
Route::prefix('hotels')->group(function () {
    Route::get('/', [HotelController::class, 'publicIndex']);
    Route::get('/{slug}', [HotelController::class, 'publicShow']);
    Route::get('/{hotel}/rooms', [HotelController::class, 'publicRooms']);
    Route::post('/{hotel}/check-availability', [HotelController::class, 'checkAvailability']);
});

// Public Transport API
Route::prefix('transport')->group(function () {
    Route::get('/routes', [TransportController::class, 'publicRoutes']);
    Route::get('/routes/{slug}', [TransportController::class, 'publicShowRoute']);
    Route::post('/calculate-price', [TransportController::class, 'calculatePrice']);
});
