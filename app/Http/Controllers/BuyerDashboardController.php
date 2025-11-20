<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyerDashboardController extends Controller
{
    /**
     * Get buyer dashboard statistics
     */
    public function stats(Request $request)
    {
        // GEÇICI: Auth olmadan ilk buyer'ı döndür (geliştirme için)
        $user = $request->user();
        
        if (!$user) {
            $user = User::where('role', 'buyer')->first();
            if (!$user) {
                return response()->json([
                    'total_orders' => 0,
                    'total_spent' => 0,
                    'favorites_count' => 0,
                    'avg_order_value' => 0,
                ]);
            }
        }
        
        // Get buyer's orders
        $orders = Order::where('user_id', $user->id)->get();
        
        $totalOrders = $orders->count();
        $totalSpent = $orders->sum('total');
        
        // Favorites
        $favoritesCount = Favorite::where('user_id', $user->id)->count();
        
        return response()->json([
            'total_orders' => $totalOrders,
            'total_spent' => round($totalSpent, 2),
            'favorites_count' => $favoritesCount,
            'avg_order_value' => $totalOrders > 0 ? round($totalSpent / $totalOrders, 2) : 0,
        ]);
    }

    /**
     * Get buyer's orders with items
     */
    public function orders(Request $request)
    {
        // GEÇICI: Auth olmadan ilk buyer'ı döndür
        $user = $request->user();
        
        if (!$user) {
            $user = User::where('role', 'buyer')->first();
            if (!$user) {
                return response()->json([]);
            }
        }
        
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product:id,name,image_url,price'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($orders);
    }

    /**
     * Get buyer's favorite products
     */
    public function favorites(Request $request)
    {
        // GEÇICI: Auth olmadan ilk buyer'ı döndür
        $user = $request->user();
        
        if (!$user) {
            $user = User::where('role', 'buyer')->first();
            if (!$user) {
                return response()->json([]);
            }
        }
        
        $favorites = Favorite::where('user_id', $user->id)
            ->with('product:id,name,price,image_url,stock')
            ->get();
        
        return response()->json($favorites);
    }
}
