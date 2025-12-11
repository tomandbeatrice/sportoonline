<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CampaignRollbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expired = Product::whereNotNull('campaign_end')
            ->where('campaign_end', '<', now())
            ->get();

        foreach ($expired as $product) {
            $product->update([
                'discount_rate' => 0,
                'campaign_tag' => null,
                'campaign_start' => null,
                'campaign_end' => null
            ]);

            Log::channel('incentive')->info('Kampanya geri alındı', [
                'product_id' => $product->id,
                'seller_id' => $product->seller_id,
                'timestamp' => now()->toIso8601String()
            ]);
        }
    }
}
