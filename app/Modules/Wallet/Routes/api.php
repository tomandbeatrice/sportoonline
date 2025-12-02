<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Wallet\Controllers\WalletController;

/*
|--------------------------------------------------------------------------
| Wallet Module Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware(['api'])->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::prefix('wallet')->name('wallet.')->group(function () {
            Route::get('/', [WalletController::class, 'index'])->name('index');
            Route::post('/deposit', [WalletController::class, 'deposit'])->name('deposit');
            Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
        });
    });
});
