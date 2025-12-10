<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Get all active banners
     */
    public function index()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
            
        return response()->json($banners);
    }

    /**
     * Store a new banner
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|max:2048',
            'link' => 'nullable|url',
            'position' => 'nullable|string|in:home,category,product',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_url'] = $request->file('image')->store('banners', 'public');
        }

        $banner = Banner::create($validated);

        return response()->json([
            'message' => 'Banner oluşturuldu',
            'banner' => $banner
        ], 201);
    }

    /**
     * Update banner
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|url',
            'position' => 'nullable|string|in:home,category,product',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image_url) {
                Storage::disk('public')->delete($banner->image_url);
            }
            $validated['image_url'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($validated);

        return response()->json([
            'message' => 'Banner güncellendi',
            'banner' => $banner
        ]);
    }

    /**
     * Delete banner
     */
    public function destroy(Banner $banner)
    {
        if ($banner->image_url) {
            Storage::disk('public')->delete($banner->image_url);
        }
        
        $banner->delete();

        return response()->json(['message' => 'Banner silindi']);
    }
}
