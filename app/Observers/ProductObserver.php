<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\SellerCampaignService;

class ProductObserver
{
    public function updated(Product $product)
    {
        $settings = Cache::get('incentive_settings');

        if (!($settings['active'] ?? false)) {
            return;
        }

        $discountRate = $product->discount_rate ?? 0;

        if ($discountRate >= $settings['min_discount']) {
            // Outlet kategorisine geçiş
            $product->category_id = $settings['outlet_category_id'];
            $product->save();

            Log::channel('incentive')->info('Outlet geçişi tetiklendi', [
                'product_id' => $product->id,
                'discount_rate' => $discountRate,
                'vendor_id' => $product->vendor_id,
                'timestamp' => now()->toIso8601String()
            ]);

            // Komisyon indirimi tetikleniyor
            SellerCampaignService::evaluate($product->vendor_id);
        }
    }
}