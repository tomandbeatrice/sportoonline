<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\VendorExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class VendorExportController extends Controller
{
    /**
     * Vendor branding bilgilerini Excel olarak dışa aktarır.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportBranding(Request $request)
    {
        $fileName = 'vendor_branding_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new VendorExport, $fileName);
    }
}