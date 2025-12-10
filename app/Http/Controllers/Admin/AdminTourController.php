<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminTourController extends Controller
{
    public function index(Request $request)
    {
        // Mock response for now, replace with actual Model query when Tour model exists
        $tours = [
            [
                'id' => 1,
                'name' => 'Kapadokya Balon Turu',
                'region' => 'Nevşehir',
                'duration' => '3 Gün 2 Gece',
                'price' => 4500,
                'rating' => 4.9,
                'reviews' => 320,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1532664189809-021334269b70?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
                'nextDate' => '2024-04-15',
                'capacity' => 20,
                'booked' => 15
            ],
            [
                'id' => 2,
                'name' => 'Likya Yolu Yürüyüşü',
                'region' => 'Antalya',
                'duration' => '5 Gün 4 Gece',
                'price' => 3200,
                'rating' => 4.8,
                'reviews' => 150,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
                'nextDate' => '2024-05-01',
                'capacity' => 12,
                'booked' => 8
            ],
            [
                'id' => 3,
                'name' => 'Karadeniz Yayla Turu',
                'region' => 'Rize',
                'duration' => '4 Gün 3 Gece',
                'price' => 5500,
                'rating' => 4.7,
                'reviews' => 210,
                'status' => 'upcoming',
                'image' => 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
                'nextDate' => '2024-06-10',
                'capacity' => 25,
                'booked' => 5
            ]
        ];

        return response()->json(['data' => $tours]);
    }

    public function stats()
    {
        return response()->json([
            'total' => 12,
            'active' => 8,
            'upcoming' => 4,
            'revenue' => 125000
        ]);
    }
}
