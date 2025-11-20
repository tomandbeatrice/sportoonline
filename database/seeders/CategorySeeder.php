<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fitness ve Kondisyon',
                'slug' => 'fitness-kondisyon',
                'description' => 'Fitness ekipmanları, kondisyon aletleri ve spor malzemeleri',
                'children' => [
                    ['name' => 'Dambıllar', 'slug' => 'dambilar'],
                    ['name' => 'Kettlebell', 'slug' => 'kettlebell'],
                    ['name' => 'Yoga Matı', 'slug' => 'yoga-mati'],
                    ['name' => 'Koşu Bandı', 'slug' => 'kosu-bandi'],
                    ['name' => 'Eliptik Bisiklet', 'slug' => 'eliptik-bisiklet'],
                    ['name' => 'Ağırlık Setleri', 'slug' => 'agirlik-setleri'],
                ]
            ],
            [
                'name' => 'Futbol',
                'slug' => 'futbol',
                'description' => 'Futbol ekipmanları ve malzemeleri',
                'children' => [
                    ['name' => 'Futbol Topu', 'slug' => 'futbol-topu'],
                    ['name' => 'Kaleci Eldiveni', 'slug' => 'kaleci-eldiveni'],
                    ['name' => 'Krampon', 'slug' => 'krampon'],
                    ['name' => 'Tekmelik', 'slug' => 'tekmelik'],
                    ['name' => 'Antrenman Malzemeleri', 'slug' => 'antrenman-malzemeleri'],
                ]
            ],
            [
                'name' => 'Basketbol',
                'slug' => 'basketbol',
                'description' => 'Basketbol ekipmanları ve aksesuarları',
                'children' => [
                    ['name' => 'Basketbol Topu', 'slug' => 'basketbol-topu'],
                    ['name' => 'Basketbol Ayakkabısı', 'slug' => 'basketbol-ayakkabisi'],
                    ['name' => 'Basketbol Potası', 'slug' => 'basketbol-potasi'],
                    ['name' => 'Kolluk', 'slug' => 'kolluk'],
                ]
            ],
            [
                'name' => 'Tenis',
                'slug' => 'tenis',
                'description' => 'Tenis ekipmanları ve aksesuarları',
                'children' => [
                    ['name' => 'Tenis Raketi', 'slug' => 'tenis-raketi'],
                    ['name' => 'Tenis Topu', 'slug' => 'tenis-topu'],
                    ['name' => 'Tenis Ayakkabısı', 'slug' => 'tenis-ayakkabisi'],
                    ['name' => 'Tenis Çantası', 'slug' => 'tenis-cantasi'],
                ]
            ],
            [
                'name' => 'Koşu ve Atletizm',
                'slug' => 'kosu-atletizm',
                'description' => 'Koşu ve atletizm ekipmanları',
                'children' => [
                    ['name' => 'Koşu Ayakkabısı', 'slug' => 'kosu-ayakkabisi'],
                    ['name' => 'Koşu Saati', 'slug' => 'kosu-saati'],
                    ['name' => 'Spor Çorap', 'slug' => 'spor-corap'],
                    ['name' => 'Tayt ve Şort', 'slug' => 'tayt-sort'],
                ]
            ],
            [
                'name' => 'Yüzme',
                'slug' => 'yuzme',
                'description' => 'Yüzme ekipmanları ve aksesuarları',
                'children' => [
                    ['name' => 'Mayo', 'slug' => 'mayo'],
                    ['name' => 'Bone', 'slug' => 'bone'],
                    ['name' => 'Yüzme Gözlüğü', 'slug' => 'yuzme-gozlugu'],
                    ['name' => 'Kulak Tıkacı', 'slug' => 'kulak-tikaci'],
                    ['name' => 'Şnorkel', 'slug' => 'snorkel'],
                ]
            ],
            [
                'name' => 'Dağcılık ve Kamp',
                'slug' => 'dagcilik-kamp',
                'description' => 'Dağcılık ve kamp malzemeleri',
                'children' => [
                    ['name' => 'Çadır', 'slug' => 'cadir'],
                    ['name' => 'Uyku Tulumu', 'slug' => 'uyku-tulumu'],
                    ['name' => 'Sırt Çantası', 'slug' => 'sirt-cantasi'],
                    ['name' => 'Trekking Ayakkabısı', 'slug' => 'trekking-ayakkabisi'],
                    ['name' => 'Kamp Ocağı', 'slug' => 'kamp-ocagi'],
                ]
            ],
            [
                'name' => 'Bisiklet',
                'slug' => 'bisiklet',
                'description' => 'Bisiklet ve aksesuarları',
                'children' => [
                    ['name' => 'Dağ Bisikleti', 'slug' => 'dag-bisikleti'],
                    ['name' => 'Şehir Bisikleti', 'slug' => 'sehir-bisikleti'],
                    ['name' => 'Bisiklet Kaskı', 'slug' => 'bisiklet-kaski'],
                    ['name' => 'Bisiklet Eldiveni', 'slug' => 'bisiklet-eldiveni'],
                    ['name' => 'Bisiklet Pompası', 'slug' => 'bisiklet-pompasi'],
                ]
            ],
            [
                'name' => 'Spor Giyim',
                'slug' => 'spor-giyim',
                'description' => 'Spor kıyafetleri ve aksesuarları',
                'children' => [
                    ['name' => 'Erkek Spor Giyim', 'slug' => 'erkek-spor-giyim'],
                    ['name' => 'Kadın Spor Giyim', 'slug' => 'kadin-spor-giyim'],
                    ['name' => 'Çocuk Spor Giyim', 'slug' => 'cocuk-spor-giyim'],
                    ['name' => 'Spor Çanta', 'slug' => 'spor-canta'],
                ]
            ],
            [
                'name' => 'Spor Beslenmesi',
                'slug' => 'spor-beslenmesi',
                'description' => 'Spor beslenmesi ürünleri ve takviyeler',
                'children' => [
                    ['name' => 'Protein Tozu', 'slug' => 'protein-tozu'],
                    ['name' => 'BCAA', 'slug' => 'bcaa'],
                    ['name' => 'Kreatin', 'slug' => 'kreatin'],
                    ['name' => 'Vitamin', 'slug' => 'vitamin'],
                    ['name' => 'Enerji Barları', 'slug' => 'enerji-barlari'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = Category::create($categoryData);

            if (!empty($children)) {
                foreach ($children as $child) {
                    Category::create(array_merge($child, [
                        'parent_id' => $category->id,
                        'description' => $child['name'] . ' kategorisi',
                    ]));
                }
            }
        }
    }
}