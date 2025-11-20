<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total_amount')
            ->withCount('favorites');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Email verified filter
        if ($request->filled('email_verified')) {
            if ($request->email_verified == '1') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Order status filter
        if ($request->filled('order_status')) {
            if ($request->order_status === 'with_orders') {
                $query->has('orders');
            } elseif ($request->order_status === 'without_orders') {
                $query->doesntHave('orders');
            }
        }

        // Date range filter
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Sorting
        switch ($request->input('sort_by', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'orders_high':
                $query->orderByDesc('orders_count');
                break;
            case 'orders_low':
                $query->orderBy('orders_count', 'asc');
                break;
            case 'spent_high':
                $query->orderByDesc('total_spent');
                break;
            case 'spent_low':
                $query->orderBy('total_spent', 'asc');
                break;
            default: // newest
                $query->orderByDesc('created_at');
                break;
        }

        $customers = $query->paginate(20);

        // Add segment and last order date to each customer
        $customers->getCollection()->transform(function ($customer) {
            $customer->segment = $this->getCustomerSegment($customer);
            $lastOrder = $customer->orders()->latest()->first();
            $customer->last_order_date = $lastOrder ? $lastOrder->created_at : null;
            return $customer;
        });

        return response()->json($customers);
    }

    public function stats()
    {
        $total = User::where('role', 'customer')->count();
        $active = User::where('role', 'customer')
            ->where('status', 'active')
            ->count();
        $verified = User::where('role', 'customer')
            ->whereNotNull('email_verified_at')
            ->count();
        $withOrders = User::where('role', 'customer')
            ->has('orders')
            ->count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // Customer segments
        $vipCustomers = User::where('role', 'customer')
            ->where(function ($q) {
                $q->has('orders', '>=', 10)
                    ->orWhereHas('orders', function ($oq) {
                        $oq->select(DB::raw('SUM(total_amount)'))
                            ->groupBy('user_id')
                            ->havingRaw('SUM(total_amount) >= 5000');
                    });
            })
            ->count();

        $regularCustomers = User::where('role', 'customer')
            ->has('orders', '>=', 3)
            ->has('orders', '<', 10)
            ->count();

        $newCustomers = User::where('role', 'customer')
            ->has('orders', '>=', 1)
            ->has('orders', '<', 3)
            ->count();

        $inactiveCustomers = User::where('role', 'customer')
            ->whereHas('orders', function ($q) {
                $q->where('created_at', '<', now()->subDays(90));
            })
            ->orDoesntHave('orders')
            ->count();

        return response()->json([
            'total' => $total,
            'active' => $active,
            'verified' => $verified,
            'withOrders' => $withOrders,
            'totalRevenue' => $totalRevenue,
            'vipCustomers' => $vipCustomers,
            'regularCustomers' => $regularCustomers,
            'newCustomers' => $newCustomers,
            'inactiveCustomers' => $inactiveCustomers,
        ]);
    }

    public function show(User $customer)
    {
        $customer->load('orders.orderItems.product', 'favorites')
            ->loadCount(['orders', 'favorites'])
            ->loadSum('orders as total_spent', 'total_amount');

        // Calculate average order value
        $customer->avg_order_value = $customer->orders_count > 0 
            ? $customer->total_spent / $customer->orders_count 
            : 0;

        // Get segment
        $customer->segment = $this->getCustomerSegment($customer);

        // Get recent orders (last 5)
        $customer->recent_orders = $customer->orders()
            ->latest()
            ->take(5)
            ->get();

        // Get last order date
        $lastOrder = $customer->orders()->latest()->first();
        $customer->last_order_date = $lastOrder ? $lastOrder->created_at : null;

        return response()->json($customer);
    }

    public function block(User $customer)
    {
        $customer->update(['status' => 'blocked']);

        return response()->json([
            'message' => 'Müşteri bloklandı',
            'customer' => $customer
        ]);
    }

    public function unblock(User $customer)
    {
        $customer->update(['status' => 'active']);

        return response()->json([
            'message' => 'Müşteri bloğu kaldırıldı',
            'customer' => $customer
        ]);
    }

    public function bulkBlock(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:users,id'
        ]);

        User::whereIn('id', $request->customer_ids)
            ->where('role', 'customer')
            ->update(['status' => 'blocked']);

        return response()->json([
            'message' => count($request->customer_ids) . ' müşteri bloklandı'
        ]);
    }

    private function getCustomerSegment(User $customer): string
    {
        $ordersCount = $customer->orders_count ?? 0;
        $totalSpent = $customer->total_spent ?? 0;

        // VIP: 10+ orders or 5000+ TL spent
        if ($ordersCount >= 10 || $totalSpent >= 5000) {
            return 'vip';
        }

        // Regular: 3-9 orders
        if ($ordersCount >= 3 && $ordersCount < 10) {
            return 'regular';
        }

        // New: 1-2 orders
        if ($ordersCount >= 1 && $ordersCount < 3) {
            return 'new';
        }

        // Inactive: No orders or last order > 90 days ago
        $lastOrder = $customer->orders()->latest()->first();
        if (!$lastOrder || $lastOrder->created_at->lt(now()->subDays(90))) {
            return 'inactive';
        }

        return 'inactive';
    }
}
