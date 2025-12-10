<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCarRentalController extends Controller
{
    public function index()
    {
        // Mock data for now, to be replaced with DB call
        $cars = [
            [
                'id' => 1,
                'model' => 'BMW 320i',
                'plate' => '34 ABC 123',
                'year' => 2024,
                'fuelType' => 'Benzin',
                'transmission' => 'Otomatik',
                'price' => 3500,
                'status' => 'available',
                'image' => 'https://images.unsplash.com/photo-1555215695-3004980adade?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
                'features' => ['Navigasyon', 'Deri Koltuk', 'Sunroof', '360 Kamera']
            ],
            [
                'id' => 2,
                'model' => 'Mercedes C200',
                'plate' => '34 XYZ 789',
                'year' => 2023,
                'fuelType' => 'Dizel',
                'transmission' => 'Otomatik',
                'price' => 3800,
                'status' => 'rented',
                'image' => 'https://images.unsplash.com/photo-1617788138017-80ad40651399?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
                'features' => ['AMG Paket', 'Head-up Display', 'Isıtmalı Koltuk']
            ],
            [
                'id' => 3,
                'model' => 'Renault Clio',
                'plate' => '35 DEF 456',
                'year' => 2022,
                'fuelType' => 'Benzin',
                'transmission' => 'Manuel',
                'price' => 1200,
                'status' => 'maintenance',
                'image' => 'https://images.unsplash.com/photo-1621007947382-bb3c3968e3bb?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
                'features' => ['Bluetooth', 'Klima', 'Hız Sabitleyici']
            ],
            [
                'id' => 4,
                'model' => 'Fiat Egea',
                'plate' => '06 GHI 789',
                'year' => 2023,
                'fuelType' => 'Dizel',
                'transmission' => 'Otomatik',
                'price' => 1400,
                'status' => 'available',
                'image' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
                'features' => ['Tablet Ekran', 'Geri Görüş', 'Apple CarPlay']
            ]
        ];

        return response()->json([
            'data' => $cars
        ]);
    }

    public function stats()
    {
        return response()->json([
            'total_cars' => 45,
            'rented_cars' => 28,
            'maintenance_cars' => 4,
            'monthly_revenue' => '450K'
        ]);
    }
}
