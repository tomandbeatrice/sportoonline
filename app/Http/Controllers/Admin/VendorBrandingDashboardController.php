<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExportLog;
use App\Models\Vendor;

class VendorBrandingDashboardController extends Controller
{
    public function index()
    {
        $logs = ExportLog::where('type', 'branding')->latest()->take(10)->get();
        $vendors = Vendor::select('id', 'name', 'branding_color', 'branding_font', 'branding_logo')->get();

        return view('admin.vendor.branding-dashboard', compact('logs', 'vendors'));
    }
}