<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class AdminBannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $banners = $query->paginate(20);
        return response()->json($banners);
    }

    public function stats()
    {
        return response()->json([
            'total' => Banner::count(),
            'active' => Banner::where('status', 'active')->count(),
            'scheduled' => Banner::where('status', 'scheduled')->count(),
            'totalViews' => Banner::sum('views') ?? 0,
        ]);
    }

    public function store(Request $request)
    {
        $banner = Banner::create($request->all());
        return response()->json($banner, 201);
    }

    public function show($id)
    {
        return Banner::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update($request->all());
        return response()->json($banner);
    }

    public function destroy($id)
    {
        Banner::destroy($id);
        return response()->json(['message' => 'Banner deleted']);
    }
}
