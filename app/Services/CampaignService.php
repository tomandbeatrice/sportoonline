<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class CampaignService
{
    protected array $segmentWeights = [
        'Premium' => 1.0,
        'Standard' => 1.0,
        'Low' => 1.0
    ];

    public function calculateScore(array $inputs): float
    {
        $coefficients = Config::get('campaign.regression_coefficients', [
            'response_time' => -0.25,
            'comment_response_rate' => 0.6,
            'update_count' => 0.3,
            'export_usage' => 0.15
        ]);

        return round(array_sum(array_map(
            fn($k) => $inputs[$k] * $coefficients[$k],
            array_keys($inputs)
        )), 2);
    }

    public function suggestSegmentedCampaign(array $inputs): array
    {
        $score = $this->calculateScore($inputs);

        $segment = match (true) {
            $score >= 15 => 'Premium',
            $score >= 10 => 'Standard',
            default => 'Low'
        };

        $weight = $this->segmentWeights[$segment] ?? 1.0;

        $type = match ($segment) {
            'Premium' => 'Outlet Boost',
            'Standard' => 'Flash Sale',
            'Low' => 'Organic Push'
        };

        $discount = match ($segment) {
            'Premium' => 30,
            'Standard' => 20,
            'Low' => 10
        };

        $duration = $inputs['response_time'] < 20 ? 7 : 3;

        return [
            'segment' => $segment,
            'campaign_type' => $type,
            'suggested_discount' => round($discount * $weight),
            'duration' => $duration,
            'score' => $score
        ];
    }

    public function suggestSegmentedCampaignWithConfidence(array $inputs): array
    {
        $suggestion = $this->suggestSegmentedCampaign($inputs);

        $confidence = round(
            ($suggestion['score'] / 20) * 0.5 +
            ($inputs['comment_response_rate'] / 100) * 0.3 +
            ($inputs['update_count'] > 5 ? 0.2 : 0),
            2
        );

        return array_merge($suggestion, [
            'confidence_score' => min($confidence * 100, 100)
        ]);
    }

    public function learnFromSegmentHistory(array $history): array
    {
        $segmentScores = [];

        foreach ($history as $entry) {
            $segment = $entry['segment'];
            $scoreChange = $entry['post_score'] - $entry['pre_score'];

            if (!isset($segmentScores[$segment])) {
                $segmentScores[$segment] = ['total' => 0, 'count' => 0];
            }

            $segmentScores[$segment]['total'] += $scoreChange;
            $segmentScores[$segment]['count'] += 1;
        }

        return collect($segmentScores)->map(fn($data) => round($data['total'] / $data['count'], 2))->toArray();
    }

    public function updateStrategyFromLearning(array $learnedScores): void
    {
        foreach ($learnedScores as $segment => $avgScoreChange) {
            $this->segmentWeights[$segment] = 1 + ($avgScoreChange / 10);
        }
    }
}