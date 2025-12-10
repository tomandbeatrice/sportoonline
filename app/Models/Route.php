<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'origin',
        'origin_code',
        'origin_latitude',
        'origin_longitude',
        'destination',
        'destination_code',
        'destination_latitude',
        'destination_longitude',
        'distance',
        'estimated_duration',
        'base_price',
        'price_per_km',
        'route_type',
        'waypoints',
        'description',
        'is_popular',
        'is_active',
        'order',
    ];

    protected $casts = [
        'waypoints' => 'array',
        'origin_latitude' => 'decimal:8',
        'origin_longitude' => 'decimal:8',
        'destination_latitude' => 'decimal:8',
        'destination_longitude' => 'decimal:8',
        'base_price' => 'decimal:2',
        'price_per_km' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('route_type', $type);
    }

    public function calculatePrice($vehicleType = 'sedan')
    {
        $multipliers = [
            'sedan' => 1.0,
            'suv' => 1.3,
            'van' => 1.5,
            'minibus' => 2.0,
            'bus' => 3.0,
        ];

        $multiplier = $multipliers[$vehicleType] ?? 1.0;
        return ($this->base_price + ($this->distance * $this->price_per_km)) * $multiplier;
    }

    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->estimated_duration / 60);
        $minutes = $this->estimated_duration % 60;

        if ($hours > 0) {
            return "{$hours} saat {$minutes} dakika";
        }

        return "{$minutes} dakika";
    }

    public function getFormattedDistanceAttribute()
    {
        return number_format($this->distance, 1) . ' km';
    }
}
