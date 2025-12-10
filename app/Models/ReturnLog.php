<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnLog extends Model
{
    use HasFactory;

    protected $table = 'return_logs';

    protected $fillable = [
        'return_request_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Aksiyon tipleri
    const ACTION_CREATED = 'created';
    const ACTION_APPROVED = 'approved';
    const ACTION_REJECTED = 'rejected';
    const ACTION_SHIPPED = 'shipped';
    const ACTION_RECEIVED = 'received';
    const ACTION_INSPECTED = 'inspected';
    const ACTION_REFUND_INITIATED = 'refund_initiated';
    const ACTION_REFUND_COMPLETED = 'refund_completed';
    const ACTION_REFUND_FAILED = 'refund_failed';
    const ACTION_COMPLETED = 'completed';
    const ACTION_CANCELLED = 'cancelled';
    const ACTION_NOTE_ADDED = 'note_added';

    /**
     * İlişkiler
     */
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Aksiyon etiketini Türkçe döndür
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            self::ACTION_CREATED => 'İade talebi oluşturuldu',
            self::ACTION_APPROVED => 'İade talebi onaylandı',
            self::ACTION_REJECTED => 'İade talebi reddedildi',
            self::ACTION_SHIPPED => 'Ürün gönderildi',
            self::ACTION_RECEIVED => 'Ürün teslim alındı',
            self::ACTION_INSPECTED => 'Ürün incelendi',
            self::ACTION_REFUND_INITIATED => 'Para iadesi başlatıldı',
            self::ACTION_REFUND_COMPLETED => 'Para iadesi tamamlandı',
            self::ACTION_REFUND_FAILED => 'Para iadesi başarısız',
            self::ACTION_COMPLETED => 'İade tamamlandı',
            self::ACTION_CANCELLED => 'İade iptal edildi',
            self::ACTION_NOTE_ADDED => 'Not eklendi',
            default => 'Bilinmeyen işlem',
        };
    }
}
