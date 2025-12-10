<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Services\ReturnService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminReturnController extends Controller
{
    protected ReturnService $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    /**
     * Tüm iade taleplerini listele
     */
    public function index(Request $request): JsonResponse
    {
        $query = ReturnRequest::with(['user', 'order', 'orderItem.product', 'vendor'])
            ->orderBy('created_at', 'desc');

        // Filtreler
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('refund_status')) {
            $query->where('refund_status', $request->refund_status);
        }

        if ($request->has('reason_category')) {
            $query->where('reason_category', $request->reason_category);
        }

        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('order', function ($q) use ($search) {
                        $q->where('order_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $returns = $query->paginate($request->per_page ?? 20);

        return response()->json($returns);
    }

    /**
     * Tek bir iade talebini görüntüle
     */
    public function show($id): JsonResponse
    {
        $return = ReturnRequest::with([
            'user',
            'order.items.product',
            'orderItem.product',
            'vendor',
            'logs.user',
            'approvedBy',
            'rejectedBy',
        ])->findOrFail($id);

        return response()->json($return);
    }

    /**
     * İade talebini onayla
     */
    public function approve(Request $request, $id): JsonResponse
    {
        $return = ReturnRequest::findOrFail($id);

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
            'refund_method' => 'nullable|string|in:original,wallet,bank_transfer',
            'generate_shipping_label' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $return = $this->returnService->approveReturn($return, $validated);
            
            return response()->json([
                'message' => 'İade talebi onaylandı.',
                'return' => $return->load(['logs']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * İade talebini reddet
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $return = ReturnRequest::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        try {
            $return = $this->returnService->rejectReturn($return, $validated['rejection_reason'], $validated);
            
            return response()->json([
                'message' => 'İade talebi reddedildi.',
                'return' => $return->load(['logs']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Ürün teslim alındı olarak işaretle
     */
    public function markAsReceived(Request $request, $id): JsonResponse
    {
        $return = ReturnRequest::findOrFail($id);

        $validated = $request->validate([
            'vendor_notes' => 'nullable|string|max:2000',
        ]);

        try {
            $return = $this->returnService->markAsReceived($return, $validated);
            
            return response()->json([
                'message' => 'Ürün teslim alındı olarak işaretlendi.',
                'return' => $return,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Ürün incelendi olarak işaretle
     */
    public function markAsInspected(Request $request, $id): JsonResponse
    {
        $return = ReturnRequest::findOrFail($id);

        $validated = $request->validate([
            'inspection_notes' => 'nullable|string|max:2000',
            'approve_refund' => 'nullable|boolean',
        ]);

        try {
            $return = $this->returnService->markAsInspected($return, $validated);
            
            return response()->json([
                'message' => 'Ürün inceleme tamamlandı.',
                'return' => $return,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Para iadesini başlat
     */
    public function initiateRefund(Request $request, $id): JsonResponse
    {
        $return = ReturnRequest::findOrFail($id);

        $validated = $request->validate([
            'refund_method' => 'nullable|string|in:original,wallet,bank_transfer',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        // İade tutarını güncelle
        if (!empty($validated['refund_amount'])) {
            $return->update(['refund_amount' => $validated['refund_amount']]);
        }

        try {
            $return = $this->returnService->initiateRefund($return, $validated);
            
            return response()->json([
                'message' => 'Para iadesi başlatıldı.',
                'return' => $return,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Para iadesini tamamla (manuel)
     */
    public function completeRefund(Request $request, $id): JsonResponse
    {
        $return = ReturnRequest::findOrFail($id);

        try {
            $return = $this->returnService->completeRefund($return);
            
            return response()->json([
                'message' => 'Para iadesi tamamlandı.',
                'return' => $return,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * İade sürecini tamamla
     */
    public function complete(Request $request, $id): JsonResponse
    {
        $return = ReturnRequest::findOrFail($id);

        try {
            $return = $this->returnService->completeReturn($return);
            
            return response()->json([
                'message' => 'İade süreci tamamlandı.',
                'return' => $return,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Not ekle
     */
    public function addNote(Request $request, $id): JsonResponse
    {
        $return = ReturnRequest::findOrFail($id);

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:2000',
        ]);

        $return->update(['admin_notes' => $validated['admin_notes']]);

        // Log kaydı
        $return->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'note_added',
            'notes' => 'Admin notu eklendi.',
        ]);

        return response()->json([
            'message' => 'Not eklendi.',
            'return' => $return->fresh(['logs']),
        ]);
    }

    /**
     * İstatistikler
     */
    public function statistics(Request $request): JsonResponse
    {
        $vendorId = $request->has('vendor_id') ? $request->vendor_id : null;
        $stats = $this->returnService->getStatistics($vendorId);

        return response()->json($stats);
    }

    /**
     * Dışa aktar
     */
    public function export(Request $request): JsonResponse
    {
        $query = ReturnRequest::with(['user', 'order', 'orderItem.product', 'vendor']);

        // Aynı filtreler
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $returns = $query->get();

        // CSV formatında döndür (basit örnek)
        $data = $returns->map(function ($r) {
            return [
                'İade No' => $r->return_number,
                'Sipariş No' => $r->order?->order_number,
                'Müşteri' => $r->user?->name,
                'Ürün' => $r->orderItem?->product?->name ?? 'Tüm Sipariş',
                'Sebep' => $r->reason_category_label,
                'Durum' => $r->status_label,
                'Tutar' => $r->refund_amount,
                'İade Durumu' => $r->refund_status_label,
                'Tarih' => $r->created_at->format('d.m.Y H:i'),
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => $returns->count(),
        ]);
    }
}
