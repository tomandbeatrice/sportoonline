<?php

namespace App\Modules\Seller;

use Illuminate\Support\ServiceProvider;

/**
 * Seller Module Service Provider
 */
class SellerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Modules\Seller\Services\SellerService::class);
    }

    public function boot(): void
    {
        // Module routes will be loaded here if needed
    }
}
