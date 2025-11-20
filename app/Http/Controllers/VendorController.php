<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    /**
     * Get list of all vendors
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'seller')
            ->where('status', 'active');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Rating filter
        if ($request->filled('min_rating')) {
            $query->where('avg_rating', '>=', $request->min_rating);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $vendors = $query->paginate($request->get('per_page', 20));

        // Enrich with stats
        $vendors->getCollection()->transform(function($vendor) {
            return $this->enrichVendorData($vendor);
        });

        return response()->json([
            'vendors' => $vendors->items(),
            'total' => $vendors->total(),
            'current_page' => $vendors->currentPage(),
            'last_page' => $vendors->lastPage()
        ]);
    }

    /**
     * Get vendor details
     */
    public function show($id)
    {
        $vendor = User::where('role', 'seller')
            ->where('id', $id)
            ->firstOrFail();

        $vendor = $this->enrichVendorData($vendor, true);

        return response()->json($vendor);
    }

    /**
     * Compare multiple vendors
     */
    public function compare(Request $request)
    {
        $request->validate([
            'ids' => 'required|string'
        ]);

        $ids = explode(',', $request->ids);
        $vendors = User::where('role', 'seller')
            ->whereIn('id', $ids)
            ->get();

        $comparison = $vendors->map(function($vendor) {
            return $this->enrichVendorData($vendor, true);
        });

        return response()->json($comparison);
    }

    /**
     * Enrich vendor with statistics
     */
    private function enrichVendorData(User $vendor, $detailed = false)
    {
        // Basic stats
        $productsCount = Product::where('seller_id', $vendor->id)->count();
        
        $reviews = Review::where('reviewable_type', 'App\\Models\\User')
            ->where('reviewable_id', $vendor->id)
            ->get();
        
        $avgRating = $reviews->avg('rating') ?? 0;
        $reviewCount = $reviews->count();

        // Order stats
        $orderItems = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_id', $vendor->id)
            ->select('order_items.*');

        $totalOrders = $orderItems->count();
        
        // Calculate return rate (canceled/returned orders)
        $completedOrders = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('products.seller_id', $vendor->id)
            ->whereIn('orders.status', ['completed', 'delivered'])
            ->count();
        
        $returnedOrders = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('products.seller_id', $vendor->id)
            ->whereIn('orders.status', ['canceled', 'returned'])
            ->count();

        $returnRate = $totalOrders > 0 ? ($returnedOrders / $totalOrders) * 100 : 0;

        // Delivery stats (mock - you can calculate from real shipping data)
        $avgDeliveryDays = rand(2, 5);
        $avgResponseTime = rand(1, 24);

        $data = [
            'id' => $vendor->id,
            'name' => $vendor->name,
            'email' => $vendor->email,
            'logo_url' => $vendor->logo_url ?? null,
            'created_at' => $vendor->created_at,
            'products_count' => $productsCount,
            'avg_rating' => round($avgRating, 2),
            'review_count' => $reviewCount,
            'total_orders' => $totalOrders,
            'return_rate' => round($returnRate, 2),
            'avg_delivery_days' => $avgDeliveryDays,
            'avg_response_time' => $avgResponseTime,
            'shipping_options' => ['PTT Kargo', 'Aras Kargo', 'Yurtiçi Kargo'],
            'payment_methods' => ['Kredi Kartı', 'Havale/EFT', 'Kapıda Ödeme']
        ];

        if ($detailed) {
            // Add more detailed info
            $data['description'] = $vendor->description ?? '';
            $data['address'] = $vendor->address ?? '';
            $data['phone'] = $vendor->phone ?? '';
            $data['total_sales'] = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.seller_id', $vendor->id)
                ->sum(DB::raw('order_items.quantity * order_items.price'));
            
            $data['top_products'] = Product::where('seller_id', $vendor->id)
                ->orderBy('sales_count', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'price', 'image_url', 'sales_count']);
        }

        return $data;
    }
}
