<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Seller;

class IncentiveCampaignController extends Controller
{
    /**
     * Kampanya tiplerine göre analytics
     */
    public function campaignTypeAnalytics()
    {
        $analytics = DB::table('campaigns')
            ->select(
                'campaign_type as type',
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(conversion_rate) as avg_conversion'),
                DB::raw('SUM(total_revenue) as total_revenue'),
                DB::raw('AVG(roi) as avg_roi')
            )
            ->groupBy('campaign_type')
            ->get();

        return response()->json($analytics);
    }

    /**
     * Campaign score dashboard
     */
    public function campaignScoreDashboard()
    {
        $scores = DB::table('campaign_scores')
            ->select(
                'campaign_id',
                'campaign_name',
                'score',
                'performance_index',
                'created_at'
            )
            ->orderBy('score', 'desc')
            ->limit(20)
            ->get();

        return response()->json($scores);
    }

    /**
     * Export campaign suggestions history
     */
    public function getExportHistory()
    {
        $history = Cache::get('campaign_export_history', []);
        return response()->json($history);
    }

    /**
     * Update suggestion rules
     */
    public function updateSuggestionRules(Request $request)
    {
        $validated = $request->validate([
            'discount_threshold' => 'required|numeric|min:0|max:100',
            'product_count_threshold' => 'required|integer|min:1',
            'campaign_types' => 'required|array|min:2',
            'campaign_types.*' => 'string'
        ]);

        Cache::put('suggestion_rules', $validated);

        return response()->json(['status' => 'rules updated']);
    }
}