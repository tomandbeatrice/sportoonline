<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'billing_cycle',
        'amount',
        'payment_method',
        'payment_gateway',
        'gateway_subscription_id',
        'auto_renew',
    ];

    protected $casts = [
        'trial_ends_at' => 'date',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'cancelled_at' => 'date',
        'amount' => 'decimal:2',
        'auto_renew' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }

    /**
     * Check if subscription is on trial
     */
    public function onTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription has expired
     */
    public function hasExpired(): bool
    {
        return $this->ends_at->isPast();
    }

    /**
     * Get days remaining
     */
    public function daysRemaining(): int
    {
        if ($this->hasExpired()) {
            return 0;
        }
        return Carbon::now()->diffInDays($this->ends_at);
    }

    /**
     * Cancel subscription
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
        ]);
    }

    /**
     * Renew subscription
     */
    public function renew(): void
    {
        $newEndsAt = $this->billing_cycle === 'yearly' 
            ? Carbon::now()->addYear()
            : Carbon::now()->addMonth();

        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => $newEndsAt,
            'cancelled_at' => null,
        ]);
    }
}
