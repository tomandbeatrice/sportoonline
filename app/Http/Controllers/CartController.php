<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Variation;

class CartController extends Controller
{
    /**
     * Vitrin sayfasından sepete ürün ekler
     */
    public function add(Request $request)
    {
        $request->validate([
            'variation_id' => 'required|exists:variations,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $variation = Variation::with('product')->findOrFail($request->variation_id);

        $cart = session()->get('cart', []);

        $cart[$variation->id] = [
            'product_name' => $variation->product->name,
            'variation_id' => $variation->id,
            'color'        => $variation->color,
            'quantity'     => $request->quantity,
            'image'        => $variation->image,
            'price'        => $variation->price,
        ];

        session()->put('cart', $cart);

        return back()->with('success', 'Ürün sepete eklendi!');
    }

    /**
     * Sepet sayfasını gösterir
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(function ($item) {
            return ($item['price'] ?? 0) * $item['quantity'];
        });

        return view('cart', compact('cart', 'total'));
    }

    /**
     * Sepetten ürün çıkarır
     */
    public function remove(Request $request)
    {
        $request->validate([
            'variation_id' => 'required|integer'
        ]);

        $cart = session()->get('cart', []);
        unset($cart[$request->variation_id]);
        session()->put('cart', $cart);

        return back()->with('success', 'Ürün sepetten çıkarıldı.');
    }

    /**
     * Sepette adet güncelleme
     */
    public function updateQuantity(Request $request)
    {
        $request->validate([
            'variation_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->variation_id])) {
            $cart[$request->variation_id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Sepet güncellendi.');
    }

    /**
     * Sepeti temizler
     */
    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Sepet temizlendi.');
    }
}