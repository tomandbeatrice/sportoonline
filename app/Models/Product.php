<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'description',
        'price',
        'stock',
        'image'
    ];

    // Satıcı ilişkisi
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Görsel URL'si (opsiyonel)
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}