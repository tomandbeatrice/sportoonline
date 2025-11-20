<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Account
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@sportoonline.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Seller Accounts
        User::create([
            'name' => 'Spor Dünyası Satıcı',
            'email' => 'seller1@sportoonline.com',
            'password' => Hash::make('seller123'),
            'role' => 'seller',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Aktif Spor Merkezi',
            'email' => 'seller2@sportoonline.com',
            'password' => Hash::make('seller123'),
            'role' => 'seller',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Fitness Pro',
            'email' => 'seller3@sportoonline.com',
            'password' => Hash::make('seller123'),
            'role' => 'seller',
            'email_verified_at' => now(),
        ]);

        // Buyer Accounts
        User::create([
            'name' => 'Ahmet Yılmaz',
            'email' => 'buyer1@sportoonline.com',
            'password' => Hash::make('buyer123'),
            'role' => 'buyer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Ayşe Demir',
            'email' => 'buyer2@sportoonline.com',
            'password' => Hash::make('buyer123'),
            'role' => 'buyer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Mehmet Kaya',
            'email' => 'buyer3@sportoonline.com',
            'password' => Hash::make('buyer123'),
            'role' => 'buyer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Zeynep Şahin',
            'email' => 'buyer4@sportoonline.com',
            'password' => Hash::make('buyer123'),
            'role' => 'buyer',
            'email_verified_at' => now(),
        ]);

        // Generate additional random buyers
        User::factory()->count(20)->create([
            'role' => 'buyer',
        ]);

        // Generate additional random sellers
        User::factory()->count(5)->create([
            'role' => 'seller',
        ]);
    }
}
