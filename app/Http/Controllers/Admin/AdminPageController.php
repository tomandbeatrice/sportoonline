<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class AdminPageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();

        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $pages = $query->paginate(20);
        return response()->json($pages);
    }

    public function stats()
    {
        return response()->json([
            'total' => Page::count(),
            'published' => Page::where('status', 'published')->count(),
            'draft' => Page::where('status', 'draft')->count(),
            'totalViews' => Page::sum('views') ?? 0,
        ]);
    }

    public function store(Request $request)
    {
        $page = Page::create($request->all());
        return response()->json($page, 201);
    }

    public function show($id)
    {
        return Page::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $page->update($request->all());
        return response()->json($page);
    }

    public function destroy($id)
    {
        Page::destroy($id);
        return response()->json(['message' => 'Page deleted']);
    }
}
