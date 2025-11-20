<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Mail\OrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class SellerOrderController extends Controller
{
    /**
     * Update the status of an order item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, OrderItem $orderItem)
    {
        // Get the authenticated user (seller)
        $seller = Auth::user();

        // Authorize: Check if the product in the order item belongs to the seller
        if ($orderItem->product->seller_id !== $seller->id) {
            return response()->json(['message' => 'Unauthorized. You can only update your own orders.'], 403);
        }

        // Validate the request
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            ],
        ]);

        $oldStatus = $orderItem->status;
        
        // Update the order item status
        $orderItem->update(['status' => $validated['status']]);

        // Duruma göre e-posta gönder
        if ($oldStatus !== $validated['status']) {
            $orderItem->load(['product.seller', 'order.user']);
            
            // Alıcıya (buyer) bildirim gönder
            if ($orderItem->order->user && $orderItem->order->user->email) {
                Mail::to($orderItem->order->user->email)
                    ->send(new OrderStatusChanged($orderItem, 'buyer'));
            }

            // Satıcıya (seller) bildirim gönder
            if ($orderItem->product->seller && $orderItem->product->seller->email) {
                Mail::to($orderItem->product->seller->email)
                    ->send(new OrderStatusChanged($orderItem, 'seller'));
            }
        }

        // Return a success response
        return response()->json([
            'message' => 'Order item status updated successfully.',
            'order_item' => $orderItem->fresh(), // Return the updated item
        ]);
    }
}
