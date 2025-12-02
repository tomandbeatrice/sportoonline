<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ModerationController extends Controller
{
    public function flagProduct($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Ürün bulunamadı'], 404);
        }

        $product->update(['flagged' => true]);
        return response()->json(['message' => 'Ürün işaretlendi', 'product' => $product]);
    }
}
