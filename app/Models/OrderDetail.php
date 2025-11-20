<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    /**
     * Bu detayın ait olduğu ürün ilişkisi.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}