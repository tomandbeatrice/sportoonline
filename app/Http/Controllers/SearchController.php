public function products(Request $request)
{
    $query = Product::query();

    if ($request->filled('keyword')) {
        $query->where('name', 'like', '%' . $request->keyword . '%');
    }

    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    if ($request->filled('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }

    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    return response()->json($query->latest()->get());
}