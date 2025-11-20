<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurboCouponUsage extends Model
{
    use HasFactory;

    protected $table = 'turbo_coupon_usage';

    protected $fillable = [
        'coupon_id',
        'order_id',
        'original_commission',
        'discounted_commission',
        'savings'
    ];

    protected $casts = [
        'original_commission' => 'decimal:2',
        'discounted_commission' => 'decimal:2',
        'savings' => 'decimal:2'
    ];

    /**
     * Get the coupon
     */
    public function coupon()
    {
        return $this->belongsTo(TurboCoupon::class);
    }

    /**
     * Get the order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
