<?php

use Illuminate\Support\Facades\Route;

// 🧠 Admin Panel Modülleri
use App\Http\Controllers\Admin\{
    ScheduledExportController,
    SystemBrainController,
    LogController,
    VendorExportController,
    VendorBrandingController,
    BrandingExportController,
    ExportLogController,
    ExportFileController,
    SegmentSheetExportController,
    VendorBrandingDashboardController,
    BrandingImportController,
    VendorBrandingExportController,
    SegmentExportController,
    SegmentExportSchedulerController
};

use App\Http\Controllers\ProductController;

// ==================== ✅ Vue Panel Ana Sayfa ====================
Route::get('/', fn() => view('index')); // Vue bileşeni burada render edilir

// ==================== ✅ Canlı Sipariş Sayfası ====================
Route::get('/live-order', fn() => view('live-order'));

// ==================== ✅ Admin Routes ====================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'isAdmin'])->group(function () {

    // 📁 Export Dosyaları
    Route::prefix('export-files')->name('export.files.')->group(function () {
        Route::get('/', [ExportFileController::class, 'index'])->name('index');
        Route::get('download/{id}', [ExportFileController::class, 'download'])->name('download');
        Route::post('sync', [ExportFileController::class, 'syncStorage'])->name('sync');
        Route::post('cleanup', [ExportFileController::class, 'cleanupDeletedFiles'])->name('cleanup');
        Route::post('{id}/retry', [ExportFileController::class, 'retry'])->name('retry');
    });

    // 🧠 Segment Export Planlama ve Listeleme
    Route::post('schedule-segment-export', [SegmentExportSchedulerController::class, 'schedule'])->name('segment.export.schedule');
    Route::get('scheduled-exports', [ScheduledExportController::class, 'list'])->name('scheduled.exports.list');
    Route::delete('scheduled-exports/{segmentId}/{day}/{time}', [ScheduledExportController::class, 'delete'])->name('scheduled.exports.delete');

    // 🔧 Vendor Branding
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('branding', [VendorBrandingController::class, 'index'])->name('branding.index');
        Route::put('branding/{vendor}', [VendorBrandingController::class, 'update'])->name('branding.update');
        Route::get('branding-dashboard', [VendorBrandingDashboardController::class, 'index'])->name('branding.dashboard');
        Route::get('branding-preview/{vendor}', [VendorBrandingController::class, 'preview'])->name('branding.preview');
        Route::get('branding/export', [BrandingExportController::class, 'export'])->name('branding.export');
        Route::get('branding/export-vendor', [VendorExportController::class, 'exportBranding'])->name('branding.export.vendor');
        Route::post('branding/import', [BrandingImportController::class, 'import'])->name('branding.import');
        Route::get('branding/cleanup', [BrandingExportController::class, 'weeklyCleanup'])->name('branding.cleanup');
        Route::get('branding/logs', [BrandingExportController::class, 'index'])->name('branding.logs');
        Route::get('branding/export-admin', [VendorBrandingExportController::class, 'export'])->name('branding.export.admin');
    });

    // 📊 Segment bazlı Excel export
    Route::get('segment/export', [SegmentSheetExportController::class, 'export'])->name('segment.export');

    // 📊 Export Log Analizi
    Route::get('export-logs', [ExportLogController::class, 'index'])->name('export.logs');

    // 📡 Canlı Log Takibi
    Route::get('logs/live', [LogController::class, 'live'])->name('logs.live');

    // 🧠 Segment Export Tetikleme
    Route::post('segment-export', [SegmentExportController::class, 'export'])->name('segment.export.trigger');

    // 📜 Export Geçmişi
    Route::get('segment-export-history', [SegmentExportController::class, 'history'])->name('segment.export.history');

    // 🧠 SystemBrain Paneli
    Route::get('systembrain-status', [SystemBrainController::class, 'status'])->name('systembrain.status');
});

// ==================== ✅ Vendor Ürün Yönetimi ====================
Route::middleware(['auth'])->prefix('vendor')->group(function () {
    Route::get('/products/create', [ProductController::class, 'create']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('vendor.products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('vendor.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('vendor.products.destroy');
});

// ==================== ✅ SPA Fallback (Vue Router için) ====================
Route::fallback(fn() => view('index'));