<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            [
                'name' => 'Bluetooth Kulaklık',
                'price' => 299.99,
                'category_id' => 1,
                'vendor_id' => 1,
                'description' => 'Kablosuz, mikrofonlu, şarjlı kulaklık',
                'image_url' => 'https://cdn.sportoonline.com/products/kulaklik.jpg',
                'stock' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Pamuklu Tişört',
                'price' => 89.90,
                'category_id' => 2,
                'vendor_id' => 1,
                'description' => '%100 pamuk, unisex tişört',
                'image_url' => 'https://cdn.sportoonline.com/products/tisort.jpg',
                'stock' => 200,
                'is_active' => true,
            ],
            [
                'name' => 'Dekoratif Mum',
                'price' => 49.90,
                'category_id' => 3,
                'vendor_id' => 1,
                'description' => 'Kokulu, el yapımı dekoratif mum',
                'image_url' => 'https://cdn.sportoonline.com/products/mum.jpg',
                'stock' => 75,
                'is_active' => true,
            ],
        ]);
    }
}