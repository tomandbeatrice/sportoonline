<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderLog;
use App\Models\Admin;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderLogController extends Controller
{
    /**
     * Export all order logs as CSV.
     */
    public function export(): StreamedResponse
    {
        $fileName = 'order_logs_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $logs = OrderLog::with(['admin', 'order'])->latest()->get();
        $columns = ['Sipariş No', 'Admin', 'Durum (Eski)', 'Durum (Yeni)', 'İşlem Tipi', 'Açıklama', 'Tarih'];

        $callback = function () use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->order_id,
                    optional($log->admin)->name,
                    $log->old_status,
                    $log->new_status,
                    $log->action_type,
                    $log->note,
                    $log->created_at->format('d.m.Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show all logs filtered by admin.
     */
    public function filterByAdmin($adminId): View
    {
        $logs = OrderLog::with(['admin', 'order'])
            ->where('admin_id', $adminId)
            ->latest()
            ->get();

        $admin = Admin::findOrFail($adminId);

        return view('admin.order-logs.by-admin', compact('logs', 'admin'));
    }
}