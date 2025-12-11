<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TurboWinner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TurboWinnerController extends Controller
{
    /**
     * Get turbo winners for a specific month
     */
    public function index(Request $request): JsonResponse
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $winners = TurboWinner::where('year', $year)
            ->where('month', $month)
            ->with('user:id,name,email')
            ->orderBy('rank')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $winners
        ]);
    }

    /**
     * Get statistics for all competitions
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_competitions' => TurboWinner::distinct(['year', 'month'])->count(),
            'total_rewards_distributed' => TurboWinner::sum('reward_money'),
            'total_points_distributed' => TurboWinner::sum('reward_points'),
            'active_coupons' => TurboWinner::whereNotNull('coupon_id')
                ->whereHas('coupon', function($q) {
                    $q->where('expires_at', '>', now())
                      ->where('is_active', true);
                })
                ->count()
        ];

        return response()->json($stats);
    }

    /**
     * Update winner rewards
     */
    public function update(Request $request, $id): JsonResponse
    {
        $winner = TurboWinner::findOrFail($id);

        $validated = $request->validate([
            'reward_money' => 'nullable|numeric|min:0',
            'reward_points' => 'nullable|integer|min:0',
        ]);

        $winner->update([
            'reward_money' => $validated['reward_money'] ?? $winner->reward_money,
            'reward_points' => $validated['reward_points'] ?? $winner->reward_points,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ödül güncellendi',
            'data' => $winner->fresh()
        ]);
    }

    /**
     * Bulk update rewards for multiple winners
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'updates' => 'required|array',
            'updates.*.id' => 'required|exists:turbo_winners,id',
            'updates.*.reward_money' => 'nullable|numeric|min:0',
            'updates.*.reward_points' => 'nullable|integer|min:0',
        ]);

        $updated = 0;
        foreach ($validated['updates'] as $update) {
            TurboWinner::where('id', $update['id'])->update([
                'reward_money' => $update['reward_money'] ?? 0,
                'reward_points' => $update['reward_points'] ?? 0,
            ]);
            $updated++;
        }

        return response()->json([
            'success' => true,
            'message' => "$updated ödül güncellendi"
        ]);
    }
}
