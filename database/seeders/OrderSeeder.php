<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach(range(1, 25) as $i) {
            $user = $users->random();
            $product = $products->random();
            $quantity = rand(1, 3);
            $price = $product->price;
            $total = $quantity * $price;
            
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $total,
                'name' => $user->name,
                'email' => $user->email,
                'address' => 'Test Address ' . $i,
                'payment_method' => 'credit_card',
                'status' => 'completed',
                'cart' => json_encode([]),
                'created_at' => now()->subDays(rand(0, 30)),
            ]);

            // Create Order Item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'status' => 'completed',
                'platform_fee' => $total * 0.10, // 10% commission
                'seller_payout' => $total * 0.90,
            ]);
        }
    }
}