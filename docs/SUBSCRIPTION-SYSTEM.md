# 💎 Abonelik Sistemi - SportOnline

## ✅ Eklenen Özellikler

### 📊 Veritabanı Tabloları
1. **subscription_plans** - Abonelik planları
2. **subscriptions** - Kullanıcı abonelikleri
3. **subscription_payments** - Ödeme geçmişi
4. **users** - Ek kolonlar (subscription_plan_id, subscription_status, subscription_ends_at)

### 🎯 Abonelik Planları

| Plan | Fiyat (Aylık) | Fiyat (Yıllık) | Ürün Limiti | Fotoğraf/Ürün | Dosya Boyutu | Komisyon | Özellikler |
|------|---------------|----------------|-------------|---------------|--------------|----------|-----------|
| **Basic** | ₺0 (Ücretsiz) | ₺0 | 100 | 5 | 2 MB | %15 | Temel özellikler, 30 gün deneme |
| **Premium** | ₺99 | ₺990 | 1,000 | 10 | 5 MB | %12 | CSV yükleme, gelişmiş analitik, 14 gün deneme |
| **Enterprise** | ₺499 | ₺4,990 | 10,000 | 15 | 10 MB | %10 | Excel yükleme, API, öncelikli destek, 7 gün deneme |
| **Unlimited** | ₺999 | ₺9,990 | 999,999 | 20 | 20 MB | %8 | Sınırsız ürün, VIP destek, özel entegrasyonlar, 7 gün deneme |

### 🔥 Öne Çıkan Özellikler

**Basic Plan:**
- ✅ 100 ürün limiti
- ✅ Ürün başına 5 fotoğraf
- ✅ Temel raporlama
- ✅ Email destek
- ✅ %15 komisyon
- ✅ 30 gün ücretsiz deneme

**Premium Plan:** (En Popüler)
- ✅ 1,000 ürün limiti
- ✅ Ürün başına 10 fotoğraf
- ✅ CSV toplu yükleme
- ✅ Gelişmiş raporlama ve analitik
- ✅ %12 komisyon
- ✅ Email ve telefon destek
- ✅ 14 gün ücretsiz deneme

**Enterprise Plan:**
- ✅ 10,000 ürün limiti
- ✅ Ürün başına 15 fotoğraf
- ✅ CSV ve Excel toplu yükleme
- ✅ Tam analitik dashboard
- ✅ API erişimi
- ✅ %10 komisyon
- ✅ Öncelikli destek (7/24)
- ✅ Özel hesap yöneticisi
- ✅ 7 gün ücretsiz deneme

**Unlimited Plan:**
- ✅ Sınırsız ürün (999,999)
- ✅ Ürün başına 20 fotoğraf
- ✅ CSV ve Excel toplu yükleme
- ✅ Tam analitik dashboard
- ✅ API erişimi
- ✅ %8 komisyon (en düşük)
- ✅ VIP destek (7/24)
- ✅ Özel hesap yöneticisi
- ✅ Özel entegrasyonlar
- ✅ White-label seçenekleri
- ✅ 7 gün ücretsiz deneme

## 📡 API Endpoints

### Public Endpoints
```bash
GET /api/subscriptions/plans
# Tüm aktif abonelik planlarını listele
```

### Authenticated Endpoints (Satıcılar)

**Mevcut Abonelik Durumu:**
```bash
GET /api/subscriptions/my-subscription
Authorization: Bearer {token}

Response:
{
  "subscription": {...},
  "plan": {
    "name": "Premium",
    "product_limit": 1000,
    ...
  },
  "is_active": true,
  "on_trial": false,
  "days_remaining": 25,
  "product_count": 450,
  "product_limit": 1000,
  "remaining_products": 550
}
```

**Abonelik Başlat:**
```bash
POST /api/subscriptions/subscribe
Authorization: Bearer {token}

Body:
{
  "plan_id": 2,
  "billing_cycle": "monthly", // or "yearly"
  "payment_method": "credit_card"
}

Response:
{
  "success": true,
  "message": "Abonelik başarıyla oluşturuldu",
  "subscription": {...}
}
```

**Abonelik İptal:**
```bash
POST /api/subscriptions/cancel
Authorization: Bearer {token}

Response:
{
  "success": true,
  "message": "Abonelik iptal edildi. Mevcut dönem sonuna kadar kullanabilirsiniz."
}
```

**Abonelik Yükseltme:**
```bash
POST /api/subscriptions/upgrade
Authorization: Bearer {token}

Body:
{
  "plan_id": 3
}

Response:
{
  "success": true,
  "message": "Abonelik yükseltildi",
  "prorated_amount": 350.00
}
```

**Abonelik Düşürme:**
```bash
POST /api/subscriptions/downgrade
Authorization: Bearer {token}

Body:
{
  "plan_id": 1
}

Response:
{
  "success": true,
  "message": "Düşürme talebi alındı. Mevcut dönem sonunda aktif olacak."
}
```

**Abonelik Yenileme:**
```bash
POST /api/subscriptions/renew
Authorization: Bearer {token}

Response:
{
  "success": true,
  "message": "Abonelik yenilendi"
}
```

**Ödeme Geçmişi:**
```bash
GET /api/subscriptions/payment-history
Authorization: Bearer {token}

Response: Paginated list of payments
```

