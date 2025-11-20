<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'seller_id',
        'vendor_id',
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'low_stock_threshold',
        'sku',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::observe(\App\Observers\ProductObserver::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    /**
     * Update stock and log history
     */
    public function updateStock(string $type, int $quantity, ?string $note = null, ?string $source = 'manual', ?int $orderId = null): bool
    {
        $oldStock = $this->stock;
        $newStock = $oldStock;

        switch ($type) {
            case 'add':
                $newStock = $oldStock + $quantity;
                break;
            case 'remove':
                $newStock = max(0, $oldStock - $quantity);
                break;
            case 'set':
                $newStock = $quantity;
                break;
        }

        $this->stock = $newStock;
        $this->save();

        StockHistory::log(
            $this->id,
            $type,
            $quantity,
            $oldStock,
            $newStock,
            $note,
            auth()->id(),
            $source,
            $orderId
        );

        // Check for low stock and send alert
        $this->checkLowStockAlert($newStock);

        return true;
    }

    /**
     * Check if stock is low and send alert to seller
     */
    private function checkLowStockAlert(int $stock): void
    {
        $lowStockThreshold = 10;

        // Only send alert if stock just went below threshold
        if ($stock <= $lowStockThreshold && $stock > 0 && $this->seller) {
            // Get all low stock products for this seller
            $lowStockProducts = self::where('seller_id', $this->seller_id)
                ->where('stock', '<=', $lowStockThreshold)
                ->where('stock', '>', 0)
                ->get();

            // Send email alert (limit to once per day to avoid spam)
            $cacheKey = "low_stock_alert_{$this->seller_id}";
            if (!\Cache::has($cacheKey)) {
                try {
                    \Mail::to($this->seller->email)->send(
                        new \App\Mail\LowStockAlert($this->seller, $lowStockProducts)
                    );
                    \Cache::put($cacheKey, true, now()->addDay());
                } catch (\Exception $e) {
                    \Log::error('Low stock alert email failed: ' . $e->getMessage());
                }
            }
        }
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}