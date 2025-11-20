<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TurboCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'winner_id',
        'seller_id',
        'code',
        'commission_discount_percent',
        'min_order_amount',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
        'conditions'
    ];

    protected $casts = [
        'commission_discount_percent' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'conditions' => 'array'
    ];

    /**
     * Get the winner
     */
    public function winner()
    {
        return $this->belongsTo(TurboWinner::class);
    }

    /**
     * Get the seller
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get usage history
     */
    public function usage()
    {
        return $this->hasMany(TurboCouponUsage::class, 'coupon_id');
    }

    /**
     * Generate unique coupon code
     */
    public static function generateCode($prefix = 'TURBO')
    {
        do {
            $code = $prefix . strtoupper(Str::random(8));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Check if coupon is valid
     */
    public function isValid($orderAmount = null)
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Kupon aktif değil.'];
        }

        if (now()->lt($this->valid_from)) {
            return ['valid' => false, 'message' => 'Kupon henüz geçerli değil.'];
        }

        if (now()->gt($this->valid_until)) {
            return ['valid' => false, 'message' => 'Kupon süresi dolmuş.'];
        }

        if ($this->used_count >= $this->max_uses) {
            return ['valid' => false, 'message' => 'Kupon kullanım limiti dolmuş.'];
        }

        if ($orderAmount !== null && $orderAmount < $this->min_order_amount) {
            return [
                'valid' => false,
                'message' => sprintf('Minimum sipariş tutarı: ₺%s', number_format($this->min_order_amount, 2))
            ];
        }

        return ['valid' => true];
    }

    /**
     * Apply coupon to order
     */
    public function apply($order)
    {
        $validation = $this->isValid($order->subtotal);
        
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        // Calculate commission discount
        $originalCommission = $order->commission_amount;
        $discountAmount = ($originalCommission * $this->commission_discount_percent) / 100;
        $discountedCommission = $originalCommission - $discountAmount;

        // Create usage record
        TurboCouponUsage::create([
            'coupon_id' => $this->id,
            'order_id' => $order->id,
            'original_commission' => $originalCommission,
            'discounted_commission' => $discountedCommission,
            'savings' => $discountAmount
        ]);

        // Increment usage count
        $this->increment('used_count');

        // Update order commission
        $order->update([
            'commission_amount' => $discountedCommission,
            'turbo_coupon_code' => $this->code
        ]);

        return [
            'success' => true,
            'original_commission' => $originalCommission,
            'discounted_commission' => $discountedCommission,
            'savings' => $discountAmount,
            'message' => sprintf('Turbo kupon uygulandı! ₺%s komisyon indirimi.', number_format($discountAmount, 2))
        ];
    }

    /**
     * Get remaining uses
     */
    public function getRemainingUsesAttribute()
    {
        return max(0, $this->max_uses - $this->used_count);
    }

    /**
     * Check if expired
     */
    public function getIsExpiredAttribute()
    {
        return now()->gt($this->valid_until);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        if (!$this->is_active) return 'Pasif';
        if ($this->is_expired) return 'Süresi Dolmuş';
        if ($this->remaining_uses <= 0) return 'Tükendi';
        return 'Aktif';
    }
}
