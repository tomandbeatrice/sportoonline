<?php

namespace App\Http\Controllers;

use App\Models\InvoiceExport;
use App\Models\InvoiceExportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoiceExportController extends Controller
{
    // Export geçmişi (InvoiceExport tablosu)
    public function listExports(Request $request)
    {
        $query = InvoiceExport::with('vendor');

        if ($request->filled('type') && in_array($request->type, ['pdf', 'csv', 'xlsx'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from')) {
            $query->whereDate('exported_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('exported_at', '<=', $request->to);
        }

        $exports = $query->orderByDesc('exported_at')->paginate(50);
        return view('invoices.exports.index', compact('exports'));
    }

    // Log paneli (InvoiceExportLog tablosu)
    public function listLogs(Request $request)
    {
        $query = InvoiceExportLog::query();

        if ($request->filled('vendor')) {
            $query->where('vendor_slug', 'LIKE', '%' . $request->vendor . '%');
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->latest()->paginate(50);
        return view('admin.invoices.index', compact('logs'));
    }

    // ZIP dosyasını token ile indir
    public function download($token)
    {
        try {
            $id = Crypt::decrypt($token);
            $export = InvoiceExport::findOrFail($id);
            $disk = config('filesystems.export_disk', 'local');

            if (!Storage::disk($disk)->exists($export->zip_path)) {
                Log::warning('ZIP dosyası bulunamadı', [
                    'user' => auth()->user()?->email,
                    'path' => $export->zip_path,
                    'token' => $token,
                ]);
                return back()->with('error', 'ZIP dosyası bulunamadı.');
            }

            Log::info('Export ZIP indirildi', [
                'user' => auth()->user()?->email,
                'file' => $export->zip_path,
                'vendor' => $export->vendor_slug,
                'token' => $token,
                'time' => now()->toDateTimeString(),
            ]);

            return Storage::disk($disk)->download($export->zip_path);
        } catch (\Exception $e) {
            Log::error('ZIP indirme hatası', [
                'token' => $token,
                'error' => $e->getMessage(),
                'user' => auth()->user()?->email,
            ]);
            return back()->with('error', 'Geçersiz indirme anahtarı.');
        }
    }
}