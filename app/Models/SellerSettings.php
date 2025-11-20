<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'bank_name',
        'account_holder',
        'iban',
        'branch_code',
        'shipping_carriers',
        'shipping_zones',
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'company_type',
        'tax_office',
        'tax_number',
        'trade_registry_number',
        'company_title',
    ];

    protected $casts = [
        'shipping_carriers' => 'array',
        'shipping_zones' => 'array',
        'email_notifications' => 'array',
        'sms_notifications' => 'array',
        'push_notifications' => 'array',
    ];

    /**
     * Get the vendor that owns the settings
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
