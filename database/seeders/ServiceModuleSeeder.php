<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'slug' => 'marketplace',
                'name' => 'Mağaza',
                'icon' => '🛒',
                'badge' => null,
                'badge_class' => '',
                'is_active' => true,
                'order' => 1,
                'route' => '/',
            ],
            [
                'slug' => 'food',
                'name' => 'Yemek',
                'icon' => '🍔',
                'badge' => 'Yeni',
                'badge_class' => 'bg-green-100 text-green-600',
                'is_active' => true,
                'order' => 2,
                'route' => '/food',
            ],
            [
                'slug' => 'hotel',
                'name' => 'Otel',
                'icon' => '🏨',
                'badge' => null,
                'badge_class' => '',
                'is_active' => true,
                'order' => 3,
                'route' => '/hotels',
            ],
            [
                'slug' => 'rides',
                'name' => 'Ulaşım',
                'icon' => '🚗',
                'badge' => null,
                'badge_class' => '',
                'is_active' => true,
                'order' => 4,
                'route' => '/rides',
            ],
            [
                'slug' => 'services',
                'name' => 'Hizmet',
                'icon' => '🔧',
                'badge' => null,
                'badge_class' => '',
                'is_active' => true,
                'order' => 5,
                'route' => '/services',
            ],
            [
                'slug' => 'career',
                'name' => 'Kariyer',
                'icon' => '💼',
                'badge' => '12',
                'badge_class' => 'bg-blue-100 text-blue-600',
                'is_active' => true,
                'order' => 6,
                'route' => '/career',
            ],
        ];

        foreach ($modules as $module) {
            \App\Models\ServiceModule::updateOrCreate(
                ['slug' => $module['slug']],
                $module
            );
        }
    }
}
