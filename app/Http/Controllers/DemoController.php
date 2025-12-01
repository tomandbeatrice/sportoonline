<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use App\Models\Order;
use App\Models\DemoLog;

class DemoController extends Controller
{
    // 🎯 Demo ürünleri getir
    public function products()
    {
        // Demo ziyaretini logla
        DemoLog::create([
            'ip' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'visited_at' => now()
        ]);

        // Demo ürünleri getir
        $products = Product::with('variants')
            ->where('title', 'like', '%Demo%')
            ->get();

        return response()->json($products);
    }

    // 💬 Demo yorumları getir
    public function reviews()
    {
        $reviews = Review::where('comment', 'like', '%Demo%')
            ->orWhere('approved', true)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return response()->json($reviews);
    }

    // 💳 Demo ödeme simülasyonu
    public function checkout(Request $request)
    {
        // Gerçek ödeme yapılmaz, sadece loglama
        \Log::info('Demo checkout:', $request->all());

        return response()->json([
            'message' => 'Demo ödeme başarıyla simüle edildi.',
            'status' => 'success'
        ]);
    }
}