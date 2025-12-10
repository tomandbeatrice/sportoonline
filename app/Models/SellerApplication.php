<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerApplication extends Model
{
    use HasFactory;

    // Hizmet türleri
    public const SERVICE_TYPES = [
        'products' => 'Ürün Satışı',
        'food' => 'Restoran / Yemek',
        'hotel' => 'Konaklama',
        'transport' => 'Ulaşım Hizmeti',
        'services' => 'Profesyonel Hizmet',
        'career' => 'İş İlanı Veren',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_name',
        'tax_number',
        'tax_office',
        'company_address',
        'bank_name',
        'iban',
        'account_holder',
        'categories',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'service_type',
        'service_data',
    ];

    protected $casts = [
        'categories' => 'array',
        'service_data' => 'array',
        'approved_at' => 'datetime',
    ];

    protected $appends = ['service_type_label'];

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Hizmet türü etiketini döndür
     */
    public function getServiceTypeLabelAttribute(): string
    {
        return self::SERVICE_TYPES[$this->service_type] ?? $this->service_type;
    }

    /**
     * Belirli bir hizmet türüne göre filtrele
     */
    public function scopeOfServiceType($query, string $type)
    {
        return $query->where('service_type', $type);
    }

    /**
     * Bekleyen başvuruları getir
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
