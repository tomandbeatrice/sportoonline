<?php

use App\Models\User;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\CampaignUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Campaign Flow', function () {
    
    it('lists active campaigns', function () {
        Campaign::factory()->count(3)->create(['status' => 'active']);
        Campaign::factory()->create(['status' => 'inactive']);
        
        $response = $this->getJson('/api/v1/campaigns');
        
        $response->assertStatus(200);
        expect($response->json('data'))->toHaveCount(3);
    });
    
    it('applies discount campaign to order', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);
        
        $campaign = Campaign::factory()->create([
            'type' => 'discount',
            'discount_type' => 'percentage',
            'discount_value' => 20, // 20% off
            'status' => 'active'
        ]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1]
                ],
                'campaign_code' => $campaign->code,
                'shipping_info' => [
                    'address' => 'Test Address',
                    'city' => 'Istanbul',
                    'zip' => '34000'
                ]
            ]);
        
        $response->assertStatus(201);
        
        // Original: 100, After 20% discount: 80
        expect($response->json('data.total_price'))->toBe(80.00);
    });
    
    it('validates campaign usage limit', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);
        
        $campaign = Campaign::factory()->create([
            'usage_limit' => 1,
            'status' => 'active'
        ]);
        
        // Use campaign once
        CampaignUsage::factory()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id
        ]);
        
        // Try to use again
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1]
                ],
                'campaign_code' => $campaign->code,
                'shipping_info' => [
                    'address' => 'Test', 'city' => 'Istanbul', 'zip' => '34000'
                ]
            ]);
        
        expect($response->status())->toBe(422);
        expect($response->json('message'))->toContain('limit');
    });
    
    it('checks campaign date validity', function () {
        $campaign = Campaign::factory()->create([
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(10),
            'status' => 'active'
        ]);
        
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'items' => [['product_id' => $product->id, 'quantity' => 1]],
                'campaign_code' => $campaign->code,
                'shipping_info' => [
                    'address' => 'Test', 'city' => 'Istanbul', 'zip' => '34000'
                ]
            ]);
        
        expect($response->status())->toBe(422);
        expect($response->json('message'))->toContain('not yet active');
    });
    
    it('applies buy_x_get_y campaign', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 50.00, 'stock' => 10]);
        
        $campaign = Campaign::factory()->create([
            'type' => 'buy_x_get_y',
            'min_quantity' => 2,
            'free_quantity' => 1,
            'status' => 'active'
        ]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 3]
                ],
                'campaign_code' => $campaign->code,
                'shipping_info' => [
                    'address' => 'Test', 'city' => 'Istanbul', 'zip' => '34000'
                ]
            ]);
        
        $response->assertStatus(201);
        
        // Buy 2 get 1 free: Pay for 2 only
        expect($response->json('data.total_price'))->toBe(100.00);
    });
    
    it('tracks campaign analytics', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);
        
        $campaign = Campaign::factory()->create(['status' => 'active']);
        
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'items' => [['product_id' => $product->id, 'quantity' => 1]],
                'campaign_code' => $campaign->code,
                'shipping_info' => [
                    'address' => 'Test', 'city' => 'Istanbul', 'zip' => '34000'
                ]
            ]);
        
        $this->assertDatabaseHas('campaign_usages', [
            'campaign_id' => $campaign->id,
            'user_id' => $user->id
        ]);
        
        // Check campaign stats updated
        $campaign->refresh();
        expect($campaign->total_usage)->toBe(1);
    });
    
    it('seller can create campaign for their products', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        
        $response = $this->actingAs($seller, 'sanctum')
            ->postJson('/api/v1/seller/campaigns', [
                'name' => 'Flash Sale',
                'type' => 'discount',
                'discount_type' => 'percentage',
                'discount_value' => 30,
                'start_date' => now(),
                'end_date' => now()->addDays(7)
            ]);
        
        $response->assertStatus(201);
        
        $this->assertDatabaseHas('campaigns', [
            'name' => 'Flash Sale',
            'seller_id' => $seller->id
        ]);
    });
    
});
