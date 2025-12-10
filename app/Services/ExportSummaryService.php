<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Vendor;

class ExportSummaryService
{
    public function getSummary($vendorId = null, $date = null)
    {
        $query = DB::table('exports')
            ->select('vendor_id', DB::raw('COUNT(*) as total_exports'), DB::raw('MAX(created_at) as last_export_date'))
            ->groupBy('vendor_id');

        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $rows = $query->get();

        // Vendor adlarını enriched şekilde ekleyelim
        return $rows->map(function ($row) {
            $vendor = Vendor::find($row->vendor_id);
            $row->vendor_name = $vendor ? $vendor->name : 'Bilinmiyor';
            return $row;
        });
    }
}