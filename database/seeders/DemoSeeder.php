<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\Review;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // Satıcı oluştur
        $seller = User::factory()->create([
            'name' => 'Demo Satıcı',
            'email' => 'seller@demo.com',
            'role' => 'seller',
            'password' => bcrypt('password') // login için sabit parola
        ]);

        // Alıcı oluştur
        $buyer = User::factory()->create([
            'name' => 'Demo Alıcı',
            'email' => 'buyer@demo.com',
            'role' => 'buyer',
            'password' => bcrypt('password')
        ]);

        // Ürün oluştur
        $product = Product::factory()->create([
            'title' => 'Demo Ürün',
            'description' => 'Demo açıklama',
            'price' => 129.99,
            'image' => 'https://via.placeholder.com/300x200.png?text=Demo+Ürün',
            'seller_id' => $seller->id
        ]);

        // Varyantlar oluştur
        $variants = [
            ['type' => 'Renk', 'value' => 'Kırmızı', 'stock' => 10, 'price' => 129.99],
            ['type' => 'Renk', 'value' => 'Siyah', 'stock' => 8, 'price' => 129.99],
            ['type' => 'Beden', 'value' => 'M', 'stock' => 5, 'price' => 129.99],
        ];

        foreach ($variants as $variant) {
            ProductVariant::create([
                'product_id' => $product->id,
                'type' => $variant['type'],
                'value' => $variant['value'],
                'stock' => $variant['stock'],
                'price' => $variant['price']
            ]);
        }

        // Sipariş oluştur
        $order = Order::factory()->create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'variant_id' => 1, // ilk varyant
            'quantity' => 1,
            'total' => 129.99,
            'status' => 'tamamlandı'
        ]);

        // Yorum oluştur
        Review::factory()->create([
            'order_id' => $order->id,
            'seller_id' => $seller->id,
            'rating' => 5,
            'comment' => 'Harika ürün!',
            'approved' => true
        ]);
    }
}