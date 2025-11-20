<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ExportLogsToCsvJob;

class ExportLogController extends Controller
{
    /**
     * Export loglarını filtreli listele
     */
    public function index(Request $request)
    {
        $query = ExportLog::query();

        if ($request->filled('user')) {
            $query->where('user_email', 'like', '%' . $request->user . '%');
        }

        if ($request->filled('vendor')) {
            $query->where('vendor_name', 'like', '%' . $request->vendor . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*')) {
            $logs = $query->latest()->take(100)->get()->map(function (ExportLog $log) {
                return [
                    'id' => $log->id,
                    'file_name' => $log->file_name,
                    'segment' => $log->segment,
                    'export_type' => $log->export_type,
                    'status' => $log->status,
                    'duration' => $log->duration,
                    'file_size' => $log->file_size,
                    'exported_at' => optional($log->exported_at)->toDateTimeString(),
                    'vendor' => $log->vendor ?? $log->vendor_name,
                    'vendor_name' => $log->vendor_name,
                    'user_email' => $log->user_email,
                    'action' => $log->action,
                    'created_at' => optional($log->created_at)->toDateTimeString(),
                ];
            });

            return response()->json($logs);
        }

        $logs = $query->latest()->paginate(20);

        return view('admin.export.index', compact('logs'));
    }

    /**
     * Filtrelenmiş logları anlık CSV olarak indir
     */
    public function download(Request $request)
    {
        $query = ExportLog::query();

        if ($request->filled('user')) {
            $query->where('user_email', 'like', '%' . $request->user . '%');
        }

        if ($request->filled('vendor')) {
            $query->where('vendor_name', 'like', '%' . $request->vendor . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->latest()->get();

        $csvData = [];
        $csvData[] = ['Dosya', 'Kullanıcı', 'Vendor', 'IP', 'Tarih', 'İşlem'];

        foreach ($logs as $log) {
            $csvData[] = [
                $log->file_name,
                $log->user_email,
                $log->vendor_name,
                $log->ip,
                $log->created_at,
                $log->action,
            ];
        }

        $filename = 'export_logs_' . now()->format('Ymd_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Response::make($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Export işlemini queue ile arka planda başlat
     */
    public function queueDownload(Request $request)
    {
        $filters = $request->only(['user', 'vendor', 'date']);
        $filename = 'export_logs_' . now()->format('Ymd_His') . '.csv';

        ExportLogsToCsvJob::dispatch($filters, $filename);

        return back()->with('status', "Export işlemi başlatıldı. Dosya hazır olduğunda 'storage/app/exports/{$filename}' altında bulunabilir.");
    }

    /**
     * Haftalık cleanup loglarını JSON olarak döndür
     */
    public function summary()
    {
        $logs = collect(Storage::files('exports/logs'))
            ->filter(fn($file) => str_ends_with($file, '.json'))
            ->map(function ($file) {
                $content = json_decode(Storage::get($file), true);
                return [
                    'week' => basename($file, '.json'),
                    'size_mb' => $content['total_size_mb'] ?? 0,
                    'count' => $content['deleted_count'] ?? 0,
                ];
            });

        return response()->json($logs);
    }
}