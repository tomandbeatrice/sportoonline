<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;

class OrderDetailSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();
        $products = Product::all();

        foreach ($orders as $order) {
            // Her siparişe rastgele 2 ürün ekleniyor
            $selectedProducts = $products->random(2);

            foreach ($selectedProducts as $product) {
                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => rand(1, 3),
                    'price'      => $product->price,
                ]);
            }
        }
    }
}