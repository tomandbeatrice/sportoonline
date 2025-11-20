namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\BulkExportJob;

class ExportBatchController extends Controller
{
    public function triggerBatch(Request $request)
    {
        $request->validate([
            'vendor_ids' => 'required|array',
            'vendor_ids.*' => 'integer|exists:vendors,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        BulkExportJob::dispatch(
            $request->vendor_ids,
            [
                'date_from' => $request->date_from,
                'date_to'   => $request->date_to
            ],
            auth()->id(),
            auth()->user()->email,
            $request->ip()
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Toplu export işlemi queue’ya alındı.'
        ]);
    }

    public function downloadZip()
    {
        $zipPath = storage_path('app/exports/bulk_export.zip');

        if (!file_exists($zipPath)) {
            return response()->json([
                'status' => 'error',
                'message' => 'ZIP dosyası henüz hazır değil.'
            ], 404);
        }

        return response()->download($zipPath);
    }
}