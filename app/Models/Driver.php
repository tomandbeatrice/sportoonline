<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'avatar',
        'license_number',
        'license_expiry',
        'license_type',
        'id_number',
        'address',
        'city',
        'date_of_birth',
        'experience_years',
        'languages',
        'rating',
        'total_trips',
        'status',
        'is_available',
        'current_location',
        'verified_at',
    ];

    protected $casts = [
        'languages' => 'array',
        'current_location' => 'array',
        'date_of_birth' => 'date',
        'license_expiry' => 'date',
        'verified_at' => 'datetime',
        'is_available' => 'boolean',
        'rating' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')->where('is_available', true);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isVerified()
    {
        return $this->verified_at !== null;
    }

    public function updateRating()
    {
        $avgRating = $this->reviews()->avg('rating') ?? 0;
        $this->update(['rating' => round($avgRating, 2)]);
    }

    public function incrementTrips()
    {
        $this->increment('total_trips');
    }
}
