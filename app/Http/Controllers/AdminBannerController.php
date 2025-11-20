<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();

        // Search filter
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Position filter
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Status filter
        if ($request->filled('status')) {
            $now = now();
            
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true)
                          ->where(function($q) use ($now) {
                              $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                          })
                          ->where(function($q) use ($now) {
                              $q->whereNull('end_date')->orWhere('end_date', '>', $now);
                          });
                    break;
                case 'scheduled':
                    $query->where('is_active', true)
                          ->where('start_date', '>', $now);
                    break;
                case 'expired':
                    $query->where('end_date', '<', $now);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'order_asc');
        switch ($sortBy) {
            case 'order_asc':
                $query->orderBy('order', 'asc');
                break;
            case 'order_desc':
                $query->orderBy('order', 'desc');
                break;
            case 'views_desc':
                $query->orderBy('views', 'desc');
                break;
            case 'views_asc':
                $query->orderBy('views', 'asc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('order', 'asc');
        }

        $banners = $query->paginate(20);

        // Calculate CTR for each banner
        $banners->getCollection()->transform(function ($banner) {
            $banner->ctr = $banner->views > 0 
                ? ($banner->clicks / $banner->views) * 100 
                : 0;
            return $banner;
        });

        return response()->json($banners);
    }

    public function stats()
    {
        $now = now();
        
        $stats = [
            'total' => Banner::count(),
            'active' => Banner::where('is_active', true)
                ->where(function($q) use ($now) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                })
                ->where(function($q) use ($now) {
                    $q->whereNull('end_date')->orWhere('end_date', '>', $now);
                })
                ->count(),
            'scheduled' => Banner::where('is_active', true)
                ->where('start_date', '>', $now)
                ->count(),
            'totalViews' => Banner::sum('views'),
        ];

        return response()->json($stats);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|in:home_top,home_middle,home_bottom,sidebar,category,product',
            'order' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_url' => 'nullable|url|max:255',
            'open_new_tab' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('banners', 'public');
            $validated['image_url'] = Storage::url($imagePath);
        }

        $banner = Banner::create($validated);

        return response()->json([
            'message' => 'Banner oluşturuldu',
            'banner' => $banner
        ], 201);
    }

    public function show(Banner $banner)
    {
        $banner->ctr = $banner->views > 0 
            ? ($banner->clicks / $banner->views) * 100 
            : 0;

        return response()->json($banner);
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'position' => 'sometimes|in:home_top,home_middle,home_bottom,sidebar,category,product',
            'order' => 'sometimes|integer|min:0',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_url' => 'nullable|url|max:255',
            'open_new_tab' => 'sometimes|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image_url) {
                $oldImagePath = str_replace('/storage/', '', $banner->image_url);
                Storage::disk('public')->delete($oldImagePath);
            }

            $image = $request->file('image');
            $imagePath = $image->store('banners', 'public');
            $validated['image_url'] = Storage::url($imagePath);
        }

        $banner->update($validated);

        return response()->json([
            'message' => 'Banner güncellendi',
            'banner' => $banner
        ]);
    }

    public function destroy(Banner $banner)
    {
        // Delete image file
        if ($banner->image_url) {
            $imagePath = str_replace('/storage/', '', $banner->image_url);
            Storage::disk('public')->delete($imagePath);
        }

        $banner->delete();

        return response()->json([
            'message' => 'Banner silindi'
        ]);
    }

    public function trackView(Banner $banner)
    {
        $banner->increment('views');

        return response()->json([
            'message' => 'View tracked'
        ]);
    }

    public function trackClick(Banner $banner)
    {
        $banner->increment('clicks');

        return response()->json([
            'message' => 'Click tracked'
        ]);
    }
}
