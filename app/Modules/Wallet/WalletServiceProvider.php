<?php

namespace App\Modules\Wallet;

use Illuminate\Support\ServiceProvider;

/**
 * Wallet Module Service Provider
 * 
 * This module handles all wallet-related functionality:
 * - Wallet management
 * - Transactions
 * - Deposits and withdrawals
 * - Internal transfers
 */
class WalletServiceProvider extends ServiceProvider
{
    /**
     * Register module services
     */
    public function register(): void
    {
        // Register module services
        $this->app->singleton(\App\Modules\Wallet\Services\WalletService::class);
    }

    /**
     * Bootstrap module services
     */
    public function boot(): void
    {
        // Load module routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }
}
