<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CampaignAIOptimizer;
use Illuminate\Support\Facades\DB;

class CampaignAIController extends Controller
{
    protected CampaignAIOptimizer $optimizer;

    public function __construct(CampaignAIOptimizer $optimizer)
    {
        $this->optimizer = $optimizer;
    }

    /**
     * Kampanya AI analizi
     */
    public function analyzeCampaign(int $campaignId)
    {
        $score = $this->optimizer->calculateCampaignScore($campaignId);
        $recommendations = $this->optimizer->generateRecommendations($campaignId);
        $segments = $this->optimizer->suggestSegments($campaignId);

        return response()->json([
            'campaign_id' => $campaignId,
            'ai_score' => $score,
            'score_grade' => $this->getScoreGrade($score),
            'recommendations' => $recommendations,
            'suggested_segments' => $segments,
            'analyzed_at' => now()->toISOString(),
        ]);
    }

    /**
     * Toplu kampanya karşılaştırması
     */
    public function compareCampaigns(Request $request)
    {
        $campaignIds = $request->input('campaign_ids', []);
        
        if (empty($campaignIds)) {
            $campaignIds = DB::table('campaigns')
                ->where('status', 'active')
                ->pluck('id')
                ->toArray();
        }

        $comparisons = collect($campaignIds)->map(function ($id) {
            return [
                'campaign_id' => $id,
                'score' => $this->optimizer->calculateCampaignScore($id),
                'grade' => $this->getScoreGrade($this->optimizer->calculateCampaignScore($id)),
                'recommendation_count' => count($this->optimizer->generateRecommendations($id)),
            ];
        })->sortByDesc('score')->values();

        return response()->json([
            'total_campaigns' => count($campaignIds),
            'average_score' => $comparisons->avg('score'),
            'campaigns' => $comparisons,
        ]);
    }

    /**
     * AI önerilerini uygula
     */
    public function applyRecommendation(Request $request, int $campaignId)
    {
        $validated = $request->validate([
            'recommendation_type' => 'required|string|in:conversion,engagement,timing,budget',
            'action' => 'required|string',
        ]);

        // Öneriyi uygula (örnek)
        $applied = $this->applyAction($campaignId, $validated['recommendation_type'], $validated['action']);

        return response()->json([
            'success' => $applied,
            'message' => $applied ? 'Öneri başarıyla uygulandı' : 'Öneri uygulanamadı',
            'campaign_id' => $campaignId,
        ]);
    }

    /**
     * Performans tahmini
     */
    public function predictPerformance(int $campaignId, Request $request)
    {
        $days = $request->input('forecast_days', 7);
        
        $historical = DB::table('campaign_performance_log')
            ->where('campaign_id', $campaignId)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        if ($historical->isEmpty()) {
            return response()->json([
                'error' => 'Yeterli geçmiş veri yok',
            ], 400);
        }

        // Basit trend analizi
        $avgGrowth = $this->calculateGrowthRate($historical);
        $currentMetrics = $historical->first();

        $predictions = collect(range(1, $days))->map(function ($day) use ($currentMetrics, $avgGrowth) {
            return [
                'day' => $day,
                'date' => now()->addDays($day)->format('Y-m-d'),
                'predicted_conversion_rate' => round($currentMetrics->conversion_rate * (1 + $avgGrowth) ** $day, 2),
                'predicted_revenue' => round($currentMetrics->revenue * (1 + $avgGrowth) ** $day, 2),
                'confidence' => max(100 - ($day * 5), 50), // Confidence azalır
            ];
        });

        return response()->json([
            'campaign_id' => $campaignId,
            'forecast_days' => $days,
            'growth_rate' => round($avgGrowth * 100, 2) . '%',
            'predictions' => $predictions,
        ]);
    }

    /**
     * Score grade hesapla
     */
    private function getScoreGrade(float $score): string
    {
        if ($score >= 90) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        return 'F';
    }

    /**
     * Öneri uygula
     */
    private function applyAction(int $campaignId, string $type, string $action): bool
    {
        // Gerçek uygulamada kampanya ayarlarını güncelle
        // Örnek: indirim artır, segment ekle, zamanlama değiştir
        
        DB::table('campaign_ai_actions')->insert([
            'campaign_id' => $campaignId,
            'action_type' => $type,
            'action_detail' => $action,
            'applied_at' => now(),
        ]);

        return true;
    }

    /**
     * Büyüme oranı hesapla
     */
    private function calculateGrowthRate($historical): float
    {
        if ($historical->count() < 2) return 0;

        $first = $historical->last();
        $last = $historical->first();

        if ($first->conversion_rate == 0) return 0;

        return ($last->conversion_rate - $first->conversion_rate) / $first->conversion_rate / $historical->count();
    }
}
