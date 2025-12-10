<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Variation;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        // If user is authenticated, fetch from DB
        if ($request->user()) {
            $items = CartItem::with(['product', 'variant'])
                ->where('user_id', $request->user()->id)
                ->get();

            $formattedItems = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'image' => $item->variant ? $item->variant->image : $item->product->image,
                    'delivery_type' => $this->getDeliveryType($item->product),
                ];
            });

            return response()->json([
                'items' => $formattedItems,
                'total' => $formattedItems->sum(fn($i) => $i['price'] * $i['quantity'])
            ]);
        }

        // Fallback for guest (if needed, or return empty)
        return response()->json(['items' => [], 'total' => 0]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:variants,id'
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if item exists
        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Get price
            $price = 0;
            if ($request->variant_id) {
                $variant = Variation::find($request->variant_id);
                $price = $variant->price;
            } else {
                $product = \App\Models\Product::find($request->product_id);
                $price = $product->price;
            }

            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
                'price' => $price
            ]);
        }

        return response()->json(['message' => 'Item added to cart']);
    }

    private function getDeliveryType($product)
    {
        // Logic to determine delivery type
        // This should ideally be a column in products table or category based
        // For now, we use category name heuristic
        $category = $product->category ? strtolower($product->category->name) : '';
        
        if (str_contains($category, 'yemek') || str_contains($category, 'food') || str_contains($category, 'restoran')) {
            return 'instant';
        }
        
        if (str_contains($category, 'otel') || str_contains($category, 'hotel') || str_contains($category, 'bilet') || str_contains($category, 'hizmet')) {
            return 'digital';
        }

        return 'cargo';
    }
}
