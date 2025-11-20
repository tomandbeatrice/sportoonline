<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\SellerApplication;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // Kullanıcıları listele
    public function users()
    {
        return response()->json(User::all());
    }

    // Ürünleri listele
    public function products()
    {
        return response()->json(Product::all());
    }

    // Siparişleri listele
    public function orders()
    {
        return response()->json(Order::all());
    }

    // Kategorileri listele
    public function categories()
    {
        return response()->json(Category::all());
    }

    // Comprehensive dashboard statistics
    public function stats()
    {
        $totalUsers = User::count();
        $totalSellers = User::where('role', 'seller')->count();
        $totalBuyers = User::where('role', 'buyer')->count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        
        // Revenue calculations
        $totalRevenue = OrderItem::sum(\DB::raw('quantity * price'));
        $last24hOrders = Order::where('created_at', '>=', now()->subDay())->count();
        $last24hRevenue = Order::where('created_at', '>=', now()->subDay())
            ->with('items')
            ->get()
            ->sum(function ($order) {
                return $order->items->sum(function ($item) {
                    return $item->quantity * $item->price;
                });
            });

        // Order status breakdown
        $pendingOrders = Order::where('status', 'pending')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        
        // Conversion and avg calculations
        $conversionRate = $totalUsers > 0 ? round(($totalOrders / $totalUsers) * 100, 2) : 0;
        $avgOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;
        
        // Active campaigns (placeholder - implement when campaign system ready)
        $activeCampaigns = 0;

        return response()->json([
            'total_users' => $totalUsers,
            'total_sellers' => $totalSellers,
            'total_buyers' => $totalBuyers,
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'total_revenue' => round($totalRevenue, 2),
            'pending_orders' => $pendingOrders,
            'shipped_orders' => $shippedOrders,
            'delivered_orders' => $deliveredOrders,
            'last_24h_orders' => $last24hOrders,
            'last_24h_revenue' => round($last24hRevenue, 2),
            'active_campaigns' => $activeCampaigns,
            'conversion_rate' => $conversionRate,
            'avg_order_value' => $avgOrderValue,
        ]);
    }

    // Real-time metrics (same as stats but optimized for polling)
    public function realtimeMetrics()
    {
        return $this->stats();
    }

    /**
     * List all seller applications/vendors.
     * Can be filtered by status.
     */
    public function listSellerApplications(Request $request)
    {
        $query = Vendor::with('user:id,name,email');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $vendors = $query->orderBy('created_at', 'desc')->get();

        return response()->json($vendors);
    }

    /**
     * Update the status of a seller application.
     * Approving a vendor also updates the user's role.
     */
    public function updateSellerStatus(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        $vendor->update(['status' => $validated['status']]);

        // If approved, update the user's role to 'seller'
        if ($validated['status'] === 'approved') {
            $vendor->user()->update(['role' => 'seller']);
        } 
        // Optional: If rejected, you might want to revert the role if it was previously 'seller'
        // else if ($validated['status'] === 'rejected') {
        //     $vendor->user()->update(['role' => 'user']);
        // }

        return response()->json([
            'message' => 'Satıcı durumu başarıyla güncellendi.',
            'vendor' => $vendor->load('user:id,name,email'),
        ]);
    }

    /**
     * Get comprehensive financial report for the platform
     */
    public function financialReport()
    {
        // Get all order items with financial data
        $allOrderItems = OrderItem::with(['order:id,status,created_at', 'product' => function($q) {
            $q->select('id', 'name', 'seller_id')->with('seller:id,name');
        }])
        ->orderBy('created_at', 'desc')
        ->get();

        // Calculate platform totals
        $totalRevenue = $allOrderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $totalPlatformFees = $allOrderItems->sum('platform_fee');
        $totalSellerPayouts = $allOrderItems->sum('seller_payout');

        // Sales by seller
        $salesBySeller = $allOrderItems->groupBy('product.seller_id')
            ->filter(fn($items, $sellerId) => $sellerId !== null)
            ->map(function ($items, $sellerId) {
                $seller = $items->first()->product->seller ?? null;
                
                return [
                    'seller_id' => $sellerId,
                    'seller_name' => $seller ? $seller->name : 'Satıcı #' . $sellerId,
                    'total_revenue' => round($items->sum(fn($i) => $i->quantity * $i->price), 2),
                    'platform_fees' => round($items->sum('platform_fee'), 2),
                    'seller_payout' => round($items->sum('seller_payout'), 2),
                    'order_count' => $items->pluck('order_id')->unique()->count(),
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();

        // Recent transactions
        $recentTransactions = $allOrderItems->take(30)->map(function ($item) {
            return [
                'id' => $item->id,
                'order_id' => $item->order_id,
                'seller_name' => $item->product && $item->product->seller ? $item->product->seller->name : 'N/A',
                'product_name' => $item->product ? $item->product->name : 'Ürün',
                'total' => round($item->quantity * $item->price, 2),
                'platform_fee' => round($item->platform_fee ?? 0, 2),
                'seller_payout' => round($item->seller_payout ?? 0, 2),
                'status' => $item->order ? $item->order->status : 'unknown',
                'date' => $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
            ];
        })->values();

        return response()->json([
            'summary' => [
                'total_revenue' => round($totalRevenue, 2),
                'total_platform_fees' => round($totalPlatformFees, 2),
                'total_seller_payouts' => round($totalSellerPayouts, 2),
                'total_orders' => $allOrderItems->pluck('order_id')->unique()->count(),
            ],
            'sales_by_seller' => $salesBySeller,
            'recent_transactions' => $recentTransactions,
        ]);
    }
}
