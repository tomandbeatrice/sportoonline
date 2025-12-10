<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'return_requests';

    protected $fillable = [
        'order_id',
        'order_item_id',
        'user_id',
        'vendor_id',
        'return_number',
        'reason',
        'reason_category',
        'description',
        'status',
        'quantity',
        'refund_amount',
        'refund_method',
        'refund_status',
        'refunded_at',
        'images',
        'admin_notes',
        'vendor_notes',
        'customer_notes',
        'shipping_label_url',
        'return_shipping_code',
        'return_shipping_carrier',
        'tracking_number',
        'shipping_carrier',
        'received_at',
        'inspected_at',
        'inspection_notes',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'images' => 'array',
        'refund_amount' => 'decimal:2',
        'refunded_at' => 'datetime',
        'received_at' => 'datetime',
        'inspected_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // İade durumları
    const STATUS_PENDING = 'pending';           // Beklemede
    const STATUS_APPROVED = 'approved';         // Onaylandı
    const STATUS_REJECTED = 'rejected';         // Reddedildi
    const STATUS_SHIPPED = 'shipped';           // Gönderildi
    const STATUS_RECEIVED = 'received';         // Teslim alındı
    const STATUS_INSPECTING = 'inspecting';     // İnceleniyor
    const STATUS_REFUNDED = 'refunded';         // İade edildi
    const STATUS_COMPLETED = 'completed';       // Tamamlandı
    const STATUS_CANCELLED = 'cancelled';       // İptal edildi

    // İade sebep kategorileri
    const REASON_DEFECTIVE = 'defective';           // Kusurlu ürün
    const REASON_WRONG_ITEM = 'wrong_item';         // Yanlış ürün
    const REASON_NOT_AS_DESCRIBED = 'not_as_described'; // Açıklamaya uygun değil
    const REASON_DAMAGED = 'damaged';               // Hasarlı ürün
    const REASON_SIZE_FIT = 'size_fit';             // Beden uyumsuzluğu
    const REASON_CHANGED_MIND = 'changed_mind';     // Fikir değişikliği
    const REASON_LATE_DELIVERY = 'late_delivery';   // Geç teslimat
    const REASON_OTHER = 'other';                   // Diğer

    // Para iadesi durumları
    const REFUND_PENDING = 'pending';
    const REFUND_PROCESSING = 'processing';
    const REFUND_COMPLETED = 'completed';
    const REFUND_FAILED = 'failed';

    // Para iadesi yöntemleri
    const REFUND_METHOD_ORIGINAL = 'original';      // Orijinal ödeme yöntemine
    const REFUND_METHOD_WALLET = 'wallet';          // Cüzdana
    const REFUND_METHOD_BANK = 'bank_transfer';     // Banka transferi

    /**
     * İade numarası oluştur
     */
    public static function generateReturnNumber(): string
    {
        $prefix = 'RET';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return "{$prefix}{$date}{$random}";
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->return_number)) {
                $model->return_number = self::generateReturnNumber();
            }
            if (empty($model->status)) {
                $model->status = self::STATUS_PENDING;
            }
            if (empty($model->refund_status)) {
                $model->refund_status = self::REFUND_PENDING;
            }
        });
    }

    // ═══════════════════════════════════════════════════════════════════
    // İlişkiler (Relationships)
    // ═══════════════════════════════════════════════════════════════════

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function logs()
    {
        return $this->hasMany(ReturnLog::class, 'return_request_id');
    }

    // ═══════════════════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════════════════

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ═══════════════════════════════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════════════════════════════

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRefunded(): bool
    {
        return $this->refund_status === self::REFUND_COMPLETED;
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING]);
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    /**
     * Durum etiketini Türkçe döndür
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Beklemede',
            self::STATUS_APPROVED => 'Onaylandı',
            self::STATUS_REJECTED => 'Reddedildi',
            self::STATUS_SHIPPED => 'Gönderildi',
            self::STATUS_RECEIVED => 'Teslim Alındı',
            self::STATUS_INSPECTING => 'İnceleniyor',
            self::STATUS_REFUNDED => 'İade Edildi',
            self::STATUS_COMPLETED => 'Tamamlandı',
            self::STATUS_CANCELLED => 'İptal Edildi',
            default => 'Bilinmiyor',
        };
    }

    /**
     * Sebep kategorisi etiketini Türkçe döndür
     */
    public function getReasonCategoryLabelAttribute(): string
    {
        return match($this->reason_category) {
            self::REASON_DEFECTIVE => 'Kusurlu Ürün',
            self::REASON_WRONG_ITEM => 'Yanlış Ürün Gönderildi',
            self::REASON_NOT_AS_DESCRIBED => 'Açıklamaya Uygun Değil',
            self::REASON_DAMAGED => 'Hasarlı Ürün',
            self::REASON_SIZE_FIT => 'Beden/Boyut Uyumsuzluğu',
            self::REASON_CHANGED_MIND => 'Fikir Değişikliği',
            self::REASON_LATE_DELIVERY => 'Geç Teslimat',
            self::REASON_OTHER => 'Diğer',
            default => 'Belirtilmemiş',
        };
    }

    /**
     * Para iadesi durumu etiketini Türkçe döndür
     */
    public function getRefundStatusLabelAttribute(): string
    {
        return match($this->refund_status) {
            self::REFUND_PENDING => 'Bekliyor',
            self::REFUND_PROCESSING => 'İşleniyor',
            self::REFUND_COMPLETED => 'Tamamlandı',
            self::REFUND_FAILED => 'Başarısız',
            default => 'Bilinmiyor',
        };
    }
}
