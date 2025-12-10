<?php

namespace App\Http\Controllers;

use App\Models\ReturnRequest;
use App\Models\Order;
use App\Services\ReturnService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ReturnController extends Controller
{
    protected ReturnService $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    /**
     * Kullanıcının iade taleplerini listele
     */
    public function index(Request $request): JsonResponse
    {
        $query = ReturnRequest::with(['order', 'orderItem.product', 'logs'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        // Durum filtresi
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $returns = $query->paginate($request->per_page ?? 10);

        return response()->json($returns);
    }

    /**
     * Tek bir iade talebini görüntüle
     */
    public function show($id): JsonResponse
    {
        $return = ReturnRequest::with(['order', 'orderItem.product', 'vendor', 'logs.user'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json($return);
    }

    /**
     * Yeni iade talebi oluştur
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_item_id' => 'nullable|exists:order_items,id',
            'reason_category' => ['required', Rule::in([
                ReturnRequest::REASON_DEFECTIVE,
                ReturnRequest::REASON_WRONG_ITEM,
                ReturnRequest::REASON_NOT_AS_DESCRIBED,
                ReturnRequest::REASON_DAMAGED,
                ReturnRequest::REASON_SIZE_FIT,
                ReturnRequest::REASON_CHANGED_MIND,
                ReturnRequest::REASON_LATE_DELIVERY,
                ReturnRequest::REASON_OTHER,
            ])],
            'reason' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'quantity' => 'nullable|integer|min:1',
            'images' => 'nullable|array|max:5',
            'images.*' => 'string', // Base64 veya URL
            'customer_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $return = $this->returnService->createReturnRequest($validated);
            
            return response()->json([
                'message' => 'İade talebi başarıyla oluşturuldu.',
                'return' => $return->load(['order', 'orderItem.product']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Ürün gönderildi bilgisi ekle
     */
    public function markAsShipped(Request $request, $id): JsonResponse
    {
        $return = ReturnRequest::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'tracking_number' => 'required|string|max:100',
            'shipping_carrier' => 'nullable|string|max:100',
        ]);

        try {
            $return = $this->returnService->markAsShipped($return, $validated);
            
            return response()->json([
                'message' => 'Kargo bilgileri güncellendi.',
                'return' => $return,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * İade talebini iptal et
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        $return = ReturnRequest::where('user_id', auth()->id())->findOrFail($id);

        try {
            $return = $this->returnService->cancelReturn($return, $request->reason);
            
            return response()->json([
                'message' => 'İade talebi iptal edildi.',
                'return' => $return,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Sipariş için iade durumunu kontrol et
     */
    public function checkEligibility($orderId): JsonResponse
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($orderId);

        // İade yapılabilirlik kontrolü
        $eligible = in_array($order->status, ['delivered', 'completed']);
        $deliveredAt = $order->delivered_at ?? $order->updated_at;
        $daysRemaining = max(0, 14 - $deliveredAt->diffInDays(now()));

        // Her ürün için iade durumu
        $items = $order->items->map(function ($item) {
            $existingReturn = ReturnRequest::where('order_item_id', $item->id)
                ->whereNotIn('status', [ReturnRequest::STATUS_CANCELLED, ReturnRequest::STATUS_REJECTED])
                ->first();

            return [
                'id' => $item->id,
                'product' => $item->product,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'can_return' => !$existingReturn,
                'existing_return' => $existingReturn,
            ];
        });

        return response()->json([
            'eligible' => $eligible && $daysRemaining > 0,
            'days_remaining' => $daysRemaining,
            'message' => !$eligible ? 'Sipariş henüz teslim edilmemiş.' : ($daysRemaining <= 0 ? 'İade süresi dolmuş.' : null),
            'items' => $items,
        ]);
    }

    /**
     * İade sebeplerini listele
     */
    public function getReasons(): JsonResponse
    {
        return response()->json([
            ['value' => ReturnRequest::REASON_DEFECTIVE, 'label' => 'Kusurlu Ürün'],
            ['value' => ReturnRequest::REASON_WRONG_ITEM, 'label' => 'Yanlış Ürün Gönderildi'],
            ['value' => ReturnRequest::REASON_NOT_AS_DESCRIBED, 'label' => 'Açıklamaya Uygun Değil'],
            ['value' => ReturnRequest::REASON_DAMAGED, 'label' => 'Hasarlı Ürün'],
            ['value' => ReturnRequest::REASON_SIZE_FIT, 'label' => 'Beden/Boyut Uyumsuzluğu'],
            ['value' => ReturnRequest::REASON_CHANGED_MIND, 'label' => 'Fikir Değişikliği'],
            ['value' => ReturnRequest::REASON_LATE_DELIVERY, 'label' => 'Geç Teslimat'],
            ['value' => ReturnRequest::REASON_OTHER, 'label' => 'Diğer'],
        ]);
    }
}
