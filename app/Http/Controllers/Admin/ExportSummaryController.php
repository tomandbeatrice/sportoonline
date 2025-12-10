<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Services\ExportSummaryService;
use PDF;

class ExportSummaryController extends Controller
{
    public function summaryView(Request $request)
    {
        $vendors = Vendor::all();
        $summary = (new ExportSummaryService)->getSummary($request->vendor, $request->date);

        return view('admin.exports.summary', compact('vendors', 'summary'));
    }

    public function downloadSummary(Request $request)
    {
        $vendorId = $request->vendor;
        $summaryData = (new ExportSummaryService)->getSummary($vendorId);

        $pdf = PDF::loadView('admin.exports.summary_pdf', ['summary' => $summaryData]);

        return $pdf->download("vendor_{$vendorId}_summary.pdf");
    }
}