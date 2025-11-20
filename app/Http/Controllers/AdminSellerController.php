<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSellerController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with(['user'])
            ->withCount(['products', 'orders'])
            ->withSum('orders as total_revenue', 'total_amount');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('store_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
            case 'revenue_high':
                $query->orderByDesc('total_revenue');
                break;
            case 'revenue_low':
                $query->orderBy('total_revenue', 'asc');
                break;
            case 'orders_high':
                $query->orderByDesc('orders_count');
                break;
            case 'orders_low':
                $query->orderBy('orders_count', 'asc');
                break;
            default: // newest
                $query->orderByDesc('created_at');
                break;
        }

        $sellers = $query->paginate(20);

        return response()->json($sellers);
    }

    public function stats()
    {
        $stats = [
            'active' => Vendor::where('status', 'active')->count(),
            'inactive' => Vendor::where('status', 'inactive')->count(),
            'suspended' => Vendor::where('status', 'suspended')->count(),
            'total' => Vendor::count(),
        ];

        return response()->json($stats);
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['user'])
            ->loadCount(['products', 'orders'])
            ->loadSum('orders as total_revenue', 'total_amount');

        // Calculate average rating if reviews exist
        $vendor->avg_rating = $vendor->reviews()->avg('rating');

        return response()->json($vendor);
    }

    public function suspend(Vendor $vendor)
    {
        $vendor->update(['status' => 'suspended']);

        return response()->json([
            'message' => 'Satıcı askıya alındı',
            'seller' => $vendor
        ]);
    }

    public function activate(Vendor $vendor)
    {
        $vendor->update(['status' => 'active']);

        return response()->json([
            'message' => 'Satıcı aktif edildi',
            'seller' => $vendor
        ]);
    }

    public function bulkActivate(Request $request)
    {
        $request->validate([
            'seller_ids' => 'required|array',
            'seller_ids.*' => 'exists:vendors,id'
        ]);

        Vendor::whereIn('id', $request->seller_ids)
            ->update(['status' => 'active']);

        return response()->json([
            'message' => count($request->seller_ids) . ' satıcı aktif edildi'
        ]);
    }

    public function bulkSuspend(Request $request)
    {
        $request->validate([
            'seller_ids' => 'required|array',
            'seller_ids.*' => 'exists:vendors,id'
        ]);

        Vendor::whereIn('id', $request->seller_ids)
            ->update(['status' => 'suspended']);

        return response()->json([
            'message' => count($request->seller_ids) . ' satıcı askıya alındı'
        ]);
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'store_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'tax_number' => 'sometimes|string|max:20',
            'tax_office' => 'sometimes|string|max:255',
            'company_address' => 'sometimes|string',
            'bank_name' => 'sometimes|string|max:255',
            'iban' => 'sometimes|string|max:32',
            'account_holder' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:active,inactive,suspended',
        ]);

        $vendor->update($validated);

        return response()->json([
            'message' => 'Satıcı bilgileri güncellendi',
            'seller' => $vendor
        ]);
    }
}
