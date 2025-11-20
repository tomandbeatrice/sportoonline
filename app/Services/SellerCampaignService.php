<?php

namespace App\Services;

use App\Models\Seller;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SellerCampaignService
{
    public static function evaluate($sellerId)
    {
        $settings = Cache::get('incentive_settings');

        if (!$settings['active']) return;

        $hasOutletProduct = Product::where('vendor_id', $sellerId)
            ->where('category_id', $settings['outlet_category_id'])
            ->exists();

        if ($hasOutletProduct) {
            $seller = Seller::find($sellerId);
            $seller->commission_rate = 100 - $settings['commission_discount'];
            $seller->save();

            Log::channel('incentive')->info('Komisyon indirimi uygulandı', [
                'seller_id' => $sellerId,
                'new_rate' => $seller->commission_rate,
                'timestamp' => now()->toIso8601String()
            ]);
        }
    }
}