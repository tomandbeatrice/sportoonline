<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExportFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExportFileController extends Controller
{
    public function index(Request $request)
    {
        $files = ExportFile::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.export_files.index', compact('files'));
    }

    public function download($id)
    {
        $file = ExportFile::findOrFail($id);
        $path = 'exports/' . $file->filename;

        if (!Storage::exists($path)) {
            abort(404, 'Dosya bulunamadı.');
        }

        return Storage::download($path);
    }

    public function syncStorage()
    {
        $storageFiles = Storage::files('exports');

        foreach ($storageFiles as $path) {
            $filename = basename($path);

            if (!ExportFile::where('filename', $filename)->exists()) {
                ExportFile::create([
                    'filename' => $filename,
                    'filetype' => pathinfo($filename, PATHINFO_EXTENSION),
                    'filesize' => Storage::size($path),
                    'status' => 'completed',
                    'exported_at' => Carbon::createFromTimestamp(Storage::lastModified($path)),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Storage senkronize edildi.');
    }
public function cleanupDeletedFiles()
{
    $files = ExportFile::where('status', 'completed')->get();

    foreach ($files as $file) {
        $path = 'exports/' . $file->filename;

        if (!Storage::exists($path)) {
            $file->update([
                'status' => 'deleted',
            ]);
        }
    }

    return redirect()->back()->with('success', 'Silinmiş dosyalar loglandı.');
}
public function retry($id)
{
    $file = ExportFile::findOrFail($id);

    if ($file->status !== 'failed') {
        return redirect()->back()->with('error', 'Sadece hatalı dosyalar tekrar denenebilir.');
    }

    // Retry işlemi burada tetiklenmeli (queue, job, vs.)
    dispatch(new RetryExportJob($file));

    return redirect()->back()->with('success', 'Export işlemi tekrar başlatıldı.');
}

}