namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Seller;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SellerController;
use App\Notifications\CampaignSuggestionNotification;
use App\Services\CampaignSuggestionService;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\ExportLiveActivate::class,
        \App\Console\Commands\CleanOldExports::class,
        \App\Console\Commands\CleanupExports::class,
        \App\Console\Commands\ExportsCleanup::class,
        \App\Console\Commands\ShowCleanupLog::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // 🧹 Export görevleri
        $schedule->command('exports:clean')->dailyAt('03:00');
        $schedule->command('exports:cleanup')->weeklyOn(1, '04:00');

        // 📤 Satıcılara kampanya önerisi bildirimi (Pazartesi 08:00)
        $schedule->call(function () {
            $sellers = Seller::all();

            foreach ($sellers as $seller) {
                $suggestions = app(SellerController::class)
                    ->campaignSuggestionList($seller->id)
                    ->getData();

                $seller->notify(new CampaignSuggestionNotification($suggestions));
            }
        })->weekly()->mondays()->at('08:00')
          ->name('send-campaign-suggestions')
          ->withoutOverlapping();

        // 🔁 Otomatik kampanya planlayıcı (Pazar 23:00)
        $schedule->call(function () {
            app(CampaignSuggestionService::class)->autoPlanNextWeek();
        })->weeklyOn(7, '23:00')
          ->name('auto-plan-next-week')
          ->withoutOverlapping();

        // 🧠 Dinamik ağırlık güncelleme (Pazartesi 03:00)
        $schedule->call([AdminController::class, 'updateSuggestionWeights'])
            ->weeklyOn(1, '03:00')
            ->name('update-suggestion-weights')
            ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}