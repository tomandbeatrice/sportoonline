<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'provider',
        'display_name',
        'description',
        'credentials',
        'is_active',
        'is_test_mode',
        'sort_order',
        'supported_currencies',
        'min_amount',
        'max_amount',
        'commission_rate',
        'fixed_commission',
    ];

    protected $casts = [
        'credentials' => 'array',
        'supported_currencies' => 'array',
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'fixed_commission' => 'decimal:2',
    ];

    /**
     * Get transactions for this gateway
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Scope: only active gateways
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Calculate total commission for amount
     */
    public function calculateCommission(float $amount): float
    {
        $percentageCommission = ($amount * $this->commission_rate) / 100;
        return $percentageCommission + $this->fixed_commission;
    }

    /**
     * Check if amount is within limits
     */
    public function isAmountValid(float $amount): bool
    {
        if ($amount < $this->min_amount) {
            return false;
        }

        if ($this->max_amount && $amount > $this->max_amount) {
            return false;
        }

        return true;
    }
}
