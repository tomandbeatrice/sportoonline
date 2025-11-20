<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori oluştur
        $category = Category::firstOrCreate(
            ['name' => 'Elektronik'],
            ['name' => 'Elektronik']
        );

        // Test satıcı kullanıcısı
        $seller = User::firstOrCreate(
            ['email' => 'satici@test.com'],
            [
                'name' => 'Test Satıcı',
                'password' => Hash::make('password'),
                'role' => 'seller'
            ]
        );

        // Vendor oluştur
        $vendor = Vendor::firstOrCreate(
            ['user_id' => $seller->id],
            [
                'name' => 'Test Mağaza',
                'status' => 'approved'
            ]
        );

        // Test ürünleri
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'En yeni iPhone modeli, 256GB',
                'price' => 45999.00,
                'stock' => 15,
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'description' => 'Android flagship telefon',
                'price' => 38999.00,
                'stock' => 20,
            ],
            [
                'name' => 'MacBook Air M3',
                'description' => '13 inç, 8GB RAM, 256GB SSD',
                'price' => 42999.00,
                'stock' => 10,
            ],
            [
                'name' => 'AirPods Pro 2',
                'description' => 'Aktif gürültü engelleme',
                'price' => 8999.00,
                'stock' => 30,
            ],
            [
                'name' => 'iPad Air',
                'description' => '11 inç, Wi-Fi, 128GB',
                'price' => 21999.00,
                'stock' => 12,
            ],
            [
                'name' => 'Apple Watch Series 9',
                'description' => 'GPS, 45mm',
                'price' => 15999.00,
                'stock' => 18,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                [
                    'name' => $productData['name'],
                    'seller_id' => $seller->id
                ],
                array_merge($productData, [
                    'category_id' => $category->id,
                    'vendor_id' => $vendor->id,
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('Test verileri başarıyla oluşturuldu!');
        $this->command->info('Satıcı: satici@test.com / password');
    }
}
