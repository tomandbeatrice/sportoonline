<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Transfer;
use App\Models\FoodOrder;

// ============================================
// User Channels
// ============================================

// Order updates for specific user
Broadcast::channel('orders.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Food order updates for specific user
Broadcast::channel('food-orders.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Reservation updates for specific user
Broadcast::channel('reservations.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Transfer updates for specific user
Broadcast::channel('transfers.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// User notifications
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Direct messages
Broadcast::channel('message.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// ============================================
// Seller Channels
// ============================================

// Seller orders
Broadcast::channel('seller.orders.{sellerId}', function ($user, $sellerId) {
    return $user->hasRole('seller') && (int) $user->id === (int) $sellerId;
});

// Restaurant orders
Broadcast::channel('restaurant.orders.{restaurantId}', function ($user, $restaurantId) {
    if ($user->hasRole('admin')) return true;
    
    return $user->hasRole('seller') && 
           $user->restaurants()->where('id', $restaurantId)->exists();
});

// Hotel reservations
Broadcast::channel('hotel.reservations.{hotelId}', function ($user, $hotelId) {
    if ($user->hasRole('admin')) return true;
    
    return $user->hasRole('seller') && 
           $user->hotels()->where('id', $hotelId)->exists();
});

// Driver transfers
Broadcast::channel('driver.transfers.{driverId}', function ($user, $driverId) {
    if ($user->hasRole('admin')) return true;
    
    return $user->driver && (int) $user->driver->id === (int) $driverId;
});

// ============================================
// Admin Channels (Public for admins)
// ============================================

// Admin dashboard - all admins can subscribe
Broadcast::channel('admin.dashboard', function ($user) {
    return $user->hasRole('admin');
});

// Admin orders channel
Broadcast::channel('admin.orders', function ($user) {
    return $user->hasRole('admin');
});

// Admin food orders channel
Broadcast::channel('admin.food-orders', function ($user) {
    return $user->hasRole('admin');
});

// Admin reservations channel
Broadcast::channel('admin.reservations', function ($user) {
    return $user->hasRole('admin');
});

// Admin transfers channel
Broadcast::channel('admin.transfers', function ($user) {
    return $user->hasRole('admin');
});

// ============================================
// Legacy Channels (Backward Compatibility)
// ============================================

Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    $order = Order::find($orderId);
    if (!$order) return false;
    
    // Owner, seller, or admin can access
    return (int) $user->id === (int) $order->user_id ||
           (int) $user->id === (int) $order->seller_id ||
           $user->hasRole('admin');
});
