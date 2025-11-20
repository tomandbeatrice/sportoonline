<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'yearly_price',
        'product_limit',
        'image_limit_per_product',
        'max_file_size_mb',
        'bulk_upload',
        'advanced_analytics',
        'priority_support',
        'api_access',
        'commission_rate',
        'is_active',
        'trial_days',
        'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'bulk_upload' => 'boolean',
        'advanced_analytics' => 'boolean',
        'priority_support' => 'boolean',
        'api_access' => 'boolean',
        'is_active' => 'boolean',
        'features' => 'array',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if plan allows bulk upload
     */
    public function allowsBulkUpload(): bool
    {
        return $this->bulk_upload;
    }

    /**
     * Check if user has reached product limit
     */
    public function hasReachedProductLimit(User $user): bool
    {
        return $user->products()->count() >= $this->product_limit;
    }

    /**
     * Get remaining products for user
     */
    public function getRemainingProducts(User $user): int
    {
        return max(0, $this->product_limit - $user->products()->count());
    }
}
