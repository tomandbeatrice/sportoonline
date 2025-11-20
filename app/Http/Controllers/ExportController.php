<?php

namespace App\Http\Controllers;

use App\Models\ExportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class ExportController extends Controller
{
    protected string $scheduleConfigPath;

    public function __construct()
    {
        $this->scheduleConfigPath = base_path('scheduled_exports.php');
    }

    /**
     * Planlanmış export loglarını getirir.
     */
    public function getScheduledExports()
    {
        $plans = File::exists($this->scheduleConfigPath)
            ? require $this->scheduleConfigPath
            : [];

        $latestRuns = ExportLog::select('segment', 'exported_at')
            ->orderByDesc('exported_at')
            ->get()
            ->keyBy('segment');

        $response = collect($plans)->map(function (array $plan) use ($latestRuns) {
            $segmentId = $plan['segmentId'] ?? null;
            $segmentLabel = $this->segmentLabel($segmentId);
            $day = strtolower($plan['day'] ?? 'daily');
            $time = $plan['time'] ?? '00:00';

            $nextRun = $this->calculateNextRun($day, $time);
            $lastRun = optional($latestRuns->get($segmentLabel))->exported_at;

            return [
                'id' => sprintf('%s-%s-%s', $segmentId ?? 'segment', $day, $time),
                'segment_id' => $segmentId,
                'segment' => $segmentLabel,
                'schedule' => $day,
                'time' => $time,
                'frequency' => $this->frequencyLabel($day),
                'window' => $this->windowLabel($day, $time),
                'next_run' => $nextRun?->toDateTimeString(),
                'last_run' => optional($lastRun)->toDateTimeString(),
            ];
        });

        return response()->json($response);
    }

    protected function segmentLabel($segmentId): string
    {
        $map = [
            1 => 'Genel Segment',
            2 => 'Top 100',
            3 => 'Sadakat Segmenti',
            4 => 'Risk Operasyon',
            5 => 'Enterprise',
        ];

        return $map[$segmentId] ?? 'Segment ' . ($segmentId ?? 'Tanımsız');
    }

    protected function frequencyLabel(string $day): string
    {
        return match ($day) {
            'daily' => 'Günlük',
            'weekly' => 'Haftalık',
            'monthly' => 'Aylık',
            'monday' => 'Pazartesi',
            'tuesday' => 'Salı',
            'wednesday' => 'Çarşamba',
            'thursday' => 'Perşembe',
            'friday' => 'Cuma',
            'saturday' => 'Cumartesi',
            'sunday' => 'Pazar',
            default => ucfirst($day)
        };
    }

    protected function windowLabel(string $day, string $time): string
    {
        return $this->frequencyLabel($day) . ' · ' . $time;
    }

    protected function calculateNextRun(string $day, string $time): ?Carbon
    {
        $now = Carbon::now();
        $scheduled = $now->copy()->setTimeFromTimeString($time);

        if ($day === 'daily') {
            if ($scheduled->lessThanOrEqualTo($now)) {
                $scheduled->addDay();
            }
            return $scheduled;
        }

        $dayMap = [
            'monday' => Carbon::MONDAY,
            'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY,
            'saturday' => Carbon::SATURDAY,
            'sunday' => Carbon::SUNDAY,
        ];

        if (isset($dayMap[$day])) {
            $scheduled->next($dayMap[$day]);
            return $scheduled;
        }

        if ($day === 'weekly') {
            $scheduled->addWeek();
            return $scheduled;
        }

        if ($day === 'monthly') {
            $scheduled->addMonth();
            return $scheduled;
        }

        return null;
    }
}