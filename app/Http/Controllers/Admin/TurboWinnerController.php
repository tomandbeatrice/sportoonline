<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TurboWinner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TurboWinnerController extends Controller
{
    /**
     * Display a listing of the turbo winners
     */
    public function index(Request $request): JsonResponse
    {
        $month = $request->input('month', now()->format('Y-m'));
        
        $query = TurboWinner::with(['user', 'competition'])
            ->whereHas('competition', function($q) use ($month) {
                $q->whereRaw('DATE_FORMAT(month, "%Y-%m") = ?', [$month]);
            })
            ->orderBy('category')
            ->orderBy('rank');
        
        $winners = $query->get()->groupBy('category');
        
        return response()->json([
            'customers' => $winners->get('customer', collect()),
            'sellers' => $winners->get('seller', collect())
        ]);
    }
    
    /**
     * Display a specific winner
     */
    public function show(TurboWinner $turboWinner): JsonResponse
    {
        $turboWinner->load(['user', 'competition', 'coupon']);
        
        return response()->json($turboWinner);
    }
    
    /**
     * Update winner rewards
     */
    public function update(Request $request, TurboWinner $turboWinner): JsonResponse
    {
        $validated = $request->validate([
            'reward_money' => 'nullable|numeric|min:0',
            'reward_points' => 'nullable|integer|min:0'
        ]);
        
        $turboWinner->update($validated);
        
        return response()->json([
            'message' => 'Ödüller güncellendi',
            'winner' => $turboWinner->fresh()
        ]);
    }
    
    /**
     * Bulk update winners
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'winners' => 'required|array',
            'winners.*.id' => 'required|exists:turbo_winners,id',
            'winners.*.reward_money' => 'nullable|numeric|min:0',
            'winners.*.reward_points' => 'nullable|integer|min:0'
        ]);
        
        $updated = 0;
        foreach ($validated['winners'] as $winnerData) {
            $winner = TurboWinner::find($winnerData['id']);
            if ($winner) {
                $winner->update([
                    'reward_money' => $winnerData['reward_money'] ?? $winner->reward_money,
                    'reward_points' => $winnerData['reward_points'] ?? $winner->reward_points
                ]);
                $updated++;
            }
        }
        
        return response()->json([
            'message' => "{$updated} kazanan güncellendi",
            'updated_count' => $updated
        ]);
    }
    
    /**
     * Get winners by month
     */
    public function getByMonth(Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);
        
        $month = $request->input('month');
        
        $winners = TurboWinner::with(['user', 'competition'])
            ->whereHas('competition', function($q) use ($month) {
                $q->whereRaw('DATE_FORMAT(month, "%Y-%m") = ?', [$month]);
            })
            ->orderBy('category')
            ->orderBy('rank')
            ->get()
            ->groupBy('category');
        
        return response()->json([
            'month' => $month,
            'customers' => $winners->get('customer', collect())->map(function($winner) {
                return [
                    'id' => $winner->id,
                    'rank' => $winner->rank,
                    'rank_badge' => $winner->rank_badge,
                    'user' => $winner->user,
                    'total_amount' => $winner->total_amount,
                    'reward_money' => $winner->reward_money,
                    'reward_points' => $winner->reward_points
                ];
            }),
            'sellers' => $winners->get('seller', collect())->map(function($winner) {
                return [
                    'id' => $winner->id,
                    'rank' => $winner->rank,
                    'rank_badge' => $winner->rank_badge,
                    'user' => $winner->user,
                    'total_amount' => $winner->total_amount,
                    'reward_money' => $winner->reward_money,
                    'reward_points' => $winner->reward_points,
                    'coupon_code' => $winner->coupon_code
                ];
            })
        ]);
    }
}
