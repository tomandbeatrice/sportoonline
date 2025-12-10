<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class AdminSellerController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with('user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        $vendors = $query->paginate(20);

        // Transform to match frontend expectation
        $data = $vendors->getCollection()->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'storeName' => $vendor->name,
                'ownerName' => $vendor->user->name ?? 'Unknown',
                'email' => $vendor->user->email ?? '',
                'phone' => $vendor->user->phone ?? '',
                'status' => $vendor->status ?? 'active',
                'rating' => 4.5, // Mock for now, or implement Review relation
                'totalSales' => 0, // Implement Order relation sum
                'orderCount' => 0, // Implement Order relation count
                'joinDate' => $vendor->created_at->format('Y-m-d'),
                'category' => 'General',
                'logo' => $vendor->branding_logo ?? 'https://ui-avatars.com/api/?name=' . urlencode($vendor->name),
            ];
        });

        return response()->json($vendors->setCollection($data));
    }

    public function stats()
    {
        return response()->json([
            'total' => Vendor::count(),
            'active' => Vendor::where('status', 'active')->count(),
            'pending' => Vendor::where('status', 'pending')->count(),
            'suspended' => Vendor::where('status', 'suspended')->count(),
        ]);
    }

    public function show($id)
    {
        return Vendor::with('user')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->all());
        return response()->json($vendor);
    }

    public function suspend($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'suspended']);
        return response()->json(['message' => 'Vendor suspended']);
    }

    public function activate($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update(['status' => 'active']);
        return response()->json(['message' => 'Vendor activated']);
    }
}
