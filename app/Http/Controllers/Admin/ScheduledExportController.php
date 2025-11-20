<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScheduledExportController extends Controller
{
    protected string $configPath;

    public function __construct()
    {
        $this->configPath = config_path('scheduled_exports.json');
    }

    public function list()
    {
        $plans = file_exists($this->configPath)
            ? json_decode(file_get_contents($this->configPath), true)
            : [];

        return response()->json($plans);
    }

    public function delete($segmentId, $day, $time)
    {
        $plans = file_exists($this->configPath)
            ? json_decode(file_get_contents($this->configPath), true)
            : [];

        $filtered = array_filter($plans, fn($plan) =>
            !(
                $plan['segmentId'] == $segmentId &&
                $plan['day'] == $day &&
                $plan['time'] == $time
            )
        );

        $this->writeConfig(array_values($filtered));

        return response()->json(['message' => 'Plan silindi']);
    }

    protected function writeConfig(array $data): void
    {
        file_put_contents($this->configPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}