<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;

class DiscoveryController extends Controller
{
    public function index(Request $request)
    {
        // Validate input parameters
        $validated = $request->validate([
            'type' => 'nullable|string|in:restaurant,hotel,service,shop,all',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:100|max:50000'
        ]);

        $query = Place::where('is_active', true);

        if (!empty($validated['type']) && $validated['type'] !== 'all') {
            $query->where('type', $validated['type']);
        }

        // In a real app, we would filter by distance using Haversine formula here
        // $lat = $request->latitude;
        // $lng = $request->longitude;

        $places = $query->get();

        return response()->json([
            'data' => $places
        ]);
    }
}
