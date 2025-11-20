<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call all seeders
        $this->call([
            SubscriptionPlanSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            ExportLogSeeder::class,
            MarketplaceSeeder::class,
            OrderSeeder::class,
            LiveOrderSeeder::class,
        ]);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('📧 Login credentials:');
        $this->command->line('');
        $this->command->line('Admin:');
        $this->command->line('  Email: admin@sportoonline.com');
        $this->command->line('  Password: admin123');
        $this->command->line('');
        $this->command->line('Seller:');
        $this->command->line('  Email: seller1@sportoonline.com');
        $this->command->line('  Password: seller123');
        $this->command->line('');
        $this->command->line('Buyer:');
        $this->command->line('  Email: buyer1@sportoonline.com');
        $this->command->line('  Password: buyer123');
    }
}