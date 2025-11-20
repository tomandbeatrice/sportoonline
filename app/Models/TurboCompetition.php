<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TurboCompetition extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    /**
     * Get winners for this competition
     */
    public function winners()
    {
        return $this->hasMany(TurboWinner::class, 'competition_id');
    }

    /**
     * Get customer winners
     */
    public function customerWinners()
    {
        return $this->winners()->where('category', 'customer')->orderBy('rank');
    }

    /**
     * Get seller winners
     */
    public function sellerWinners()
    {
        return $this->winners()->where('category', 'seller')->orderBy('rank');
    }

    /**
     * Get current active competition
     */
    public static function current()
    {
        return static::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * Create a new competition period
     */
    public static function createForMonth($year, $month)
    {
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return static::create([
            'year' => $year,
            'month' => $month,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active'
        ]);
    }

    /**
     * Get top customers by spending
     */
    public function getTopCustomers($limit = 5)
    {
        return DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.turbo_points',
                DB::raw('COALESCE(SUM(orders.total), 0) as total_spending'),
                DB::raw('COUNT(orders.id) as order_count')
            )
            ->leftJoin('orders', function($join) {
                $join->on('users.id', '=', 'orders.user_id')
                    ->where('orders.status', '=', 'completed')
                    ->whereBetween('orders.created_at', [$this->start_date, $this->end_date]);
            })
            ->where('users.role', 'buyer')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.turbo_points')
            ->orderByDesc('total_spending')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top sellers by revenue
     */
    public function getTopSellers($limit = 5)
    {
        return DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.turbo_points',
                DB::raw('COALESCE(SUM(order_items.price * order_items.quantity), 0) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->leftJoin('order_items', function($join) {
                $join->on('users.id', '=', 'order_items.seller_id');
            })
            ->leftJoin('orders', function($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.status', '=', 'completed')
                    ->whereBetween('orders.created_at', [$this->start_date, $this->end_date]);
            })
            ->where('users.role', 'seller')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.turbo_points')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's position in competition
     */
    public function getUserPosition($userId, $category = 'customer')
    {
        if ($category === 'customer') {
            $leaderboard = $this->getTopCustomers(100);
            $rankKey = 'total_spending';
        } else {
            $leaderboard = $this->getTopSellers(100);
            $rankKey = 'total_revenue';
        }

        $position = $leaderboard->search(function($user) use ($userId) {
            return $user->id === $userId;
        });

        if ($position === false) {
            return null;
        }

        $user = $leaderboard[$position];

        return [
            'rank' => $position + 1,
            'user' => $user,
            'total' => $user->$rankKey,
            'order_count' => $user->order_count,
            'turbo_points' => $user->turbo_points
        ];
    }

    /**
     * Check if competition has ended
     */
    public function hasEnded()
    {
        return now()->greaterThan($this->end_date);
    }

    /**
     * Finalize competition and determine winners
     */
    public function finalize()
    {
        if ($this->status === 'finalized') {
            return false;
        }

        $this->update(['status' => 'ended']);

        // Get top 3 customers
        $topCustomers = $this->getTopCustomers(3);
        
        // Get top 3 sellers
        $topSellers = $this->getTopSellers(3);

        // Create winner records
        foreach ($topCustomers as $index => $customer) {
            if ($customer->total_spending > 0) {
                TurboWinner::create([
                    'competition_id' => $this->id,
                    'category' => 'customer',
                    'user_id' => $customer->id,
                    'rank' => $index + 1,
                    'total_amount' => $customer->total_spending,
                    'reward_money' => 0,
                    'reward_points' => 0
                ]);
            }
        }

        foreach ($topSellers as $index => $seller) {
            if ($seller->total_revenue > 0) {
                TurboWinner::create([
                    'competition_id' => $this->id,
                    'category' => 'seller',
                    'user_id' => $seller->id,
                    'rank' => $index + 1,
                    'total_amount' => $seller->total_revenue,
                    'reward_money' => 0,
                    'reward_points' => 0
                ]);
            }
        }

        $this->update(['status' => 'finalized']);

        return true;
    }
}
