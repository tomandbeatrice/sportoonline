# Gerçek Zamanlı Kur Dönüşümü Özelliği

## Özellik Özeti
Tüm fiyatlar seçilen para birimine gerçek zamanlı olarak dönüştürülür. Exchange rate API'ı 1 saat cache ile çalışır ve fallback mock oranları mevcut.

## Bileşenler

### 1. Exchange Rate Service
**Dosya:** `src/services/exchangeRateService.ts`
- **`getExchangeRates(baseCurrency)`** — Dış API'dan kurları alır (1 saat cache)
- **`convertCurrency(amount, from, to, rates)`** — Tutar dönüştürme
- **`clearCache()`** — Cache temizleme
- **Desteklenen Para Birimleri:** TRY, USD, EUR, GBP, JPY, CNY, AED, SAR

### 2. Currency Pinia Store
**Dosya:** `src/stores/currency.ts`
- **`selectedCurrency`** — Seçilen para birimi (localStorage persist)
- **`exchangeRates`** — Aktif kur verileri
- **`loading`** — Yükleme durumu
- **`error`** — Hata mesajı
- **Metodlar:**
  - `setSelectedCurrency(currency)` — Para birimini değiştir
  - `fetchExchangeRates(baseCurrency)` — Kurları async yükle
  - `getSymbol(currency)` — Para birimi sembolü al (₺, $, €, vb.)

### 3. Currency Converter Utils
**Dosya:** `src/utils/currencyConverter.ts`
- **`useConvertPrice(price, fromCurrency)`** — Fiyatı dönüştür ve döndür
- **`formatCurrencyWithConversion(amount, baseCurrency, decimals)`** — Sembol + fiyat
- **`formatPriceRange(min, max, baseCurrency)`** — Aralık formatı

### 4. Currency Selector Bileşeni
**Dosya:** `src/components/common/CurrencySelector.vue`
- Para birimi seçme dropdown'ı
- Yenileme butonu
- onMounted'da otomatik kur yükleme

## Entegrasyon Noktaları

### MarketplaceNavigation
- **CurrencySelector** sticky position'da kütüphanelerinin altına yerleştirildi

### MarketplaceHome
- `onMounted` hook'unda `currencyStore.fetchExchangeRates()` çağırılır

### Ürün Bileşenleri
- **MarketplaceTrending.vue:** `formatCurrencyWithConversion()` kullanılır
- **MarketplaceFlashSales.vue:** `formatCurrencyWithConversion()` kullanılır
- **ProductDetail.vue:** Ana fiyat için `formatCurrencyWithConversion()` kullanılır

## Doğrulama Hataları

### "Validation error: Required field missing"
- **Nedeni:** Test senaryolarında E2E test runner tarafından simüle edilir (ağırlık: %15)
- **Çözüm:** 
  - Currency store otomatik olarak localStorage'dan para birimini yükler
  - İlk değer: 'TRY'
  - Exchange rate API fallback mock oranları sağlar

### Mock Oranlar (API başarısız olursa)
```typescript
{
  'TRY': 1,
  'USD': 0.032,  // 1 TRY = 0.032 USD
  'EUR': 0.030,  // 1 TRY = 0.030 EUR
  'GBP': 0.025,
  'JPY': 4.72,
  'CNY': 0.23,
  'AED': 0.117,
  'SAR': 0.120
}
```

## Hata Yönetimi

1. **API hatası:** Console'a log edilir, mock oranlar kullanılır
2. **localStorage kullanılamaz:** Para birimi bellekte saklı kalır (session)
3. **Kur dönüştürme hatası:** Orijinal fiyat döndürülür (hata kaydedilir)

## Performans
- **1 saatlik cache:** API çağrılarını minimize eder
- **Lazy loading:** Kur verileri onMounted'da async yüklenir
- **localStorage persist:** Sayfa yenilemesinde state korunur

## Kullanım Örneği

```typescript
import { formatCurrencyWithConversion } from '@/utils/currencyConverter'

// Bileşen içinde:
<span>{{ formatCurrencyWithConversion(4500, 'TRY') }}</span>
<!-- Çıktı: ₺4500.00 (TRY seçiliyse) veya $144.00 (USD seçiliyse) -->
```

## Test Kontrol Listesi
- [x] CurrencySelector görüntüleniyor
- [x] Para birimi değiştirildiğinde fiyatlar güncelleniyor
- [x] API başarısız olduğunda mock oranlar kullanılıyor
- [x] localStorage para birimini persist ediyor
- [x] MarketplaceHome initialization başarılı
- [x] Ürün detay sayfasında kur dönüşümü çalışıyor
