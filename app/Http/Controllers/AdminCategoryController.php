<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')
            ->withCount('products')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    public function stats()
    {
        $stats = [
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'parents' => Category::whereNull('parent_id')->count(),
            'totalProducts' => Category::withCount('products')->get()->sum('products_count'),
        ];

        return response()->json($stats);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        // Ensure slug is unique
        $validated['slug'] = Str::slug($validated['slug']);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Kategori oluşturuldu',
            'category' => $category
        ], 201);
    }

    public function show(Category $category)
    {
        $category->load('parent', 'children')
            ->loadCount('products');

        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        // Prevent category from being its own parent
        if (isset($validated['parent_id']) && $validated['parent_id'] == $category->id) {
            return response()->json([
                'message' => 'Kategori kendi üst kategorisi olamaz'
            ], 422);
        }

        // Prevent circular parent relationships
        if (isset($validated['parent_id'])) {
            $parent = Category::find($validated['parent_id']);
            if ($parent && $parent->parent_id == $category->id) {
                return response()->json([
                    'message' => 'Döngüsel kategori ilişkisi oluşturulamaz'
                ], 422);
            }
        }

        if (isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $category->update($validated);

        return response()->json([
            'message' => 'Kategori güncellendi',
            'category' => $category
        ]);
    }

    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'message' => 'Bu kategoride ürün bulunduğu için silemezsiniz'
            ], 422);
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return response()->json([
                'message' => 'Bu kategorinin alt kategorileri var'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Kategori silindi'
        ]);
    }
}
