<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SegmentSheetExport;

class SegmentSheetExportController extends Controller
{
    public function export(Request $request)
    {
        $vendorId = $request->input('vendor_id');
        $segmentKey = $request->input('segment');

        $fileName = 'segment_export_' . $segmentKey . '_vendor_' . $vendorId . '.xlsx';

        return Excel::download(
            new SegmentSheetExport($vendorId, $segmentKey),
            $fileName
        );
    }
}
