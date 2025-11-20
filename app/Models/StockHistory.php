<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'old_stock',
        'new_stock',
        'note',
        'source',
        'order_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'old_stock' => 'integer',
        'new_stock' => 'integer',
    ];

    /**
     * Get the product that this history belongs to
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who made this change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order related to this stock change
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Create a stock history entry
     */
    public static function log(
        int $productId,
        string $type,
        int $quantity,
        int $oldStock,
        int $newStock,
        ?string $note = null,
        ?int $userId = null,
        ?string $source = 'manual',
        ?int $orderId = null
    ): self {
        return self::create([
            'product_id' => $productId,
            'user_id' => $userId ?? auth()->id(),
            'type' => $type,
            'quantity' => $quantity,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'note' => $note,
            'source' => $source,
            'order_id' => $orderId,
        ]);
    }
}
