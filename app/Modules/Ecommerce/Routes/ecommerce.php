<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProductController;

/*
|--------------------------------------------------------------------------
| Ecommerce Module Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    // Products (using new modular controller)
    Route::prefix('products')->name('v1.products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/{id}', [ProductController::class, 'show'])->name('show');
        
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/', [ProductController::class, 'store'])->name('store');
        });
    });
    
});
