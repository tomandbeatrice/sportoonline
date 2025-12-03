<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Payment;
use App\Observers\PaymentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Module Service Providers
        $this->app->register(\App\Modules\Ecommerce\EcommerceServiceProvider::class);
        $this->app->register(\App\Modules\Campaign\CampaignServiceProvider::class);
        $this->app->register(\App\Modules\Seller\SellerServiceProvider::class);
        $this->app->register(\App\Modules\Admin\AdminServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 💳 Ödeme durumu değiştiğinde observer tetiklenir
        Payment::observe(PaymentObserver::class);
    }
}