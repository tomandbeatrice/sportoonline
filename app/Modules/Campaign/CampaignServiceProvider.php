<?php

namespace App\Modules\Campaign;

use Illuminate\Support\ServiceProvider;

/**
 * Campaign Module Service Provider
 */
class CampaignServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Modules\Campaign\Services\CampaignService::class);
    }

    public function boot(): void
    {
        // Module routes will be loaded here if needed
    }
}
