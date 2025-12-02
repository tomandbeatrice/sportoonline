<?php

namespace App\Modules\Campaign\Services;

use App\Models\Campaign;
use Illuminate\Support\Facades\Cache;

/**
 * Campaign Service
 * Handles campaign business logic
 */
class CampaignService
{
    /**
     * Get all active campaigns
     */
    public function getActiveCampaigns()
    {
        return Cache::remember('campaigns.active', 900, function () {
            return Campaign::where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->get();
        });
    }
    
    /**
     * Get campaign by ID
     */
    public function getCampaign(int $id): ?Campaign
    {
        return Cache::remember("campaign.{$id}", 900, function () use ($id) {
            return Campaign::find($id);
        });
    }
    
    /**
     * Create campaign
     */
    public function createCampaign(array $data): Campaign
    {
        $campaign = Campaign::create($data);
        
        $this->clearCache();
        
        return $campaign;
    }
    
    /**
     * Update campaign
     */
    public function updateCampaign(int $id, array $data): Campaign
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->update($data);
        
        Cache::forget("campaign.{$id}");
        $this->clearCache();
        
        return $campaign->fresh();
    }
    
    /**
     * Clear campaign cache
     */
    protected function clearCache(): void
    {
        Cache::forget('campaigns.active');
    }
}
