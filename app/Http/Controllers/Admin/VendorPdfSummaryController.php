<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\VendorPdfSummaryService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VendorPdfSummaryExport;

class VendorPdfSummaryController extends Controller
{
    protected $service;

    public function __construct(VendorPdfSummaryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $vendorId = $request->query('vendor');
        $summary = $this->service->getSummary($vendorId);

        return view('admin.vendor.pdf_summary', compact('vendorId', 'summary'));
    }

    public function export(Request $request)
    {
        $vendorId = $request->query('vendor');
        $summary = $this->service->getSummary($vendorId);

        return Excel::download(new VendorPdfSummaryExport($summary), "vendor_{$vendorId}_pdf_summary.xlsx");
    }
}