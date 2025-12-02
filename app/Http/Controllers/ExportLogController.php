<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExportLog;

class ExportLogController extends Controller
{
    /**
     * Export geçmişini vendor ve segment bazlı filtreleyerek getirir.
     * Cockpit ekranı için karar destek verisi sağlar.
     */
    public function index(Request $request)
    {
        $query = ExportLog::query()->with('vendor');

        // 🔍 Vendor filtresi
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // 🧠 Segment filtresi (campaign, feedback, score vs.)
        if ($request->filled('segment')) {
            $query->where('segment', $request->segment);
        }

        // 📊 Son 100 logu getir ve sadeleştir
        return $query->latest()->take(100)->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'vendor_name' => $log->vendor->name ?? 'Bilinmiyor',
                'segment' => $log->segment,
                'exported_at' => $log->exported_at
            ];
        });
    }
}