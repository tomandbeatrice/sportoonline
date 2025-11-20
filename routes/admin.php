<?php

use Illuminate\Support\Facades\Route;

// Admin Controller'lar
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\AdminInvoiceExportController;
use App\Http\Controllers\Admin\InvoiceExportController;
use App\Http\Controllers\Admin\ExportLogController;


// 🛡️ Admin erişimi için middleware
Route::middleware(['auth', 'is_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // 📦 Siparişler
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('orders/{order}/note', [OrderController::class, 'addNote'])->name('orders.addNote');
        Route::get('orders/export/csv', [OrderController::class, 'export'])->name('orders.export');
        Route::get('orders/{order}/invoice-preview', [OrderController::class, 'invoicePreview'])->name('orders.invoice.preview');
        Route::get('orders/{order}/invoice-pdf', [OrderController::class, 'downloadInvoice'])->name('orders.invoice.pdf');

        // 📤 Vendor filtreli export (ZIP)
        Route::post('invoices/export-zip', [InvoiceExportController::class, 'exportZip'])->name('invoices.exportZip');

        // 📥 Admin/vendor filtreli toplu export
        Route::post('invoices/bulk-download', [AdminInvoiceExportController::class, 'bulkDownload'])->name('invoices.bulkDownload');

        // 🧾 Export UI (Blade form desteği)
        Route::get('invoices/export', [AdminInvoiceExportController::class, 'exportForm'])->name('invoices.exportForm');

        // 📊 Export geçmişi & log ekranları
        Route::get('invoices/exports', [InvoiceExportController::class, 'listExports'])->name('invoices.exports');
        Route::get('invoices/logs', [InvoiceExportController::class, 'listLogs'])->name('invoices.logs');

        // 📥 ZIP indirme işlemi (token bazlı)
        Route::get('invoices/download/{token}', [InvoiceExportController::class, 'download'])->name('invoices.download');
        Route::get('invoices/download-by-token/{token}', [AdminInvoiceExportController::class, 'downloadByToken'])->name('invoices.downloadByToken');
    
    
     Route::get('/logs/export-activity', [ExportLogController::class, 'exportActivity'])
    ->name('admin.logs.exportActivity');
    
    
    }); 

    
