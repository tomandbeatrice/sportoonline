<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'name',
        'email',
        'phone',
        'address',
        'shipping_name',
        'shipping_address',
        'shipping_phone',
        'shipping_city',
        'shipping_district',
        'billing_address',
        'payment_method',
        'payment_status',
        'status',
        'total_price',
        'total_amount',
        'subtotal',
        'total',
        'transaction_id',
        'shipping_provider',
        'tracking_code',
        'shipping_status',
        'notes',
        'campaign_id',
        'discount_amount',
    ];

    protected $casts = [
        'total_price' => 'float',
    ];

    protected $appends = ['status_tr', 'total'];


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
                'paid' => 'Ödendi',
                'shipped' => 'Kargolandı',
                'completed' => 'Tamamlandı',
                'cancelled' => 'İptal Edildi',
                default => 'Bilinmiyor',
            },
        );
    }

    /**
     * Get the total price of the items for the seller in this order.
     * Note: This relies on the 'items' relation being pre-filtered.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function total(): Attribute
    {
        return new Attribute(
            get: fn () => $this->items->sum(function ($item) {
                return $item->quantity * $item->price;
            })
        );
    }


    public $timestamps = true;

    // 🧩 İlişkiler

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(OrderLog::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function latestPaymentTransaction(): HasOne
    {
        return $this->hasOne(PaymentTransaction::class)->latestOfMany();
    }
}