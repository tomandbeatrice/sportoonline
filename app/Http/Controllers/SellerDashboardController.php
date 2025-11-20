<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerDashboardController extends Controller
{
    /**
     * Get seller dashboard statistics
     */
    public function stats(Request $request)
    {
        $user = Auth::user();
        
        // Get seller's products
        $products = Product::where('seller_id', $user->id)->get();
        $productIds = $products->pluck('id');
        
        // Get all order items for seller's products
        $orderItems = OrderItem::whereIn('product_id', $productIds)->get();
        
        // Calculate stats
        $totalRevenue = $orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });
        
        $totalOrders = $orderItems->pluck('order_id')->unique()->count();
        
        // Order status breakdown
        $orders = Order::whereIn('id', $orderItems->pluck('order_id'))->get();
        $pendingOrders = $orders->where('status', 'pending')->count();
        $shippedOrders = $orders->where('status', 'shipped')->count();
        $completedOrders = $orders->whereIn('status', ['delivered', 'completed'])->count();
        
        // Commission and payout
        $totalCommission = $orderItems->sum('platform_fee');
        $sellerPayout = $orderItems->sum('seller_payout');
        
        // Average rating (if available)
        $avgRating = $products->avg('avg_rating') ?? 0;
        
        return response()->json([
            'total_products' => $products->count(),
            'total_orders' => $totalOrders,
            'total_revenue' => round($totalRevenue, 2),
            'pending_orders' => $pendingOrders,
            'shipped_orders' => $shippedOrders,
            'completed_orders' => $completedOrders,
            'avg_rating' => round($avgRating, 2),
            'total_commission' => round($totalCommission, 2),
            'seller_payout' => round($sellerPayout, 2),
        ]);
    }

    /**
     * Get seller's products with sales data
     */
    public function products(Request $request)
    {
        $user = Auth::user();
        
        $products = Product::where('seller_id', $user->id)
            ->withCount(['orderItems as sold_count' => function ($query) {
                $query->selectRaw('sum(quantity)');
            }])
            ->with(['orderItems' => function ($query) {
                $query->selectRaw('product_id, sum(quantity * price) as revenue')
                      ->groupBy('product_id');
            }])
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'sold_count' => $product->sold_count ?? 0,
                    'revenue' => $product->orderItems->first()->revenue ?? 0,
                    'image_url' => $product->image_url,
                ];
            });
        
        return response()->json($products);
    }

    /**
     * Get seller's orders
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        
        $products = Product::where('seller_id', $user->id)->pluck('id');
        
        $orders = Order::whereHas('items', function ($query) use ($products) {
            $query->whereIn('product_id', $products);
        })
        ->with(['items' => function ($query) use ($products) {
            $query->whereIn('product_id', $products)->with('product:id,name,image_url');
        }])
        ->orderBy('created_at', 'desc')
        ->get();
        
        return response()->json($orders);
    }
}
