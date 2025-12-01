<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\VendorPdfBrandingService;

class VendorPdfBrandingController extends Controller
{
    protected $service;

    public function __construct(VendorPdfBrandingService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $vendorId = $request->query('vendor');
        $titles = $this->service->extractTitles($vendorId);
        $grouped = $this->service->groupTitles($titles);

        return view('admin.vendor.pdf_branding', compact('vendorId', 'titles', 'grouped'));
    }
}