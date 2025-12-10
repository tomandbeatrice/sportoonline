<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'driver_id',
        'brand',
        'model',
        'year',
        'plate_number',
        'color',
        'vehicle_type',
        'capacity',
        'fuel_type',
        'transmission',
        'features',
        'images',
        'insurance_expiry',
        'inspection_expiry',
        'status',
        'is_available',
    ];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
        'insurance_expiry' => 'date',
        'inspection_expiry' => 'date',
        'is_available' => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')->where('is_available', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('vehicle_type', $type);
    }

    public function getFullNameAttribute()
    {
        return "{$this->brand} {$this->model} ({$this->year})";
    }

    public function needsMaintenance()
    {
        return $this->insurance_expiry <= now()->addDays(30) ||
            $this->inspection_expiry <= now()->addDays(30);
    }
}
