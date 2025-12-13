<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Services\ReturnService;
use App\Mail\ReturnShippingCode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class SellerReturnController extends Controller
{
    protected ReturnService $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    /**
     * Satıcının iade taleplerini listele
     */
    public function index(Request $request): JsonResponse
    {
        $vendorId = auth()->user()->vendor_id ?? auth()->user()->vendor?->id;
        
        if (!$vendorId) {
            return response()->json(['message' => 'Satıcı bulunamadı.'], 403);
        }

        $query = ReturnRequest::with(['user', 'order', 'orderItem.product'])
            ->where('vendor_id', $vendorId)
            ->orderBy('created_at', 'desc');

        // Filtreler
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('reason_category')) {
            $query->where('reason_category', $request->reason_category);
        }

        $returns = $query->paginate($request->per_page ?? 20);

        return response()->json($returns);
    }

    /**
     * Tek bir iade talebini görüntüle
     */
    public function show($id): JsonResponse
    {
        $vendorId = auth()->user()->vendor_id ?? auth()->user()->vendor?->id;

        $return = ReturnRequest::with(['user', 'order', 'orderItem.product', 'logs.user'])
            ->where('vendor_id', $vendorId)
            ->findOrFail($id);

        return response()->json($return);
    }

    /**
     * İade talebini onayla
     */
    public function approve(Request $request, $id): JsonResponse
    {
        $vendorId = auth()->user()->vendor_id ?? auth()->user()->vendor?->id;
        $return = ReturnRequest::where('vendor_id', $vendorId)->findOrFail($id);

        $validated = $request->validate([
            'vendor_notes' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:500',
            'return_shipping_code' => 'nullable|string|max:100',
            'return_shipping_carrier' => 'nullable|string|max:100',
        ]);

        try {
            $return = $this->returnService->approveReturn($return, $validated);
            
            return response()->json([
                'message' => 'İade talebi onaylandı.',
                'return' => $return,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * İade kargo kodu gönder
     */
    public function sendShippingCode(Request $request, $id): JsonResponse
    {
        $vendorId = auth()->user()->vendor_id ?? auth()->user()->vendor?->id;
        $return = ReturnRequest::where('vendor_id', $vendorId)->findOrFail($id);

        if (!in_array($return->status, [ReturnRequest::STATUS_PENDING, ReturnRequest::STATUS_APPROVED])) {
            return response()->json([
                'message' => 'Bu durumda kargo kodu gönderilemez.',
            ], 422);
        }

        $validated = $request->validate([
            'return_shipping_code' => 'required|string|max:100',
            'return_shipping_carrier' => 'required|string|max:100',
        ]);

        $return->update([
            'return_shipping_code' => $validated['return_shipping_code'],
            'return_shipping_carrier' => $validated['return_shipping_carrier'],
        ]);

        // Log kaydı
        $return->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'shipping_code_sent',
            'notes' => "Kargo kodu gönderildi: {$validated['return_shipping_code']} ({$validated['return_shipping_carrier']})",
        ]);

        // Send notification to customer via email
        if ($return->user && $return->user->email) {
            Mail::to($return->user->email)->send(new ReturnShippingCode($return));
        }

        return response()->json([
            'message' => 'Kargo kodu müşteriye gönderildi.',
            'return' => $return->fresh(['logs']),
        ]);
    }

    /**
     * İade talebini reddet
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $vendorId = auth()->user()->vendor_id ?? auth()->user()->vendor?->id;
        $return = ReturnRequest::where('vendor_id', $vendorId)->findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'vendor_notes' => 'nullable|string|max:2000',
        ]);

        try {
            $return = $this->returnService->rejectReturn($return, $validated['rejection_reason'], $validated);
            
            return response()->json([
                'message' => 'İade talebi reddedildi.',
                'return' => $return,
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
        $vendorId = auth()->user()->vendor_id ?? auth()->user()->vendor?->id;
        $return = ReturnRequest::where('vendor_id', $vendorId)->findOrFail($id);

        $validated = $request->validate([
            'vendor_notes' => 'nullable|string|max:2000',
        ]);

        try {
            $return = $this->returnService->markAsReceived($return, $validated);
            
            return response()->json([
                'message' => 'Ürün teslim alındı.',
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
        $vendorId = auth()->user()->vendor_id ?? auth()->user()->vendor?->id;
        $return = ReturnRequest::where('vendor_id', $vendorId)->findOrFail($id);

        $validated = $request->validate([
            'inspection_notes' => 'nullable|string|max:2000',
            'approve_refund' => 'nullable|boolean',
        ]);

        try {
            $return = $this->returnService->markAsInspected($return, $validated);
            
            return response()->json([
                'message' => 'Ürün incelendi.',
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
        $vendorId = auth()->user()->vendor_id ?? auth()->user()->vendor?->id;
        $return = ReturnRequest::where('vendor_id', $vendorId)->findOrFail($id);

        $validated = $request->validate([
            'vendor_notes' => 'required|string|max:2000',
        ]);

        $return->update(['vendor_notes' => $validated['vendor_notes']]);

        // Log kaydı
        $return->logs()->create([
            'user_id' => auth()->id(),
            'action' => 'note_added',
            'notes' => 'Satıcı notu eklendi.',
        ]);

        return response()->json([
            'message' => 'Not eklendi.',
            'return' => $return->fresh(['logs']),
        ]);
    }

    /**
     * İstatistikler
     */
    public function statistics(): JsonResponse
    {
        $vendorId = auth()->user()->vendor_id ?? auth()->user()->vendor?->id;
        $stats = $this->returnService->getStatistics($vendorId);

        return response()->json($stats);
    }
}
