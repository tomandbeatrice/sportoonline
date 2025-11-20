<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds for C2C marketplace development
     */
    public function run(): void
    {
        // Clear existing data (development only!)
        if (app()->environment('local')) {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            User::truncate();
            Product::truncate();
            Category::truncate();
            Order::truncate();
            OrderItem::truncate();
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@sportoonline.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Seller Users
        $sellers = [];
        for ($i = 1; $i <= 5; $i++) {
            $sellers[] = User::create([
                'name' => "Satıcı {$i}",
                'email' => "seller{$i}@sportoonline.com",
                'password' => Hash::make('seller123'),
                'role' => 'seller',
                'email_verified_at' => now(),
            ]);
        }

        // Create Buyer Users
        $buyers = [];
        for ($i = 1; $i <= 10; $i++) {
            $buyers[] = User::create([
                'name' => "Alıcı {$i}",
                'email' => "buyer{$i}@sportoonline.com",
                'password' => Hash::make('buyer123'),
                'role' => 'buyer',
                'email_verified_at' => now(),
            ]);
        }

        // Create Categories
        $categories = [];
        $categoryNames = [
            'Ayakkabı' => ['Koşu Ayakkabısı', 'Spor Ayakkabı', 'Günlük Ayakkabı'],
            'Giyim' => ['Tişört', 'Şort', 'Eşofman'],
            'Aksesuar' => ['Çanta', 'Şapka', 'Çorap'],
            'Ekipman' => ['Top', 'Raket', 'Eldiven']
        ];

        foreach ($categoryNames as $parent => $children) {
            $parentCat = Category::create([
                'name' => $parent,
                'slug' => \Str::slug($parent),
                'is_active' => true,
            ]);

            foreach ($children as $child) {
                $categories[] = Category::create([
                    'name' => $child,
                    'slug' => \Str::slug($child),
                    'parent_id' => $parentCat->id,
                    'is_active' => true,
                ]);
            }
        }

        // Create Products
        $products = [];
        $productNames = [
            'Nike Air Max 270' => 1299.99,
            'Adidas Ultraboost 22' => 1499.99,
            'Puma RS-X' => 899.99,
            'New Balance 574' => 799.99,
            'Reebok Classic' => 699.99,
            'Under Armour Hovr' => 1099.99,
            'Asics Gel-Kayano' => 1399.99,
            'Skechers Go Run' => 599.99,
            'Converse Chuck Taylor' => 499.99,
            'Vans Old Skool' => 549.99,
            'Nike Dri-FIT Tişört' => 299.99,
            'Adidas Tiro Şort' => 349.99,
            'Puma Spor Çanta' => 449.99,
            'Nike Heritage Sırt Çantası' => 599.99,
            'Adidas Futbol Topu' => 399.99,
        ];

        foreach ($productNames as $name => $price) {
            $seller = $sellers[array_rand($sellers)];
            $category = $categories[array_rand($categories)];

            $products[] = Product::create([
                'name' => $name,
                'slug' => \Str::slug($name),
                'description' => "Kaliteli {$name} - Orijinal ürün",
                'price' => $price,
                'category_id' => $category->id,
                'seller_id' => $seller->id,
                'stock' => rand(5, 50),
                'image_url' => "https://picsum.photos/400/400?random=" . rand(1, 100),
                'status' => 'active',
                'is_featured' => rand(0, 1),
            ]);
        }

        // Create Orders
        $orderStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        foreach ($buyers as $buyer) {
            $orderCount = rand(1, 5);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $orderProducts = [];
                $itemCount = rand(1, 3);
                
                for ($j = 0; $j < $itemCount; $j++) {
                    $orderProducts[] = $products[array_rand($products)];
                }

                $total = 0;
                $order = Order::create([
                    'user_id' => $buyer->id,
                    'name' => $buyer->name,
                    'email' => $buyer->email,
                    'status' => $orderStatuses[array_rand($orderStatuses)],
                    'total_amount' => 0, // Will update
                    'total_price' => 0,
                    'address' => 'Test Adres, İstanbul',
                    'shipping_address' => 'Test Adres, İstanbul',
                    'shipping_phone' => '+90 555 123 4567',
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);

                foreach ($orderProducts as $product) {
                    $quantity = rand(1, 3);
                    $subtotal = $product->price * $quantity;
                    $total += $subtotal;
                    
                    // Platform 15% commission
                    $platformFee = $subtotal * 0.15;
                    $sellerPayout = $subtotal - $platformFee;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->price,
                        'subtotal' => $subtotal,
                        'platform_fee' => $platformFee,
                        'seller_payout' => $sellerPayout,
                    ]);
                }

                $order->update(['total_amount' => $total]);
            }
        }

        $this->command->info('✅ Development data seeded successfully!');
        $this->command->info('📧 Admin: admin@sportoonline.com / admin123');
        $this->command->info('📧 Seller: seller1@sportoonline.com / seller123');
        $this->command->info('📧 Buyer: buyer1@sportoonline.com / buyer123');
        $this->command->info('📊 Created:');
        $this->command->info('   - Users: ' . User::count());
        $this->command->info('   - Products: ' . Product::count());
        $this->command->info('   - Orders: ' . Order::count());
        $this->command->info('   - Categories: ' . Category::count());
    }
}
