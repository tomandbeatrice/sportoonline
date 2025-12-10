<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Services\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class OrderController extends Controller
{
    /**
     * Sipariş oluşturur
     */
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'nullable|exists:user_addresses,id',
            'address' => 'required_without:address_id|string|max:255',
            'phone' => 'nullable|string|max:20',
            'note' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|string|in:stripe,iyzico,paytr,test',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        // Eager load products to get their prices
        $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Sepet boş'], 400);
        }

        // Get address details
        $shippingAddress = null;
        $shippingName = auth()->user()->name;
        $shippingPhone = $request->phone ?? auth()->user()->phone;
        
        if ($request->address_id) {
            $address = \App\Models\UserAddress::where('user_id', auth()->id())
                ->findOrFail($request->address_id);
            
            $shippingAddress = $address->formatted_address;
            $shippingName = $address->full_name;
            $shippingPhone = $address->phone;
        } else {
            $shippingAddress = $request->address;
        }

        // Get commission rate from config
        $commissionRate = Config::get('marketplace.commission_rate', 0.15);
        $orderTotal = 0;
        $discountAmount = 0;

        // Validate and apply campaign discount
        if ($request->campaign_id) {
            $campaign = \App\Models\Campaign::find($request->campaign_id);
            
            if ($campaign && $campaign->isCurrentlyActive() && $campaign->canBeUsedBy(auth()->id())) {
                $discountAmount = $request->discount_amount ?? 0;
            }
        }

        DB::beginTransaction();

        try {
            // First, calculate the total price of the order
            foreach ($cartItems as $item) {
                if (!$item->product) {
                    // Handle case where product might have been deleted
                    throw new \Exception("Ürün bulunamadı: ID " . $item->product_id);
                }
                $orderTotal += $item->quantity * $item->product->price;
            }

            // Create the main order record
            $order = Order::create([
                'user_id' => auth()->id(),
                'name' => $shippingName,
                'email' => auth()->user()->email,
                'phone' => $shippingPhone,
                'address' => $shippingAddress,
                'shipping_address' => $shippingAddress,
                'shipping_name' => $shippingName,
                'shipping_phone' => $shippingPhone,
                'billing_address' => $shippingAddress,
                'payment_method' => $request->payment_method ?? 'pending',
                'payment_status' => 'pending',
                'total_price' => max(0, $orderTotal - $discountAmount),
                'total' => max(0, $orderTotal - $discountAmount),
                'subtotal' => $orderTotal,
                'total_amount' => max(0, $orderTotal - $discountAmount),
                'status' => 'pending',
                'notes' => $request->note ?? $request->notes,
                'campaign_id' => $request->campaign_id,
                'discount_amount' => $discountAmount,
            ]);

            // Create order items with commission calculation
            foreach ($cartItems as $item) {
                $lineTotal = $item->quantity * $item->product->price;
                $platformFee = $lineTotal * $commissionRate;
                $sellerPayout = $lineTotal - $platformFee;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'unit_price' => $item->product->price,
                    'total_price' => $lineTotal,
                    'status' => 'pending',
                    'platform_fee' => $platformFee,
                    'seller_payout' => $sellerPayout,
                ]);
            }

            // Clear the user's cart
            Cart::where('user_id', auth()->id())->delete();

            // Record campaign usage if applicable
            if ($request->campaign_id && $discountAmount > 0) {
                $campaign = \App\Models\Campaign::find($request->campaign_id);
                if ($campaign) {
                    \App\Models\CampaignUsage::create([
                        'campaign_id' => $campaign->id,
                        'user_id' => auth()->id(),
                        'order_id' => $order->id,
                        'discount_amount' => $discountAmount,
                    ]);
                    $campaign->incrementUsage();
                }
            }

            // Send order confirmation email
            try {
                \Mail::to($order->email)->send(new \App\Mail\OrderConfirmation($order));
            } catch (\Exception $e) {
                \Log::error('Order confirmation email failed: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sipariş başarıyla oluşturuldu',
                'order' => $order->load('items'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Sipariş oluşturulamadı',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sipariş listesi
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items.product')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($orders);
    }

    /**
     * Aktif siparişler (devam eden)
     */
    public function active()
    {
        $orders = Order::where('user_id', auth()->id())
            ->whereNotIn('status', ['delivered', 'cancelled', 'completed', 'refunded'])
            ->with('items.product')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json($orders);
    }

    /**
     * Sipariş detayları
     */
    public function show($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('items.product')
            ->firstOrFail();

        return response()->json($order);
    }

    /**
     * Satıcı sipariş durumunu günceller
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        // Verify seller owns products in this order
        $sellerProducts = $order->items()
            ->whereHas('product', function($q) {
                $q->where('seller_id', auth()->id());
            })
            ->count();

        if ($sellerProducts === 0) {
            return response()->json(['error' => 'Bu siparişte satıcınıza ait ürün bulunmuyor'], 403);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Sipariş durumu güncellendi',
            'order' => $order->fresh()
        ]);
    }

    /**
     * Satıcı kargo bilgilerini ekler
     */
    public function addShipping(Request $request, Order $order)
    {
        $request->validate([
            'shipping_company' => 'required|string|max:100',
            'shipping_tracking_number' => 'required|string|max:100',
            'status' => 'sometimes|in:shipped,delivered'
        ]);

        // Verify seller owns products in this order
        $sellerProducts = $order->items()
            ->whereHas('product', function($q) {
                $q->where('seller_id', auth()->id());
            })
            ->count();

        if ($sellerProducts === 0) {
            return response()->json(['error' => 'Bu siparişte satıcınıza ait ürün bulunmuyor'], 403);
        }

        $order->update([
            'shipping_provider' => $request->shipping_company,
            'tracking_code' => $request->shipping_tracking_number,
            'shipping_status' => 'in_transit',
            'status' => $request->status ?? 'shipped'
        ]);

        // Send shipping update email
        try {
            \Mail::to($order->email)->send(new \App\Mail\ShippingUpdate($order->fresh()));
        } catch (\Exception $e) {
            \Log::error('Shipping update email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Kargo bilgileri eklendi',
            'order' => $order->fresh()
        ]);
    }

    /**
     * Download order invoice (PDF)
     */
    public function downloadInvoice($id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);

        // Check authorization
        if ($order->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Yetkisiz erişim'], 403);
        }

        // Generate PDF using DomPDF
        $pdf = \PDF::loadView('invoices.order', ['order' => $order])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
        
        $filename = "fatura-" . str_pad($order->id, 8, '0', STR_PAD_LEFT) . ".pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Request order cancellation
     */
    public function requestCancellation(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $order = Order::findOrFail($id);

        // Check authorization
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Yetkisiz erişim'], 403);
        }

        // Check if order can be cancelled
        if (in_array($order->status, ['completed', 'delivered', 'canceled'])) {
            return response()->json([
                'error' => 'Bu sipariş iptal edilemez'
            ], 400);
        }

        // Create cancellation request
        DB::table('order_cancellation_requests')->insert([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'reason' => $request->reason,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Notify admin and seller
        try {
            // Send notification to admin
            \Notification::send(
                \App\Models\User::where('role', 'admin')->get(),
                new \App\Notifications\OrderCancellationRequested($order)
            );
        } catch (\Exception $e) {
            \Log::error('Cancellation notification failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'İptal talebiniz alındı, en kısa sürede değerlendirilecektir'
        ]);
    }
}