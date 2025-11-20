<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Variant;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        $colors = ['Kırmızı', 'Mavi', 'Siyah', 'Beyaz'];
        $sizes = ['S', 'M', 'L', 'XL'];

        foreach (range(1, 10) as $i) {
            Variant::create([
                'name'           => "Varyant {$i}",
                'variant_price'  => rand(150, 800),
                'product_id'     => 1,
                'color'          => $colors[array_rand($colors)],
                'size'           => $sizes[array_rand($sizes)],
                'stock'          => rand(5, 30),
                'image'          => null,
            ]);
        }
    }
}