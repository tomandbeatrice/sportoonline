<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use App\Models\IncentiveCampaign;

class MarketplaceSeeder extends Seeder
{
    public function run()
    {
        // Create Category
        $category = Category::firstOrCreate(
            ['name' => 'Otomotiv']
        );

        // Create Vendor Users
        $vendorUser1 = User::firstOrCreate(
            ['email' => 'vendor1@marketplace.com'],
            [
                'name' => 'FormulaTech Admin',
                'password' => bcrypt('password'),
                'role' => 'seller',
            ]
        );

        $vendorUser2 = User::firstOrCreate(
            ['email' => 'vendor2@marketplace.com'],
            [
                'name' => 'EcoGear Admin',
                'password' => bcrypt('password'),
                'role' => 'seller',
            ]
        );

        // Satıcılar
        $vendor1 = Vendor::create([
            'name' => 'FormulaTech',
            'user_id' => $vendorUser1->id,
        ]);

        $vendor2 = Vendor::create([
            'name' => 'EcoGear',
            'user_id' => $vendorUser2->id,
        ]);

        // Ürünler
        Product::create([
            'vendor_id' => $vendor1->id,
            'seller_id' => $vendorUser1->id,
            'name' => 'Sprint Lastik Seti',
            'price' => 1299,
            'stock' => 25,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        Product::create([
            'vendor_id' => $vendor2->id,
            'seller_id' => $vendorUser2->id,
            'name' => 'Eco Fren Balatası',
            'price' => 499,
            'stock' => 40,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        // Alıcılar
        User::firstOrCreate(
            ['email' => 'ali@buyer.com'],
            [
                'name' => 'Ali',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );

        User::firstOrCreate(
            ['email' => 'zeynep@buyer.com'],
            [
                'name' => 'Zeynep',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );

        // Kampanyalar
        IncentiveCampaign::create([
            'name' => 'Yarışa Özel %20 İndirim',
            'type' => 'discount',
            'description' => 'Premium üyeler için özel indirim',
            'discount_percentage' => 20,
            'target_segments' => ['Premium'],
            'is_active' => true,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);
    }
}