<?php

namespace App\Modules\Seller\Services;

use App\Models\User;
use App\Models\SellerApplication;
use Illuminate\Support\Facades\Cache;

/**
 * Seller Service
 * Handles seller-related business logic
 */
class SellerService
{
    /**
     * Get seller dashboard stats
     */
    public function getDashboardStats(int $sellerId): array
    {
        return Cache::remember("seller.{$sellerId}.stats", 1800, function () use ($sellerId) {
            $seller = User::findOrFail($sellerId);
            
            return [
                'total_products' => $seller->products()->count(),
                'total_orders' => $seller->orders()->count(),
                'total_revenue' => $seller->orders()->sum('total_price'),
                'pending_orders' => $seller->orders()->where('status', 'pending')->count(),
            ];
        });
    }
    
    /**
     * Apply as seller
     */
    public function applyAsSeller(int $userId, array $data): SellerApplication
    {
        return SellerApplication::create([
            'user_id' => $userId,
            'business_name' => $data['business_name'],
            'tax_number' => $data['tax_number'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'status' => 'pending',
        ]);
    }
    
    /**
     * Approve seller application
     */
    public function approveApplication(int $applicationId): SellerApplication
    {
        $application = SellerApplication::findOrFail($applicationId);
        
        $application->update(['status' => 'approved']);
        
        // Update user role to seller
        $user = User::findOrFail($application->user_id);
        $user->update(['role' => 'seller']);
        
        // Clear cache
        Cache::forget("seller.{$user->id}.stats");
        
        return $application->fresh();
    }
}
