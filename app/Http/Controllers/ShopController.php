<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Vitrin sayfası için aktif ürünleri varyasyonlarıyla birlikte getirir
     */
    public function index()
    {
        // Aktif ürünleri varyasyonlarıyla birlikte çek
        $products = Product::with('variations')
            ->where('status', 'active')
            ->get();

        // Blade sayfasına gönder
        return view('shop', compact('products'));
    }
}