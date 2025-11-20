<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    public function index()
    {
        $variants = Variant::with('product')->paginate(20);
        return view('admin.variants.index', compact('variants'));
    }

    public function create()
    {
        return view('admin.variants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        Variant::create($validated);

        return redirect()->route('admin.variants.index')
            ->with('success', 'Varyant başarıyla eklendi.');
    }

    public function edit($id)
    {
        $variant = Variant::findOrFail($id);
        return view('admin.variants.edit', compact('variant'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $variant = Variant::findOrFail($id);
        $variant->update($validated);

        return redirect()->route('admin.variants.index')
            ->with('success', 'Varyant başarıyla güncellendi.');
    }

    public function updateBasic(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $variant = Variant::findOrFail($id);
        $variant->update($validated);

        return back()->with('success', 'Variant güncellendi.');
    }

    public function destroy($id)
    {
        $variant = Variant::findOrFail($id);
        $variant->delete();

        return redirect()->route('admin.variants.index')
            ->with('success', 'Varyant başarıyla silindi.');
    }
}