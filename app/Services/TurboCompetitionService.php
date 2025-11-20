<?php

namespace App\Services;

use App\Models\TurboCompetition;
use App\Models\TurboWinner;
use App\Models\TurboCoupon;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TurboCompetitionService
{
    /**
     * Default reward structure
     */
    private const CUSTOMER_REWARDS = [
        1 => ['money' => 1000, 'points' => 5000],
        2 => ['money' => 500, 'points' => 3000],
        3 => ['money' => 250, 'points' => 2000]
    ];

    private const SELLER_REWARDS = [
        1 => ['money' => 2000, 'points' => 10000, 'commission_discount' => 10],
        2 => ['money' => 1000, 'points' => 6000, 'commission_discount' => 7],
        3 => ['money' => 500, 'points' => 4000, 'commission_discount' => 5]
    ];

    /**
     * Get or create current competition
     */
    public function getCurrentCompetition()
    {
        $competition = TurboCompetition::current();

        if (!$competition) {
            $competition = TurboCompetition::createForMonth(
                now()->year,
                now()->month
            );
        }

        return $competition;
    }

    /**
     * Get customer leaderboard
     */
    public function getCustomerLeaderboard($limit = 5)
    {
        $competition = $this->getCurrentCompetition();
        return $competition->getTopCustomers($limit);
    }

    /**
     * Get seller leaderboard
     */
    public function getSellerLeaderboard($limit = 5)
    {
        $competition = $this->getCurrentCompetition();
        return $competition->getTopSellers($limit);
    }

    /**
     * Get user's current position
     */
    public function getUserPosition($userId)
    {
        $competition = $this->getCurrentCompetition();
        $user = User::find($userId);

        if (!$user) {
            return null;
        }

        $category = $user->role === 'buyer' ? 'customer' : 'seller';
        
        return $competition->getUserPosition($userId, $category);
    }

    /**
     * Finalize current competition and distribute rewards
     */
    public function finalizeCompetition($competitionId = null)
    {
        $competition = $competitionId 
            ? TurboCompetition::find($competitionId)
            : TurboCompetition::where('status', 'ended')->first();

        if (!$competition || $competition->status === 'finalized') {
            return false;
        }

        DB::beginTransaction();

        try {
            // Finalize and create winner records
            $competition->finalize();

            // Distribute rewards
            $this->distributeRewards($competition);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Distribute rewards to winners
     */
    private function distributeRewards(TurboCompetition $competition)
    {
        // Customer rewards
        foreach ($competition->customerWinners as $winner) {
            $rewards = self::CUSTOMER_REWARDS[$winner->rank] ?? null;
            
            if ($rewards) {
                // Update winner record
                $winner->update([
                    'reward_money' => $rewards['money'],
                    'reward_points' => $rewards['points']
                ]);

                // Add money to wallet
                $winner->user->increment('wallet_balance', $rewards['money']);

                // Add turbo points
                $winner->user->increment('turbo_points', $rewards['points']);
            }
        }

        // Seller rewards
        foreach ($competition->sellerWinners as $winner) {
            $rewards = self::SELLER_REWARDS[$winner->rank] ?? null;
            
            if ($rewards) {
                // Update winner record
                $winner->update([
                    'reward_money' => $rewards['money'],
                    'reward_points' => $rewards['points']
                ]);

                // Add money to wallet
                $winner->user->increment('wallet_balance', $rewards['money']);

                // Add turbo points
                $winner->user->increment('turbo_points', $rewards['points']);

                // Create commission discount coupon
                $coupon = $this->createCouponForWinner(
                    $winner,
                    $rewards['commission_discount']
                );

                // Update winner with coupon code
                $winner->update(['coupon_code' => $coupon->code]);
            }
        }
    }

    /**
     * Create commission discount coupon for seller winner
     */
    private function createCouponForWinner(TurboWinner $winner, $discountPercent)
    {
        $code = TurboCoupon::generateCode();
        
        $validFrom = now();
        $validUntil = now()->addDays(30); // Valid for 30 days

        return TurboCoupon::create([
            'winner_id' => $winner->id,
            'seller_id' => $winner->user_id,
            'code' => $code,
            'commission_discount_percent' => $discountPercent,
            'min_order_amount' => 0,
            'max_uses' => 10, // Can be used 10 times
            'used_count' => 0,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
            'is_active' => true,
            'conditions' => [
                'description' => sprintf('%d. Sıra Turbo Mod Ödülü', $winner->rank),
                'competition_month' => $winner->competition->month,
                'competition_year' => $winner->competition->year
            ]
        ]);
    }

    /**
     * Check and finalize ended competitions
     */
    public function checkAndFinalizeEndedCompetitions()
    {
        $endedCompetitions = TurboCompetition::where('status', 'active')
            ->where('end_date', '<', now())
            ->get();

        foreach ($endedCompetitions as $competition) {
            $this->finalizeCompetition($competition->id);
        }

        // Create next month's competition if needed
        $currentCompetition = TurboCompetition::current();
        if (!$currentCompetition) {
            TurboCompetition::createForMonth(now()->year, now()->month);
        }
    }

    /**
     * Get competition history
     */
    public function getCompetitionHistory($limit = 12)
    {
        return TurboCompetition::where('status', 'finalized')
            ->with(['winners.user'])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's winning history
     */
    public function getUserWinningHistory($userId, $limit = 10)
    {
        return TurboWinner::where('user_id', $userId)
            ->with(['competition', 'coupon'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Apply turbo coupon to order
     */
    public function applyCoupon($couponCode, $order)
    {
        $coupon = TurboCoupon::where('code', $couponCode)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Geçersiz kupon kodu.'
            ];
        }

        // Verify seller owns this order
        if ($order->seller_id !== $coupon->seller_id) {
            return [
                'success' => false,
                'message' => 'Bu kupon sadece kodu alan satıcının siparişlerinde geçerlidir.'
            ];
        }

        return $coupon->apply($order);
    }

    /**
     * Get stats for dashboard
     */
    public function getStats()
    {
        $competition = $this->getCurrentCompetition();

        return [
            'current_competition' => [
                'id' => $competition->id,
                'month' => $competition->month,
                'year' => $competition->year,
                'start_date' => $competition->start_date->format('Y-m-d'),
                'end_date' => $competition->end_date->format('Y-m-d'),
                'days_remaining' => now()->diffInDays($competition->end_date, false),
                'status' => $competition->status
            ],
            'top_customers' => $this->getCustomerLeaderboard(5),
            'top_sellers' => $this->getSellerLeaderboard(5),
            'total_active_coupons' => TurboCoupon::where('is_active', true)
                ->where('valid_until', '>=', now())
                ->count(),
            'total_competitions' => TurboCompetition::where('status', 'finalized')->count()
        ];
    }
}
