<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Payment Flow', function () {
    
    it('initializes payment for stripe', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_price' => 100.00,
            'status' => 'pending'
        ]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/payments/initialize', [
                'order_id' => $order->id,
                'payment_method' => 'stripe'
            ]);
        
        expect($response->status())->toBeIn([200, 201]);
        expect($response->json())->toHaveKeys(['success', 'data']);
    });
    
    it('creates payment record on initialization', function () {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_price' => 150.00
        ]);
        
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/payments/initialize', [
                'order_id' => $order->id,
                'payment_method' => 'iyzico'
            ]);
        
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => 'iyzico'
        ]);
    });
    
    it('prevents duplicate payment initialization', function () {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_price' => 100.00,
            'status' => 'completed'
        ]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/payments/initialize', [
                'order_id' => $order->id,
                'payment_method' => 'stripe'
            ]);
        
        expect($response->status())->toBe(422);
        expect($response->json('message'))->toContain('already');
    });
    
    it('validates payment gateway availability', function () {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/payments/initialize', [
                'order_id' => $order->id,
                'payment_method' => 'invalid_gateway'
            ]);
        
        $response->assertStatus(422);
    });
    
    it('processes successful payment callback', function () {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);
        
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'status' => 'pending'
        ]);
        
        $response = $this->postJson('/api/v1/payments/callback', [
            'transaction_id' => $payment->transaction_id,
            'status' => 'success'
        ]);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'completed'
        ]);
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing'
        ]);
    });
    
    it('handles failed payment callback', function () {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'status' => 'pending'
        ]);
        
        $this->postJson('/api/v1/payments/callback', [
            'transaction_id' => $payment->transaction_id,
            'status' => 'failed'
        ]);
        
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'failed'
        ]);
    });
    
    it('supports refund for completed payments', function () {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'status' => 'completed',
            'amount' => 100.00
        ]);
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/payments/refund', [
                'transaction_id' => $payment->transaction_id,
                'amount' => 50.00,
                'reason' => 'Customer request'
            ]);
        
        expect($response->status())->toBeIn([200, 201]);
        
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'type' => 'refund',
            'amount' => 50.00
        ]);
    });
    
});

describe('Payment Gateway Integration', function () {
    
    it('stripe gateway is available', function () {
        $service = app(\App\Services\StripeService::class);
        expect($service)->toBeInstanceOf(\App\Contracts\PaymentGatewayInterface::class);
        expect($service->isAvailable())->toBeTrue();
    });
    
    it('iyzico gateway is available', function () {
        $service = app(\App\Services\IyzicoService::class);
        expect($service)->toBeInstanceOf(\App\Contracts\PaymentGatewayInterface::class);
    });
    
    it('paytr gateway is available', function () {
        $service = app(\App\Services\PaytrService::class);
        expect($service)->toBeInstanceOf(\App\Contracts\PaymentGatewayInterface::class);
    });
    
});
