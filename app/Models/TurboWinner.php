<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurboWinner extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'category',
        'user_id',
        'rank',
        'total_amount',
        'reward_money',
        'reward_points',
        'coupon_code'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'reward_money' => 'decimal:2',
        'reward_points' => 'integer'
    ];

    /**
     * Get the competition
     */
    public function competition()
    {
        return $this->belongsTo(TurboCompetition::class);
    }

    /**
     * Get the winner user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the coupon (for sellers only)
     */
    public function coupon()
    {
        return $this->hasOne(TurboCoupon::class, 'winner_id');
    }

    /**
     * Get rank badge
     */
    public function getRankBadgeAttribute()
    {
        return match($this->rank) {
            1 => '🥇',
            2 => '🥈',
            3 => '🥉',
            default => ''
        };
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute()
    {
        return $this->category === 'customer' ? 'En İyi Müşteri' : 'En İyi Satıcı';
    }
}
