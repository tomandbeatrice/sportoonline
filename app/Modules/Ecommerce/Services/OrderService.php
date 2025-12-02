<?php

namespace App\Modules\Ecommerce\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Modules\Ecommerce\Services\ProductService;
use Illuminate\Support\Facades\DB;

/**
 * Order Service
 * Handles order creation and management
 */
class OrderService
{
    public function __construct(
        protected ProductService $productService
    ) {}
    
    /**
     * Create new order
     */
    public function createOrder(int $userId, array $items, array $shippingInfo): Order
    {
        return DB::transaction(function () use ($userId, $items, $shippingInfo) {
            // Calculate total
            $total = 0;
            foreach ($items as $item) {
                $product = $this->productService->getProduct($item['product_id']);
                
                if (!$product) {
                    throw new \Exception("Product {$item['product_id']} not found");
                }
                
                // Check stock
                if (!$this->productService->checkStock($item['product_id'], $item['quantity'])) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }
                
                $total += $product->price * $item['quantity'];
            }
            
            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'total_price' => $total,
                'status' => 'pending',
                'shipping_address' => $shippingInfo['address'] ?? null,
                'shipping_city' => $shippingInfo['city'] ?? null,
                'shipping_zip' => $shippingInfo['zip'] ?? null,
            ]);
            
            // Create order items and reduce stock
            foreach ($items as $item) {
                $product = $this->productService->getProduct($item['product_id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $product->price * $item['quantity'],
                ]);
                
                // Reduce stock
                $this->productService->reduceStock($item['product_id'], $item['quantity']);
            }
            
            return $order->fresh(['items', 'user']);
        });
    }
    
    /**
     * Cancel order
     */
    public function cancelOrder(int $orderId): Order
    {
        return DB::transaction(function () use ($orderId) {
            $order = Order::with('items')->findOrFail($orderId);
            
            if ($order->status !== 'pending') {
                throw new \Exception("Cannot cancel order with status: {$order->status}");
            }
            
            // Restore stock
            foreach ($order->items as $item) {
                $product = $this->productService->getProduct($item->product_id);
                $product->increment('stock', $item->quantity);
            }
            
            $order->update(['status' => 'cancelled']);
            
            return $order->fresh();
        });
    }
    
    /**
     * Update order status
     */
    public function updateStatus(int $orderId, string $status): Order
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);
        
        return $order->fresh();
    }
}
