<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $categories = $query->paginate(20);
        
        // Transform for frontend
        $data = $categories->getCollection()->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'parent' => $cat->parent_id ? 'Parent' : 'Root', // Simplify for now
                'productCount' => 0, // Implement product count
                'status' => $cat->status ?? 'active',
                'image' => $cat->image,
                'description' => $cat->description,
                'searchVolume' => 0
            ];
        });

        return response()->json($categories->setCollection($data));
    }

    public function store(Request $request)
    {
        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    public function show($id)
    {
        return Category::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->all());
        return response()->json($category);
    }

    public function destroy($id)
    {
        Category::destroy($id);
        return response()->json(['message' => 'Category deleted']);
    }
}
