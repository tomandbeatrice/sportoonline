<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TurboCompetitionService;
use Illuminate\Http\Request;

class TurboCompetitionController extends Controller
{
    protected $turboService;

    public function __construct(TurboCompetitionService $turboService)
    {
        $this->turboService = $turboService;
    }

    /**
     * Get current competition stats and leaderboards
     */
    public function currentCompetition()
    {
        try {
            $stats = $this->turboService->getStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Turbo Mod verileri alınamadı.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer leaderboard
     */
    public function customerLeaderboard(Request $request)
    {
        try {
            $limit = $request->get('limit', 5);
            $leaderboard = $this->turboService->getCustomerLeaderboard($limit);

            return response()->json([
                'success' => true,
                'data' => $leaderboard
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Müşteri sıralaması alınamadı.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get seller leaderboard
     */
    public function sellerLeaderboard(Request $request)
    {
        try {
            $limit = $request->get('limit', 5);
            $leaderboard = $this->turboService->getSellerLeaderboard($limit);

            return response()->json([
                'success' => true,
                'data' => $leaderboard
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Satıcı sıralaması alınamadı.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user's position
     */
    public function myPosition(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $position = $this->turboService->getUserPosition($userId);

            if (!$position) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sıralama bulunamadı. Henüz yarışmaya katılmadınız.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $position
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pozisyon bilgisi alınamadı.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get competition history
     */
    public function history(Request $request)
    {
        try {
            $limit = $request->get('limit', 12);
            $history = $this->turboService->getCompetitionHistory($limit);

            return response()->json([
                'success' => true,
                'data' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Geçmiş veriler alınamadı.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's winning history
     */
    public function myWinnings(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $limit = $request->get('limit', 10);
            
            $winnings = $this->turboService->getUserWinningHistory($userId, $limit);

            return response()->json([
                'success' => true,
                'data' => $winnings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kazanç geçmişi alınamadı.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
