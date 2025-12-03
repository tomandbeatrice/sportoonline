<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Ride\Controllers\TransferController;

Route::prefix('api/v1')->group(function () {
    Route::prefix('transfers')->group(function () {
        Route::get('/suggestions', [TransferController::class, 'getSuggestions']);
        Route::get('/available', [TransferController::class, 'getAvailable']);
        Route::post('/book', [TransferController::class, 'createBooking']);
    });
});
