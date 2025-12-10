<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class NearbyBusinessController extends Controller
{
    public function index(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        // Fetch sellers. Adjust the query based on your actual User model and roles.
        $sellers = User::where('role', 'seller')->take(20)->get();

        $businesses = $sellers->map(function ($seller) {
            return [
                'id' => $seller->id,
                'name' => $seller->name ?? $seller->company_name ?? 'Mağaza ' . $seller->id,
                'category' => 'store', // You might want to map this from seller details
                'rating' => rand(35, 50) / 10,
                'reviewCount' => rand(5, 200),
                'distance' => rand(1, 50) / 10, // Mock distance in km
                'address' => $seller->address ?? 'İstanbul, Türkiye',
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=500&q=80',
                'isOpen' => (bool)rand(0, 1),
                'location' => [
                    'lat' => 41.0082 + (rand(-50, 50) / 1000),
                    'lng' => 28.9784 + (rand(-50, 50) / 1000),
                ],
                'tags' => ['Genel', 'Alışveriş'],
                'deliveryTime' => rand(20, 60) . ' dk',
                'priceRange' => str_repeat('₺', rand(1, 3))
            ];
        });

        return response()->json($businesses);
    }
}
