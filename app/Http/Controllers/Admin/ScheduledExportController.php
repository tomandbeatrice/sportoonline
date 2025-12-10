<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScheduledExportController extends Controller
{
    protected string $configPath;

    public function __construct()
    {
        $this->configPath = config_path('scheduled_exports.php');
    }

    public function list()
    {
        $plans = file_exists($this->configPath) ? require $this->configPath : [];

        return response()->json($plans);
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'segmentId' => 'required|integer',
            'day' => 'required|string',
            'time' => 'required|string'
        ]);

        $plans = file_exists($this->configPath) ? require $this->configPath : [];

        $filtered = array_filter($plans, fn($plan) =>
            !(
                $plan['segmentId'] == $validated['segmentId'] &&
                $plan['day'] == $validated['day'] &&
                $plan['time'] == $validated['time']
            )
        );

        $this->writeConfig(array_values($filtered));

        return response()->json(['message' => 'Plan silindi']);
    }

    protected function writeConfig(array $data): void
    {
        $export = var_export($data, true);
        $content = "<?php\n\nreturn {$export};\n";

        file_put_contents($this->configPath, $content);
    }
}