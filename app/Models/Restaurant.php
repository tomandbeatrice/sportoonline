<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'owner_id',
        'address',
        'city',
        'district',
        'phone',
        'email',
        'latitude',
        'longitude',
        'opening_hours',
        'delivery_radius',
        'min_order_amount',
        'delivery_fee',
        'avg_delivery_time',
        'status',
        'is_featured',
        'logo',
        'cover_image',
        'rating',
        'review_count',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_featured' => 'boolean',
        'min_order_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function orders()
    {
        return $this->hasMany(FoodOrder::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
