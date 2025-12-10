<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Campaign;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class UnifiedDashboardController extends Controller
{
    public function recentActivities()
    {
        // Combine recent orders, new users, and new products into a single activity feed
        $orders = Order::with('user')->latest()->take(5)->get()->map(function ($order) {
            return [
                'id' => 'order-' . $order->id,
                'type' => 'order',
                'description' => "Yeni sipariş alındı: #{$order->id} - {$order->user->name}",
                'time' => $order->created_at->diffForHumans(),
                'timestamp' => $order->created_at
            ];
        });

        $users = User::latest()->take(5)->get()->map(function ($user) {
            return [
                'id' => 'user-' . $user->id,
                'type' => 'system',
                'description' => "Yeni üye katıldı: {$user->name}",
                'time' => $user->created_at->diffForHumans(),
                'timestamp' => $user->created_at
            ];
        });

        $activities = $orders->concat($users)->sortByDesc('timestamp')->take(10)->values();

        return response()->json($activities);
    }

    public function activeCampaigns()
    {
        $campaigns = Campaign::where('status', 'active')
            ->orWhere(function($q) {
                $q->where('start_date', '<=', now())
                  ->where('end_date', '>=', now());
            })
            ->take(5)
            ->get()
            ->map(function ($campaign) {
                return [
                    'id' => $campaign->id,
                    'name' => $campaign->title ?? $campaign->name,
                    'status' => 'active',
                    'roi' => rand(120, 350) // Placeholder calculation for ROI
                ];
            });

        return response()->json($campaigns);
    }

    public function performance()
    {
        // Last 7 days revenue
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $revenue = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->get()
        ->pluck('total', 'date');

        $data = $dates->map(function ($date) use ($revenue) {
            return $revenue->get($date, 0);
        });

        return response()->json([
            'labels' => $dates->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M')),
            'datasets' => [
                [
                    'label' => 'Gelir (₺)',
                    'data' => $data,
                    'borderColor' => '#4F46E5',
                    'tension' => 0.4
                ]
            ]
        ]);
    }

    public function topSellers()
    {
        // This requires OrderItem -> Product -> Seller relationship
        // Simplified version using Vendor model directly if sales data exists there
        // Or calculating from orders
        
        $sellers = Vendor::with('user')
            ->take(5)
            ->get()
            ->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->store_name ?? $vendor->user->name,
                    'sales' => rand(50, 500), // Placeholder if no real sales count column
                    'revenue' => rand(5000, 50000), // Placeholder
                    'growth' => rand(5, 25)
                ];
            });

        return response()->json($sellers);
    }

    public function pendingOrders()
    {
        $orders = Order::where('status', 'pending')
            ->with('user')
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'orderNumber' => $order->order_number ?? $order->id,
                    'customer' => $order->user->name,
                    'items' => $order->items_count ?? rand(1, 5), // Assuming items_count or placeholder
                    'total' => $order->total_amount
                ];
            });

        return response()->json($orders);
    }
}
