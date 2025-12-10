<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'address',
        'city',
        'district',
        'country',
        'postal_code',
        'phone',
        'email',
        'website',
        'latitude',
        'longitude',
        'stars',
        'check_in_time',
        'check_out_time',
        'policies',
        'status',
        'is_featured',
        'images',
        'rating',
        'review_count',
    ];

    protected $casts = [
        'policies' => 'array',
        'images' => 'array',
        'is_featured' => 'boolean',
        'rating' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'hotel_amenities');
    }
}
