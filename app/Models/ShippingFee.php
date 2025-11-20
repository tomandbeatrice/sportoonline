<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingFee extends Model
{
    protected $fillable = [
        'user_id',
        'region',
        'fee',
        'free_shipping_threshold',
        'is_active',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if order qualifies for free shipping
     */
    public function qualifiesForFreeShipping(float $orderTotal): bool
    {
        return $this->free_shipping_threshold > 0 && $orderTotal >= $this->free_shipping_threshold;
    }

    /**
     * Calculate shipping fee for order
     */
    public function calculateFee(float $orderTotal): float
    {
        if ($this->qualifiesForFreeShipping($orderTotal)) {
            return 0;
        }

        return $this->fee;
    }

    /**
     * Common Turkish regions
     */
    public static function getRegions(): array
    {
        return [
            'istanbul' => 'İstanbul',
            'ankara' => 'Ankara',
            'izmir' => 'İzmir',
            'marmara' => 'Marmara Bölgesi',
            'ege' => 'Ege Bölgesi',
            'akdeniz' => 'Akdeniz Bölgesi',
            'ic_anadolu' => 'İç Anadolu',
            'karadeniz' => 'Karadeniz',
            'dogu_anadolu' => 'Doğu Anadolu',
            'guneydogu_anadolu' => 'Güneydoğu Anadolu',
        ];
    }

    /**
     * Get city to region mapping
     */
    public static function getCityRegion(string $city): string
    {
        $cityMap = [
            'İstanbul' => 'istanbul',
            'Ankara' => 'ankara',
            'İzmir' => 'izmir',
            'Bursa' => 'marmara',
            'Kocaeli' => 'marmara',
            'Tekirdağ' => 'marmara',
            'Edirne' => 'marmara',
            'Antalya' => 'akdeniz',
            'Mersin' => 'akdeniz',
            'Adana' => 'akdeniz',
            'Muğla' => 'ege',
            'Aydın' => 'ege',
            'Denizli' => 'ege',
            'Manisa' => 'ege',
            'Balıkesir' => 'ege',
            'Konya' => 'ic_anadolu',
            'Kayseri' => 'ic_anadolu',
            'Eskişehir' => 'ic_anadolu',
            'Samsun' => 'karadeniz',
            'Trabzon' => 'karadeniz',
            'Ordu' => 'karadeniz',
            'Erzurum' => 'dogu_anadolu',
            'Van' => 'dogu_anadolu',
            'Ağrı' => 'dogu_anadolu',
            'Diyarbakır' => 'guneydogu_anadolu',
            'Gaziantep' => 'guneydogu_anadolu',
            'Şanlıurfa' => 'guneydogu_anadolu',
        ];

        return $cityMap[$city] ?? 'ic_anadolu'; // Default to İç Anadolu
    }
}
