<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'status',
        'message',
        'exported_at'
    ];

    protected $casts = [
        'exported_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scope: segment bazlı filtreleme için hazır
    public function scopeSegment($query, $segmentId)
    {
        return $query->where('product_id', $segmentId);
    }

    // Scope: son 7 gün logları
    public function scopeRecent($query)
    {
        return $query->where('exported_at', '>=', now()->subDays(7));
    }
}