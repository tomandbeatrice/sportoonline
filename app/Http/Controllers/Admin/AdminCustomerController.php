<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        // Assuming 'customer' role or just users who are not admins/sellers
        $query = User::where('role', 'customer');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(20);

        $data = $customers->getCollection()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status ?? 'active',
                'totalSpent' => 0, // Implement Order sum
                'orderCount' => 0, // Implement Order count
                'lastOrderDate' => '-',
                'segment' => 'New', // Implement segmentation logic
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name),
            ];
        });

        return response()->json($customers->setCollection($data));
    }

    public function stats()
    {
        return response()->json([
            'total' => User::where('role', 'customer')->count(),
            'active' => User::where('role', 'customer')->where('status', 'active')->count(),
            'blocked' => User::where('role', 'customer')->where('status', 'blocked')->count(),
        ]);
    }

    public function show($id)
    {
        return User::findOrFail($id);
    }

    public function block($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'blocked']);
        return response()->json(['message' => 'User blocked']);
    }

    public function unblock($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);
        return response()->json(['message' => 'User unblocked']);
    }
}
