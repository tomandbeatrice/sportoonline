<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusChanged;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'vendor'])
            ->withCount('orderItems as items_count');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Vendor filter
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
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
            case 'amount_high':
                $query->orderByDesc('total_amount');
                break;
            case 'amount_low':
                $query->orderBy('total_amount', 'asc');
                break;
            default: // newest
                $query->orderByDesc('created_at');
                break;
        }

        $orders = $query->paginate(20);

        return response()->json($orders);
    }

    public function stats()
    {
        $stats = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'totalRevenue' => Order::where('status', '!=', 'cancelled')
                ->sum('total_amount'),
        ];

        return response()->json($stats);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'vendor', 'orderItems.product']);

        return response()->json($order);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Send email notification to customer
        if ($order->user && $order->user->email && $oldStatus !== $request->status) {
            // Get first order item for email (or create a separate Order email)
            $orderItem = $order->orderItems()->first();
            if ($orderItem) {
                Mail::to($order->user->email)->send(new OrderStatusChanged($orderItem, 'buyer'));
            }
        }

        return response()->json([
            'message' => 'Sipariş durumu güncellendi',
            'order' => $order
        ]);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        Order::whereIn('id', $request->order_ids)
            ->update(['status' => $request->status]);

        return response()->json([
            'message' => count($request->order_ids) . ' sipariş güncellendi'
        ]);
    }
}
