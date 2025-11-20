<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Campaign;
use App\Models\SellerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStatsController extends Controller
{
    public function getStats()
    {
        $stats = [
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
            'total_orders' => Order::count(),
            'active_sellers' => Vendor::where('status', 'active')->count(),
            'total_users' => User::where('role', 'customer')->count(),
            'pending_applications' => SellerApplication::where('status', 'pending')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_products' => Product::where('is_active', true)->count(),
            'active_campaigns' => Campaign::where('is_active', true)
                ->where('end_date', '>=', now())
                ->count(),
        ];

        return response()->json($stats);
    }

    public function getActivities()
    {
        $activities = collect();

        // Son siparişler
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => 'order_' . $order->id,
                    'type' => 'order',
                    'description' => ($order->user->name ?? 'Misafir') . ' yeni sipariş verdi (#' . $order->id . ')',
                    'created_at' => $order->created_at,
                ];
            });

        // Son satıcı başvuruları
        $recentApplications = SellerApplication::latest()
            ->take(5)
            ->get()
            ->map(function ($app) {
                return [
                    'id' => 'seller_' . $app->id,
                    'type' => 'seller',
                    'description' => $app->first_name . ' ' . $app->last_name . ' satıcı olarak başvurdu',
                    'created_at' => $app->created_at,
                ];
            });

        // Son eklenen ürünler
        $recentProducts = Product::with('vendor')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => 'product_' . $product->id,
                    'type' => 'product',
                    'description' => $product->vendor->store_name . ' yeni ürün ekledi: ' . $product->name,
                    'created_at' => $product->created_at,
                ];
            });

        // Son kampanyalar
        $recentCampaigns = Campaign::with('vendor')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($campaign) {
                return [
                    'id' => 'campaign_' . $campaign->id,
                    'type' => 'campaign',
                    'description' => $campaign->vendor->store_name . ' yeni kampanya başlattı: ' . $campaign->name,
                    'created_at' => $campaign->created_at,
                ];
            });

        return response()->json(
            $activities
                ->merge($recentOrders)
                ->merge($recentApplications)
                ->merge($recentProducts)
                ->merge($recentCampaigns)
                ->sortByDesc('created_at')
                ->values()
                ->take(20)
        );
    }

    public function getRecentOrders()
    {
        $orders = Order::with(['user', 'vendor'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json($orders);
    }

    public function getSystemAlerts()
    {
        $alerts = collect();

        // Bekleyen satıcı başvuruları
        $pendingApplications = SellerApplication::where('status', 'pending')->count();
        if ($pendingApplications > 0) {
            $alerts->push([
                'id' => 'pending_sellers',
                'type' => 'warning',
                'title' => 'Bekleyen Satıcı Başvuruları',
                'message' => "$pendingApplications satıcı başvurusu onay bekliyor.",
            ]);
        }

        // Düşük stoklu ürünler
        $lowStockProducts = Product::where('stock', '<=', 10)
            ->where('stock', '>', 0)
            ->count();
        if ($lowStockProducts > 0) {
            $alerts->push([
                'id' => 'low_stock',
                'type' => 'warning',
                'title' => 'Düşük Stok Uyarısı',
                'message' => "$lowStockProducts ürünün stoğu azalıyor.",
            ]);
        }

        // Bekleyen siparişler
        $pendingOrders = Order::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->count();
        if ($pendingOrders > 0) {
            $alerts->push([
                'id' => 'pending_orders',
                'type' => 'error',
                'title' => 'Bekleyen Siparişler',
                'message' => "$pendingOrders sipariş 24 saatten fazladır işlenmemiş.",
            ]);
        }

        // Bugün sona erecek kampanyalar
        $expiringCampaigns = Campaign::where('is_active', true)
            ->whereDate('end_date', now()->toDateString())
            ->count();
        if ($expiringCampaigns > 0) {
            $alerts->push([
                'id' => 'expiring_campaigns',
                'type' => 'info',
                'title' => 'Sona Eren Kampanyalar',
                'message' => "$expiringCampaigns kampanya bugün sona eriyor.",
            ]);
        }

        return response()->json($alerts);
    }

    public function getRevenueChart(Request $request)
    {
        $period = $request->input('period', '7days'); // 7days, 30days, 12months

        $query = Order::where('status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date');

        switch ($period) {
            case '7days':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
            case '30days':
                $query->where('created_at', '>=', now()->subDays(30));
                break;
            case '12months':
                $query->where('created_at', '>=', now()->subMonths(12))
                    ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as date, SUM(total_amount) as revenue')
                    ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'));
                break;
        }

        $data = $query->get();

        return response()->json($data);
    }

    public function getTopSellers(Request $request)
    {
        $limit = $request->input('limit', 10);

        $topSellers = Vendor::withCount('orders')
            ->with('user')
            ->withSum('orders as total_revenue', 'total_amount')
            ->orderByDesc('total_revenue')
            ->take($limit)
            ->get()
            ->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'store_name' => $vendor->store_name,
                    'owner' => $vendor->user->name,
                    'total_orders' => $vendor->orders_count,
                    'total_revenue' => $vendor->total_revenue ?? 0,
                ];
            });

        return response()->json($topSellers);
    }

    public function getTopProducts(Request $request)
    {
        $limit = $request->input('limit', 10);

        $topProducts = Product::with('vendor')
            ->withCount('orderItems')
            ->withSum('orderItems as total_sales', 'quantity')
            ->orderByDesc('total_sales')
            ->take($limit)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'vendor' => $product->vendor->store_name,
                    'total_orders' => $product->order_items_count,
                    'total_quantity' => $product->total_sales ?? 0,
                    'revenue' => ($product->total_sales ?? 0) * $product->price,
                ];
            });

        return response()->json($topProducts);
    }
}
