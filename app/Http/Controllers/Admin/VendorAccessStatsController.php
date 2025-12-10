<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorAccessStatsController extends Controller
{
    public function index(Request $request)
    {
        $selectedVendorId = $request->input('vendor_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Vendor::with(['externalPdfAccessLogs' => function ($q) use ($startDate, $endDate) {
            if ($startDate) {
                $q->where('accessed_at', '>=', $startDate);
            }
            if ($endDate) {
                $q->where('accessed_at', '<=', $endDate);
            }
        }]);

        if ($selectedVendorId) {
            $query->where('id', $selectedVendorId);
        }

        $vendors = $query->get()->map(function ($vendor) {
            $logs = $vendor->externalPdfAccessLogs;

            return (object)[
                'id' => $vendor->id,
                'name' => $vendor->name,
                'token' => $vendor->token,
                'active' => $vendor->is_active,
                'access_count' => $logs->count(),
                'last_access' => $logs->max('accessed_at'),
                'most_accessed_file' => $logs
                    ->groupBy('file_name')
                    ->sortByDesc(fn($g) => $g->count())
                    ->keys()
                    ->first(),
            ];
        });

        $vendorList = Vendor::select('id', 'name')->orderBy('name')->get();

        return view('admin.external_pdf.vendor_stats', compact(
            'vendors', 'vendorList', 'selectedVendorId', 'startDate', 'endDate'
        ));
    }

    public function showLogs($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);

        $logs = $vendor->externalPdfAccessLogs()
            ->orderByDesc('accessed_at')
            ->take(50)
            ->get();

        return response()->json([
            'vendor' => $vendor->name,
            'logs' => $logs->map(function ($log) {
                return [
                    'ip' => $log->ip,
                    'file' => $log->file_name,
                    'accessed_at' => $log->accessed_at->format('d.m.Y H:i'),
                ];
            }),
        ]);
    }

    public function preview($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);

        $files = $vendor->externalPdfAccessLogs()
            ->select('file_name')
            ->distinct()
            ->pluck('file_name');

        return response()->json([
            'vendor' => $vendor->name,
            'files' => $files,
        ]);
    }
}