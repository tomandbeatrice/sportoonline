<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Kayıtlı artisan komutları
     */
    protected $commands = [
        \App\Console\Commands\SystemScan::class,
        \App\Console\Commands\ExportCleanup::class,
        \App\Console\Commands\SegmentExport::class,
        \App\Console\Commands\SprintVerify::class,
        \App\Console\Commands\SendCampaignExpiringNotifications::class,
        \App\Console\Commands\CalculateMonthlyCommissions::class,
        \App\Console\Commands\FinalizeTurboCompetition::class,
    ];

    /**
     * Zamanlanmış görevler
     */
    protected function schedule(Schedule $schedule): void
    {
        // ExportCleanup → her gece 02:00
        $schedule->command('export:cleanup')->dailyAt('02:00');

        // Campaign expiring notifications → her gün 09:00
        $schedule->command('campaigns:notify-expiring')->dailyAt('09:00');
        
        // Monthly commission calculation → her ayın 1'inde 00:00
        $schedule->command('commissions:calculate')->monthlyOn(1, '00:00');
        
        // Turbo competition finalization → her ayın 1'inde 01:00 (komisyon hesabından sonra)
        $schedule->command('turbo:finalize')->monthlyOn(1, '01:00');

        // SegmentExport → dinamik planlama
        if (file_exists(config_path('scheduled_exports.php'))) {
            $plans = include config_path('scheduled_exports.php');

            foreach ($plans as $plan) {
                $command = "segment:export {$plan['segmentId']}";

                match ($plan['day']) {
                    'daily'  => $schedule->command($command)->dailyAt($plan['time']),
                    'monday' => $schedule->command($command)->weeklyOn(1, $plan['time']),
                    'friday' => $schedule->command($command)->weeklyOn(5, $plan['time']),
                    default  => null
                };
            }
        }

        // AI strateji güncellemesi → her gece 03:00
        $schedule->call(function () {
            app(\App\Http\Controllers\SellerController::class)
                ->refreshCampaignStrategy(app(\App\Services\CampaignService::class));
        })->dailyAt('03:00');

        // SprintVerify → her saat başı sistem refleksi kontrolü
        $schedule->command('sprint:verify')->hourly();
    }

    /**
     * Komutları yükle
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}