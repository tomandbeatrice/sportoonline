<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;

class LiveOrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $product = Product::first();
        
        if (!$user) {
             $user = User::create([
                'name' => 'Live User',
                'email' => 'live@example.com',
                'password' => bcrypt('password'),
             ]);
        }

        if (!$product) {
            return;
        }

        $quantity = 1;
        $price = $product->price;
        $total = $quantity * $price;

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => $total,
            'name' => $user->name,
            'address' => 'Test Address',
            'payment_method' => 'credit_card',
            'cart' => json_encode([]),
            'created_at' => now(), // Ensure it's today
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $price,
            'status' => 'pending',
            'platform_fee' => $total * 0.10,
            'seller_payout' => $total * 0.90,
        ]);
    }
}