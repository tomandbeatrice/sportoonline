namespace App\Services;

use App\Models\PlannedCampaign;
use App\Models\AutoPlanLog;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Http\Controllers\AdminController;

class CampaignSuggestionService
{
    /**
     * Haftalık düşük yoğunluklu günlere otomatik kampanya planlar
     * ve log kaydı oluşturur.
     */
    public function autoPlanNextWeek(): void
    {
        $density = $this->getNextWeekDensity();

        $lowDays = collect($density)
            ->filter(fn($count) => $count < 2);

        foreach ($lowDays as $date => $count) {
            PlannedCampaign::updateOrCreate(
                ['code' => 'auto_' . Str::replace('-', '', $date)],
                [
                    'title' => 'Otomatik Trend Booster',
                    'discount_rate' => 10,
                    'commission_drop' => 2,
                    'start_date' => $date,
                    'end_date' => $date,
                ]
            );
        }

        AutoPlanLog::create([
            'run_at' => now(),
            'planned_count' => $lowDays->count(),
            'filled_dates' => $lowDays->keys()->toArray()
        ]);
    }

    /**
     * Son 6 haftalık otomatik planlama loglarını döner.
     */
    public function autoPlanTrend(): Collection
    {
        return AutoPlanLog::orderByDesc('run_at')
            ->take(6)
            ->get(['run_at', 'planned_count', 'filled_dates']);
    }

    /**
     * Etki trendine göre en güçlü kampanya türünü seçip
     * düşük yoğunluklu günlere öneri kampanyası planlar.
     */
    public function planByImpactTrend(): void
    {
        $trend = app(AdminController::class)->campaignImpactTrend()->getData(true);
        $latestWeek = now()->subWeek()->startOfWeek()->format('Y-m-d');

        $latestScores = collect($trend)->map(function ($group) use ($latestWeek) {
            return collect($group)->firstWhere('week', $latestWeek);
        })->filter();

        $top = $latestScores->sortByDesc('impact_score')->first();
        $selectedTag = $top['tag'];

        $density = $this->getNextWeekDensity();
        $lowDays = collect($density)->filter(fn($count) => $count < 2);

        foreach ($lowDays as $date => $count) {
            PlannedCampaign::updateOrCreate(
                ['code' => 'auto_' . Str::slug($selectedTag) . '_' . str_replace('-', '', $date)],
                [
                    'title' => "Önerilen: {$selectedTag} Booster",
                    'discount_rate' => 12,
                    'commission_drop' => 3,
                    'start_date' => $date,
                    'end_date' => $date,
                    'tag' => $selectedTag
                ]
            );
        }
    }

    /**
     * Mock yoğunluk verisi – gerçek versiyonda sistemden alınır.
     */
    public function getNextWeekDensity(): array
    {
        return [
            '2025-08-26' => 1,
            '2025-08-27' => 3,
            '2025-08-28' => 0,
            '2025-08-29' => 2
        ];
    }
}