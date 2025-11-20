<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'updated_desc');
        switch ($sortBy) {
            case 'updated_desc':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'updated_asc':
                $query->orderBy('updated_at', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'views_desc':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->orderBy('updated_at', 'desc');
        }

        $pages = $query->paginate($request->input('per_page', 20));

        return response()->json($pages);
    }

    public function stats()
    {
        $stats = [
            'total' => Page::count(),
            'published' => Page::where('status', 'published')->count(),
            'draft' => Page::where('status', 'draft')->count(),
            'totalViews' => Page::sum('views'),
        ];

        return response()->json($stats);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'content' => 'required|string',
            'type' => 'required|in:system,custom',
            'status' => 'required|in:published,draft',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'show_in_footer' => 'boolean',
            'show_in_menu' => 'boolean',
        ]);

        // Ensure slug is URL-friendly
        $validated['slug'] = Str::slug($validated['slug']);

        $page = Page::create($validated);

        return response()->json([
            'message' => 'Sayfa oluşturuldu',
            'page' => $page
        ], 201);
    }

    public function show(Page $page)
    {
        return response()->json($page);
    }

    public function showBySlug($slug)
    {
        $page = Page::where('slug', $slug)->first();

        if (!$page) {
            return response()->json([
                'message' => 'Sayfa bulunamadı'
            ], 404);
        }

        return response()->json($page);
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'sometimes|string',
            'type' => 'sometimes|in:system,custom',
            'status' => 'sometimes|in:published,draft',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'show_in_footer' => 'sometimes|boolean',
            'show_in_menu' => 'sometimes|boolean',
        ]);

        if (isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $page->update($validated);

        return response()->json([
            'message' => 'Sayfa güncellendi',
            'page' => $page
        ]);
    }

    public function destroy(Page $page)
    {
        // Prevent deletion of system pages
        if ($page->type === 'system') {
            return response()->json([
                'message' => 'Sistem sayfaları silinemez'
            ], 422);
        }

        $page->delete();

        return response()->json([
            'message' => 'Sayfa silindi'
        ]);
    }

    public function trackView(Page $page)
    {
        $page->increment('views');

        return response()->json([
            'message' => 'View tracked'
        ]);
    }
}
