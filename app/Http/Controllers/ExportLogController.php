use App\Models\ExportLog;
use Illuminate\Http\Request;

class ExportLogController extends Controller
{
    public function index(Request $request)
    {
        return ExportLog::query()
            ->when($request->segment, fn($q) => $q->where('segment', $request->segment))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('exported_at')
            ->paginate(20);
    }

    public function export(Request $request)
    {
        $logs = ExportLog::query()
            ->when($request->segment, fn($q) => $q->where('segment', $request->segment))
            ->get();

        // CSV oluşturma mantığı (örnek)
        $csv = "Segment,Type,Status,Duration,File Name,File Size,Exported At\n";
        foreach ($logs as $log) {
            $csv .= "{$log->segment},{$log->export_type},{$log->status},{$log->duration},{$log->file_name},{$log->file_size},{$log->exported_at}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="export_logs.csv"');
    }
}