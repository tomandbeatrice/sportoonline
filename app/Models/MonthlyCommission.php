<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyCommission extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'month',
        'total_sales',
        'commission_rate',
        'commission_amount',
        'subscription_fee',
        'net_commission',
        'order_count',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'total_sales' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'subscription_fee' => 'decimal:2',
        'net_commission' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Check if commission is positive (seller makes profit)
     */
    public function isProfitable(): bool
    {
        return $this->net_commission > 0;
    }

    /**
     * Get effective commission rate (including subscription fee)
     */
    public function getEffectiveCommissionRate(): float
    {
        if ($this->total_sales == 0) {
            return 0;
        }

        $totalCost = $this->commission_amount + $this->subscription_fee;
        return ($totalCost / $this->total_sales) * 100;
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}
