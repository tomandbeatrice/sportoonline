<?php

namespace App\Services;

use App\Models\VendorAccessLog;
use Illuminate\Support\Facades\DB;

class VendorAccessLogService
{
    /**
     * Vendor bazlı erişim loglarını filtrele
     */
    public function filterLogs(array $filters)
    {
        return VendorAccessLog::query()
            ->when($filters['vendor_id'] ?? null, fn($q, $id) => $q->where('vendor_id', $id))
            ->when($filters['date_from'] ?? null, fn($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['date_to'] ?? null, fn($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->orderByDesc('created_at')
            ->paginate(50);
    }

    /**
     * Vendor bazlı istatistikleri getir
     */
    public function getStats(int $vendorId)
    {
        return [
            'total_access' => VendorAccessLog::where('vendor_id', $vendorId)->count(),
            'last_access' => VendorAccessLog::where('vendor_id', $vendorId)->latest()->first()?->created_at,
            'daily_access' => VendorAccessLog::select(DB::raw('DATE(created_at) as day'), DB::raw('count(*) as count'))
                ->where('vendor_id', $vendorId)
                ->groupBy('day')
                ->orderByDesc('day')
                ->limit(14)
                ->get(),
        ];
    }
}