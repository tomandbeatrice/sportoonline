<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class CampaignAIOptimizer
{
    /**
     * Kampanya başarı skorunu hesapla (0-100)
     */
    public function calculateCampaignScore(int $campaignId): float
    {
        $campaign = DB::table('campaigns')->find($campaignId);
        if (!$campaign) return 0;

        $metrics = $this->getCampaignMetrics($campaignId);
        
        // Ağırlıklı skorlama
        $weights = [
            'conversion_rate' => 0.35,
            'revenue_impact' => 0.30,
            'engagement_rate' => 0.20,
            'roi' => 0.15,
        ];

        $score = 0;
        $score += ($metrics['conversion_rate'] ?? 0) * $weights['conversion_rate'];
        $score += min(($metrics['revenue_impact'] ?? 0) / 10000, 100) * $weights['revenue_impact'];
        $score += ($metrics['engagement_rate'] ?? 0) * $weights['engagement_rate'];
        $score += min(($metrics['roi'] ?? 0) * 10, 100) * $weights['roi'];

        return round($score, 2);
    }

    /**
     * Kampanya için AI önerileri üret
     */
    public function generateRecommendations(int $campaignId): array
    {
        $score = $this->calculateCampaignScore($campaignId);
        $metrics = $this->getCampaignMetrics($campaignId);
        $historicalData = $this->getHistoricalPerformance($campaignId);

        $recommendations = [];

        // Düşük dönüşüm oranı
        if ($metrics['conversion_rate'] < 2.0) {
            $recommendations[] = [
                'type' => 'conversion',
                'priority' => 'high',
                'title' => 'Dönüşüm Oranını Artır',
                'description' => 'Dönüşüm oranınız %' . $metrics['conversion_rate'] . ' seviyesinde. Hedef kitleyi daraltmayı veya indirimi artırmayı deneyin.',
                'actions' => [
                    'Segment filtreleme ekle',
                    'İndirim oranını +5% artır',
                    'Kampanya süresini uzat'
                ],
                'expected_impact' => '+1.2% dönüşüm artışı',
            ];
        }

        // Düşük engagement
        if ($metrics['engagement_rate'] < 15.0) {
            $recommendations[] = [
                'type' => 'engagement',
                'priority' => 'medium',
                'title' => 'Etkileşimi Güçlendir',
                'description' => 'Kampanya etkileşimi düşük. Görsel ve mesajlaşmayı optimize edin.',
                'actions' => [
                    'E-posta kampanyası ekle',
                    'SMS bildirimi gönder',
                    'Banner yerleşimini değiştir'
                ],
                'expected_impact' => '+8% etkileşim artışı',
            ];
        }

        // Zaman optimizasyonu
        $bestTime = $this->predictBestTime($historicalData);
        if ($bestTime) {
            $recommendations[] = [
                'type' => 'timing',
                'priority' => 'medium',
                'title' => 'Zamanlama Optimizasyonu',
                'description' => "Verilerinize göre en iyi performans {$bestTime['day']} günü {$bestTime['hour']}:00 saatinde.",
                'actions' => [
                    "Kampanyayı {$bestTime['day']} gününe programla",
                    'Otomatik zamanlama aktif et'
                ],
                'expected_impact' => '+15% erişim artışı',
            ];
        }

        // Budget optimizasyonu
        if ($metrics['roi'] < 1.5) {
            $recommendations[] = [
                'type' => 'budget',
                'priority' => 'high',
                'title' => 'Bütçe Optimizasyonu',
                'description' => 'ROI düşük. Kampanya bütçesini yeniden dağıtın.',
                'actions' => [
                    'Düşük performanslı segmentleri duraklat',
                    'Yüksek ROI kanallarına yönlendir',
                    'Teklif stratejisini gözden geçir'
                ],
                'expected_impact' => 'ROI: 1.5 → 2.3',
            ];
        }

        return $recommendations;
    }

    /**
     * Kampanya metriklerini al
     */
    private function getCampaignMetrics(int $campaignId): array
    {
        return Cache::remember("campaign.metrics.{$campaignId}", 300, function () use ($campaignId) {
            $orders = DB::table('orders')
                ->where('campaign_id', $campaignId)
                ->get();

            $views = DB::table('campaign_views')
                ->where('campaign_id', $campaignId)
                ->count();

            $clicks = DB::table('campaign_clicks')
                ->where('campaign_id', $campaignId)
                ->count();

            $revenue = $orders->sum('total_price');
            $cost = DB::table('campaigns')->where('id', $campaignId)->value('budget') ?? 0;

            return [
                'conversion_rate' => $views > 0 ? ($orders->count() / $views) * 100 : 0,
                'revenue_impact' => $revenue,
                'engagement_rate' => $views > 0 ? ($clicks / $views) * 100 : 0,
                'roi' => $cost > 0 ? $revenue / $cost : 0,
                'total_orders' => $orders->count(),
                'total_views' => $views,
                'total_clicks' => $clicks,
            ];
        });
    }

    /**
     * Geçmiş performans verilerini al
     */
    private function getHistoricalPerformance(int $campaignId): Collection
    {
        return DB::table('campaign_performance_log')
            ->where('campaign_id', $campaignId)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();
    }

    /**
     * En iyi zamanlama tahmini
     */
    private function predictBestTime(Collection $historicalData): ?array
    {
        if ($historicalData->isEmpty()) return null;

        $dayPerformance = $historicalData->groupBy(function ($item) {
            return date('l', strtotime($item->created_at));
        })->map(function ($group) {
            return $group->avg('conversion_rate');
        });

        $bestDay = $dayPerformance->sortDesc()->keys()->first();

        $hourPerformance = $historicalData
            ->filter(fn($item) => date('l', strtotime($item->created_at)) === $bestDay)
            ->groupBy(function ($item) {
                return date('H', strtotime($item->created_at));
            })->map(function ($group) {
                return $group->avg('conversion_rate');
            });

        $bestHour = $hourPerformance->sortDesc()->keys()->first();

        return [
            'day' => $bestDay,
            'hour' => $bestHour,
            'expected_performance' => $dayPerformance[$bestDay] ?? 0,
        ];
    }

    /**
     * Kampanya segment önerisi
     */
    public function suggestSegments(int $campaignId): array
    {
        $campaignType = DB::table('campaigns')->where('id', $campaignId)->value('type');
        
        $segments = [];

        // Yüksek değerli müşteriler
        $segments[] = [
            'name' => 'High Value Customers',
            'criteria' => [
                'total_spent' => ['min' => 5000],
                'order_count' => ['min' => 10],
            ],
            'estimated_size' => $this->estimateSegmentSize(['total_spent' => 5000]),
            'expected_roi' => 2.8,
        ];

        // Aktif alıcılar
        $segments[] = [
            'name' => 'Active Buyers (30 days)',
            'criteria' => [
                'last_order_days' => ['max' => 30],
            ],
            'estimated_size' => $this->estimateSegmentSize(['last_order_days' => 30]),
            'expected_roi' => 2.1,
        ];

        // Kayıp riskinde
        $segments[] = [
            'name' => 'At Risk (Churn Prevention)',
            'criteria' => [
                'last_order_days' => ['min' => 60, 'max' => 120],
                'order_count' => ['min' => 3],
            ],
            'estimated_size' => $this->estimateSegmentSize(['last_order_days' => [60, 120]]),
            'expected_roi' => 1.9,
        ];

        return $segments;
    }

    /**
     * Segment boyutu tahmini
     */
    private function estimateSegmentSize(array $criteria): int
    {
        // Basit tahmin - gerçek uygulamada DB query kullan
        return rand(500, 5000);
    }
}
