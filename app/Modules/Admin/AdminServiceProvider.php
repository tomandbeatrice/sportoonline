<?php

namespace App\Modules\Admin;

use Illuminate\Support\ServiceProvider;

/**
 * Admin Module Service Provider
 * 
 * Handles admin panel functionality
 */
class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Modules\Admin\Services\AdminStatsService::class);
    }

    public function boot(): void
    {
        // Module routes will be loaded here
    }
}
