<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Checkout Flow', function () {
    
    it('allows authenticated user to complete checkout with valid cart', function () {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 10
        ]);
        
        // Act - Add to cart
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', [
                'product_id' => $product->id,
                'quantity' => 2
            ]);
        
        $response->assertStatus(200);
        
        // Act - Checkout
        $checkoutResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'payment_method' => 'stripe',
                'address_id' => 1,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2
                    ]
                ]
            ]);
        
        // Assert
        $checkoutResponse->assertStatus(200);
        expect($checkoutResponse->json('success'))->toBeTrue();
        
        // Verify order created in database
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_price' => 200.00
        ]);
    });
    
    it('prevents checkout with out of stock products', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Out of Stock Product',
            'price' => 50.00,
            'stock' => 0
        ]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', [
                'product_id' => $product->id,
                'quantity' => 1
            ]);
        
        $response->assertStatus(422);
        expect($response->json('message'))->toContain('stock');
    });
    
    it('calculates correct total with multiple items', function () {
        $user = User::factory()->create();
        $product1 = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        $product2 = Product::factory()->create(['price' => 50.00, 'stock' => 10]);
        
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', [
                'product_id' => $product1->id,
                'quantity' => 2
            ]);
        
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', [
                'product_id' => $product2->id,
                'quantity' => 3
            ]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/cart');
        
        $response->assertStatus(200);
        $expectedTotal = (100.00 * 2) + (50.00 * 3); // 350.00
        expect($response->json('total'))->toBe($expectedTotal);
    });
    
    it('requires authentication for checkout', function () {
        $response = $this->postJson('/api/v1/orders', [
            'payment_method' => 'stripe',
            'items' => []
        ]);
        
        $response->assertStatus(401);
    });
    
});

describe('Payment Processing', function () {
    
    it('creates payment record when order is placed', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/orders', [
                'payment_method' => 'stripe',
                'address_id' => 1,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1]
                ]
            ]);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'amount' => 100.00,
            'payment_method' => 'stripe'
        ]);
    });
    
    it('handles failed payment gracefully', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        
        // Simulate payment failure by using invalid card token
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/payments/initialize', [
                'order_id' => 1,
                'payment_method' => 'stripe',
                'card_token' => 'tok_invalid'
            ]);
        
        // Should return error but not crash
        expect($response->status())->toBeIn([400, 422, 500]);
        expect($response->json())->toHaveKey('error');
    });
    
});

describe('Order Management', function () {
    
    it('allows user to view their orders', function () {
        $user = User::factory()->create();
        Order::factory()->count(3)->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/orders');
        
        $response->assertStatus(200);
        expect($response->json('data'))->toHaveCount(3);
    });
    
    it('prevents users from viewing others orders', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user2->id]);
        
        $response = $this->actingAs($user1, 'sanctum')
            ->getJson("/api/v1/orders/{$order->id}");
        
        $response->assertStatus(403);
    });
    
    it('allows order cancellation within allowed time', function () {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'created_at' => now()
        ]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/orders/{$order->id}/cancel");
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled'
        ]);
    });
    
});
