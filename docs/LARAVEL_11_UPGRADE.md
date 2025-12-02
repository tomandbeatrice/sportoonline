# Laravel 11 Upgrade Checklist

## Ön Hazırlık

- [ ] Mevcut testlerin tamamının geçtiğinden emin ol
- [ ] Composer paketlerini güncelle
- [ ] PHP 8.2+ kullanıldığından emin ol

## Adımlar

### 1. composer.json Güncelleme

```bash
composer require laravel/framework:^11.0
```

### 2. Değişen Yapılar

#### Model Casts (v2 API)
```php
// Eski (Laravel 10)
protected $casts = [
    'is_active' => 'boolean',
    'metadata' => 'array',
];

// Yeni (Laravel 11)
protected function casts(): array
{
    return [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];
}
```

#### Application Bootstrap
- `bootstrap/app.php` dosyası yeni yapıya geçirilmeli
- `Kernel.php` dosyaları kaldırılacak

### 3. Breaking Changes

- `Illuminate\Http\Request` type hints
- `Illuminate\Contracts\Auth\Authenticatable` değişiklikler
- Route model binding changes

### 4. Yeni Özellikler

- **Lazy Collections** iyileştirmeleri
- **Eager Loading** optimization
- **Rate Limiting** improvements
- **Model Factories** enhancements

### 5. Test Strategy

```bash
# Tüm testleri çalıştır
php artisan test

# Belirli bir modül test et
php artisan test --filter=CheckoutFlowTest

# Coverage raporu
php artisan test --coverage --min=80
```

### 6. Migration Plan

1. Development ortamında test
2. Staging ortamında validation
3. Production deployment (rollback planı hazır)

## Fayda Analizi

| Özellik | Laravel 10 | Laravel 11 | İyileştirme |
|---------|------------|------------|-------------|
| Min PHP Version | 8.1 | 8.2 | +Modern syntax |
| LTS Support | March 2025 | Feb 2026 | +11 months |
| Performance | Baseline | +15% | +Speed |
| Type Safety | Good | Excellent | +Reliability |

## Risk Değerlendirmesi

**Düşük Risk:**
- Yeni projeler
- Küçük kod tabanı
- Az bağımlılık

**Orta Risk:** ⭐ (Bizim durumumuz)
- Orta ölçekli proje
- Çoklu paket bağımlılığı
- Test coverage %50+

**Yüksek Risk:**
- Legacy kod
- Düşük test coverage
- Çok fazla custom kod

## Tavsiye Edilen Zamanlama

🗓️ **Şimdi Hazırlık**: Testleri güçlendir, bağımlılıkları güncelle
🗓️ **1 Ay İçinde**: Development ortamında deneme
🗓️ **2 Ay İçinde**: Staging deployment
🗓️ **3 Ay İçinde**: Production deployment (Laravel 10 EOL'dan önce)
