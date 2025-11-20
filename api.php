<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\AdminController;

// Demo Ortamı Endpoint'leri
Route::prefix('demo')->name('demo.')->group(function () {
    Route::get('/products', [DemoController::class, 'products'])->name('products');
    Route::get('/reviews', [DemoController::class, 'reviews'])->name('reviews');
    Route::post('/checkout', [DemoController::class, 'checkout'])->name('checkout');
});

// Admin Panel Endpoint'leri (giriş ve rol kontrolü ile)
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->name('admin.')->group(function () {
    Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/products', [AdminController::class, 'products']);
    Route::get('/orders', [AdminController::class, 'orders']);
    Route::get('/categories', [AdminController::class, 'categories']);
});