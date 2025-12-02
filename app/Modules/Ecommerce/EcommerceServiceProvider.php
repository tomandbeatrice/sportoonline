<?php

namespace App\Modules\Ecommerce;

use Illuminate\Support\ServiceProvider;

/**
 * Ecommerce Module Service Provider
 * 
 * This module handles all ecommerce-related functionality:
 * - Products
 * - Orders
 * - Cart
 * - Checkout
 * - Payments
 */
class EcommerceServiceProvider extends ServiceProvider
{
    /**
     * Register module services
     */
    public function register(): void
    {
        // Register module services
        $this->app->singleton(\App\Modules\Ecommerce\Services\ProductService::class);
        $this->app->singleton(\App\Modules\Ecommerce\Services\OrderService::class);
        $this->app->singleton(\App\Modules\Ecommerce\Services\CartService::class);
    }

    /**
     * Bootstrap module services
     */
    public function boot(): void
    {
        // Load module routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/ecommerce.php');
        
        // Load migrations if needed
        // $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        
        // Register module config
        $this->publishes([
            __DIR__ . '/Config/ecommerce.php' => config_path('ecommerce.php'),
        ], 'ecommerce-config');
    }
}
