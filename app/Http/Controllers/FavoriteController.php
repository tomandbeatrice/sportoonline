<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Favori ürünleri listele
     */
    public function index()
    {
        $favorites = auth()->user()
            ->favorites()
            ->with('product')
            ->get()
            ->pluck('product');

        return response()->json($favorites);
    }

    /**
     * Favorilere ekle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $favorite = Favorite::firstOrCreate([
            'user_id' => auth()->id(),
            'product_id' => $validated['product_id']
        ]);

        return response()->json([
            'message' => 'Ürün favorilere eklendi',
            'favorite' => $favorite
        ], 201);
    }

    /**
     * Favorilerden kaldır
     */
    public function destroy($productId)
    {
        Favorite::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->delete();

        return response()->json([
            'message' => 'Ürün favorilerden kaldırıldı'
        ]);
    }
}
