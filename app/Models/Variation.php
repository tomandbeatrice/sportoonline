<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variation extends Model
{
    use HasFactory;

    protected $table = 'variants';

    protected $fillable = [
        'product_id',
        'name',
        'value',
        'price_adjustment',
        'stock',
    ];

    protected $casts = [
        'price_adjustment' => 'float',
        'stock' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
