<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncentiveCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'discount_amount',
        'discount_percentage',
        'min_order_value',
        'max_discount',
        'usage_limit',
        'usage_count',
        'is_active',
        'start_date',
        'end_date',
        'target_segments',
        'conditions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'target_segments' => 'array',
        'conditions' => 'array',
    ];
}
