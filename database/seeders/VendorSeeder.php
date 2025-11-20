<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    public function run()
    {
        DB::table('vendors')->insert([
            [
                'id' => 1,
                'name' => 'Sportoonline Resmi Satıcı',
                'branding_color' => '#FF6600',
                'branding_font' => 'Roboto',
                'branding_logo' => 'https://cdn.sportoonline.com/vendor/logo.png',
                'logo_url' => 'https://cdn.sportoonline.com/vendor/logo.png',
                'primary_color' => '#FF6600',
                'secondary_color' => '#333333',
                'settings' => json_encode([
                    'shipment' => 'standard',
                    'return_policy' => '15 days'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}