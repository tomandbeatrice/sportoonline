<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentExportLog;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class PaymentLogController extends Controller
{
    /**
     * List all payments with statistics
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'order']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhere('id', $search)
                  ->orWhere('order_id', $search);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderByDesc('created_at')->paginate(20);

        // Statistics
        $stats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_withdrawals' => Payment::where('type', 'withdrawal')->where('status', 'pending')->sum('amount'),
            'today_transactions' => Payment::whereDate('created_at', today())->count(),
            'monthly_commission' => Payment::where('type', 'commission')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        return response()->json([
            'transactions' => $transactions,
            'stats' => $stats,
        ]);
    }

    /**
     * Approve a pending payment/withdrawal
     */
    public function approve($id)
    {
        $payment = Payment::findOrFail($id);
        
        if ($payment->status !== 'pending') {
            return response()->json(['error' => 'Bu işlem zaten işlenmiş'], 400);
        }

        $payment->update([
            'status' => 'completed',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ödeme onaylandı',
            'payment' => $payment->fresh(),
        ]);
    }

    /**
     * Reject a pending payment/withdrawal
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $payment = Payment::findOrFail($id);
        
        if ($payment->status !== 'pending') {
            return response()->json(['error' => 'Bu işlem zaten işlenmiş'], 400);
        }

        $payment->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ödeme reddedildi',
            'payment' => $payment->fresh(),
        ]);
    }

    public function exportCsv(Request $request)
    {
        $query = Payment::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderByDesc('created_at')->get();

        $csvData = [];
        $csvData[] = ['ID', 'User ID', 'Order ID', 'Method', 'Amount', 'Status', 'Paid At', 'Created At'];

        foreach ($payments as $p) {
            $csvData[] = [
                $p->id,
                $p->user_id,
                $p->order_id,
                $p->method,
                number_format($p->amount, 2, '.', ''),
                $p->status,
                $p->paid_at,
                $p->created_at
            ];
        }

        $filename = 'payment_logs_' . now()->format('Ymd_His') . '.csv';

        // 🧠 Export işlemini logla
        PaymentExportLog::create([
            'filename' => $filename,
            'filters' => $request->all(),
            'admin_id' => auth()->id(),
            'exported_at' => now()
        ]);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\""
        ];

        $callback = function () use ($csvData) {
            $handle = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}