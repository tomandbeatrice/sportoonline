<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SegmentExportSchedulerController extends Controller
{
    /**
     * Segment export zamanlaması oluştur
     */
    public function schedule(Request $request)
    {
        $validated = $request->validate([
            'segment_id' => 'required|string',
            'day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'time' => 'required|string|regex:/^\d{2}:\d{2}$/',
            'format' => 'nullable|string|in:xlsx,csv',
        ]);

        $configPath = config_path('scheduled_exports.php');
        $schedules = file_exists($configPath) ? require $configPath : [];

        // Yeni zamanlama ekle
        $key = "{$validated['segment_id']}_{$validated['day']}_{$validated['time']}";
        $schedules[$key] = [
            'segment_id' => $validated['segment_id'],
            'day' => $validated['day'],
            'time' => $validated['time'],
            'format' => $validated['format'] ?? 'xlsx',
            'created_at' => now()->toDateTimeString(),
        ];

        // Config dosyasına kaydet
        $content = "<?php\n\nreturn " . var_export($schedules, true) . ";\n";
        file_put_contents($configPath, $content);

        return response()->json([
            'success' => true,
            'message' => 'Export zamanlaması oluşturuldu',
            'schedule' => $schedules[$key],
        ]);
    }
}