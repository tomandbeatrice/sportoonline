<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'driver_id',
        'order_number',
        'status',
        'subtotal',
        'delivery_fee',
        'discount',
        'total',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'delivery_instructions',
        'estimated_delivery_time',
        'actual_delivery_time',
        'payment_method',
        'payment_status',
        'note',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'estimated_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function items()
    {
        return $this->hasMany(FoodOrderItem::class, 'food_order_id');
    }
}
