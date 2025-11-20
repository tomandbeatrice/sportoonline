<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'status',
        'platform_fee',
        'seller_payout',
    ];

    protected $appends = ['status_tr'];

    /**
     * Get the translated status attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function statusTr(): Attribute
    {
        return new Attribute(
            get: fn () => match ($this->status) {
                'pending' => 'Beklemede',
                'processing' => 'İşleniyor',
                'shipped' => 'Kargolandı',
                'delivered' => 'Teslim Edildi',
                'cancelled' => 'İptal Edildi',
                default => 'Bilinmiyor',
            },
        );
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}