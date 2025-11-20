<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\VendorAccessLogService;

class VendorAccessLogController extends Controller
{
    protected $service;

    public function __construct(VendorAccessLogService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $logs = $this->service->filterLogs($request->all());
        return view('admin.vendor.logs.index', compact('logs'));
    }

    public function stats(int $vendorId)
    {
        $stats = $this->service->getStats($vendorId);
        return view('admin.vendor.logs.stats', compact('stats'));
    }
}