<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'seller_id',
        'name',
        'description',
        'type',
        'discount_value',
        'min_quantity',
        'free_quantity',
        'min_purchase_amount',
        'coupon_code',
        'usage_limit',
        'usage_per_customer',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
        'applies_to',
        'product_ids',
        'category_ids',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'product_ids' => 'array',
        'category_ids' => 'array',
        'min_quantity' => 'integer',
        'free_quantity' => 'integer',
        'usage_limit' => 'integer',
        'usage_per_customer' => 'integer',
        'used_count' => 'integer',
    ];

    /**
     * Get the seller that owns the campaign
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get campaign usages
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CampaignUsage::class);
    }

    /**
     * Check if campaign is currently active
     */
    public function isCurrentlyActive(): bool
    {
        $now = now();
        
        return $this->is_active 
            && $this->start_date <= $now 
            && $this->end_date >= $now
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    /**
     * Check if user can use this campaign
     */
    public function canBeUsedBy(int $userId): bool
    {
        if (!$this->isCurrentlyActive()) {
            return false;
        }

        $userUsageCount = $this->usages()
            ->where('user_id', $userId)
            ->count();

        return $userUsageCount < $this->usage_per_customer;
    }

    /**
     * Calculate discount for given amount
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percentage') {
            return ($amount * $this->discount_value) / 100;
        }

        if ($this->type === 'fixed') {
            return min($this->discount_value, $amount);
        }

        return 0;
    }

    /**
     * Check if campaign applies to given products
     */
    public function appliesToProducts(array $productIds): bool
    {
        if ($this->applies_to === 'all_products') {
            return true;
        }

        if ($this->applies_to === 'specific_products') {
            return !empty(array_intersect($productIds, $this->product_ids ?? []));
        }

        // For specific_categories, need to check product categories
        return false;
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    /**
     * Scope for active campaigns
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Scope for campaigns with available usage
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('used_count < usage_limit');
        });
    }
}
