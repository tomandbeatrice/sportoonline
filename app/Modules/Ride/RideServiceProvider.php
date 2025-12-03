<?php

namespace App\Modules\Ride;

use Illuminate\Support\ServiceProvider;

class RideServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Services\TransferService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/ride.php');
    }
}