**Limit Kontrolü:**
```bash
GET /api/subscriptions/check-limits
Authorization: Bearer {token}

Response:
{
  "has_subscription": true,
  "plan_name": "Premium",
  "product_count": 450,
  "product_limit": 1000,
  "remaining_products": 550,
  "can_add_products": true,
  "bulk_upload_enabled": true,
  "image_limit_per_product": 10,
  "max_file_size_mb": 5,
  "days_remaining": 25
}
```

## 💻 Frontend Component

**Kullanım:**
```vue
<template>
  <SubscriptionPlans />
</template>

<script setup>
import SubscriptionPlans from '@/components/subscription/SubscriptionPlans.vue'
</script>
```

**Dosya:** `src/components/subscription/SubscriptionPlans.vue`

**Özellikler:**
- 📊 4 plan kartı (grid layout)
- 🔄 Aylık/Yıllık fatura geçişi
- 💳 Abonelik seçimi
- 📈 Mevcut abonelik durumu gösterimi
- ⏱️ Kalan gün sayacı
- 📊 Ürün kullanım progress bar
- ❌ İptal etme butonu
- 🔄 Yenileme butonu

## 🔄 İş Akışı

### 1. Yeni Kullanıcı (Trial)
```
Kayıt Ol → Basic Plan (30 gün deneme) → Trial biter → Ödeme yap veya plan seç
```

### 2. Abonelik Başlatma
```
Plan Seç → Billing Cycle (Monthly/Yearly) → Ödeme → Aktif Abonelik → Ürün Eklemeye Başla
```

### 3. Upgrade (Yükseltme)
```
Mevcut Plan → Daha Üst Plan Seç → Prorated Ödeme → Anında Aktif
```

### 4. Downgrade (Düşürme)
```
Mevcut Plan → Daha Alt Plan Seç → Mevcut Dönem Sonunda Aktif
```

### 5. İptal
```
Aktif Abonelik → İptal Et → Mevcut Dönem Sonuna Kadar Kullan → Expired
```

### 6. Yenileme
```
İptal Edilmiş/Expired → Yenile → Aktif (Yeni Dönem Başlar)
```

## 🎨 Durum Yönetimi

**Subscription Status:**
- `trial` - Deneme sürümü
- `active` - Aktif abonelik
- `inactive` - Pasif
- `cancelled` - İptal edildi (dönem sonuna kadar kullanılabilir)
- `expired` - Süresi doldu

**Payment Status:**
- `pending` - Beklemede
- `completed` - Tamamlandı
- `failed` - Başarısız
- `refunded` - İade edildi

## 🔐 Middleware ve Validasyon

**Ürün Ekleme Kontrolü:**
```php
// ProductController@store
$subscription = auth()->user()->subscriptions()->active()->first();
if (!$subscription) {
    return response()->json(['error' => 'Aktif abonelik gerekli'], 403);
}

$plan = $subscription->plan;
if (auth()->user()->products()->count() >= $plan->product_limit) {
    return response()->json(['error' => 'Ürün limitine ulaştınız'], 403);
}
```

**Bulk Upload Kontrolü:**
```php
// BulkProductController@uploadCsv
$subscription = auth()->user()->subscriptions()->active()->first();
$plan = $subscription->plan;

if (!$plan->bulk_upload) {
    return response()->json([
        'error' => 'Toplu yükleme için Premium veya daha üst plan gerekli'
    ], 403);
}
```

## 📊 Raporlama

**Admin Dashboard:**
```sql
-- Toplam abonelik geliri
SELECT SUM(amount) FROM subscription_payments WHERE status = 'completed'

-- Plan bazlı dağılım
SELECT sp.name, COUNT(*) as subscribers 
FROM subscriptions s 
JOIN subscription_plans sp ON s.subscription_plan_id = sp.id
WHERE s.status = 'active'
GROUP BY sp.name

-- Aylık yinelenen gelir (MRR)
SELECT SUM(amount) FROM subscriptions 
WHERE status = 'active' 
AND billing_cycle = 'monthly'
```

## 🎯 Gelecek Geliştirmeler

- [ ] Ödeme gateway entegrasyonu (Iyzico, Stripe)
- [ ] Otomatik yenileme (cron job)
- [ ] Fatura PDF oluşturma
- [ ] Email bildirimleri (trial bitti, abonelik yenileme)
- [ ] Kupon sistemi
- [ ] Referans programı
- [ ] Multi-currency desteği
- [ ] Plan karşılaştırma tablosu
- [ ] Usage analytics (günlük kullanım grafikleri)
- [ ] Soft limits (limit aşımında uyarı)

## 🚀 Test

```bash
# Migration
php artisan migrate

# Seed plans
php artisan db:seed --class=SubscriptionPlanSeeder

# Test API
curl http://localhost:8000/api/subscriptions/plans

# Authenticated request
curl -H "Authorization: Bearer {token}" \
     http://localhost:8000/api/subscriptions/my-subscription
```

## 📞 Destek

Abonelik sistemi ile ilgili sorularınız için:
- Email: billing@sportoonline.com
- Dokümantasyon: /docs/subscriptions
- API Reference: /api/documentation#subscriptions
