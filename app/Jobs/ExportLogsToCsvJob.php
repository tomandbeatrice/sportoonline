namespace App\Jobs;

use App\Models\ExportLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportLogsToCsvJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $filename;

    public function __construct(array $filters, string $filename)
    {
        $this->filters = $filters;
        $this->filename = $filename;
    }

    public function handle()
    {
        $query = ExportLog::query();

        if (!empty($this->filters['user'])) {
            $query->where('user_email', 'like', '%' . $this->filters['user'] . '%');
        }

        if (!empty($this->filters['vendor'])) {
            $query->where('vendor_name', 'like', '%' . $this->filters['vendor'] . '%');
        }

        if (!empty($this->filters['date'])) {
            $query->whereDate('created_at', $this->filters['date']);
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

        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        Storage::put("exports/{$this->filename}", $content);
    }
}