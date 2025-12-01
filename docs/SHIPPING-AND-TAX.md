# 📦 Kargo Ücreti ve 💰 E-Ticaret Stopajı Sistemi

## 🎯 Genel Bakış

Platformumuz iki önemli kesinti sistemi içermektedir:

1. **Kargo Ücreti:** Satıcıdan tahsil edilir (müşteri ödese bile)
2. **E-Ticaret Stopajı:** Yeni yasaya göre %1 veya %2

## 📦 Kargo Ücreti Sistemi

### Nasıl Çalışır?

**Müşteri Perspektifi:**
- Ürün fiyatı: ₺1,000
- Kargo ücreti: ₺30
- **Toplam ödeme: ₺1,030**

**Satıcı Perspektifi:**
- Satış tutarı: ₺1,000
- Komisyon (%12): -₺120
- **Kargo ücreti (satıcıdan kesilir): -₺30**
- Stopaj (%1): -₺10
- **Net kazanç: ₺840**

**Platform Perspektifi:**
- Komisyon: +₺120
- Kargo ücreti: +₺30
- Stopaj: +₺10
- **Toplam gelir: ₺160**

### Bölgesel Kargo Fiyatlandırması

Türkiye 10 bölgeye ayrılmıştır:

| Bölge | Örnek Şehirler | Önerilen Ücret |
|-------|----------------|----------------|
| İstanbul | İstanbul | ₺20-30 |
| Ankara | Ankara | ₺25-35 |
| İzmir | İzmir | ₺25-35 |
| Marmara | Bursa, Kocaeli, Tekirdağ | ₺30-40 |
| Ege | Muğla, Aydın, Denizli | ₺35-45 |
| Akdeniz | Antalya, Mersin, Adana | ₺35-45 |
| İç Anadolu | Konya, Kayseri, Eskişehir | ₺40-50 |
| Karadeniz | Samsun, Trabzon, Ordu | ₺45-55 |
| Doğu Anadolu | Erzurum, Van, Ağrı | ₺50-65 |
| Güneydoğu Anadolu | Diyarbakır, Gaziantep, Şanlıurfa | ₺50-65 |

### Ücretsiz Kargo Kampanyası

Satıcılar belirli bir tutarın üzerindeki siparişlerde ücretsiz kargo sunabilir:

```
Sipariş Tutarı: ₺500
Ücretsiz Kargo Limiti: ₺400
Sonuç: Ücretsiz Kargo ✅

Sipariş Tutarı: ₺300
Ücretsiz Kargo Limiti: ₺400
Sonuç: Normal kargo ücreti uygulanır (ör: ₺30)
```

### API Kullanımı

#### Kargo Ücretlerini Listele
```http
GET /api/seller/shipping
Authorization: Bearer {token}

Response:
{
  "shipping_fees": [
    {
      "id": 1,
      "user_id": 123,
      "region": "istanbul",
      "fee": 25.00,
      "free_shipping_threshold": 500.00,
      "is_active": true
    },
    ...
  ],
  "available_regions": {
    "istanbul": "İstanbul",
    "ankara": "Ankara",
    ...
  }
}
```

#### Kargo Ücreti Kaydet/Güncelle
```http
POST /api/seller/shipping/upsert
Authorization: Bearer {token}

Body:
{
  "region": "istanbul",
  "fee": 25.00,
  "free_shipping_threshold": 500.00,
  "is_active": true
}

Response:
{
  "message": "Kargo ücreti kaydedildi",
  "shipping_fee": {...}
}
```

#### Tüm Bölgelere Varsayılan Ücret Ayarla
```http
POST /api/seller/shipping/set-defaults
Authorization: Bearer {token}

Body:
{
  "default_fee": 30.00,
  "free_shipping_threshold": 400.00
}

Response:
{
  "message": "Tüm bölgeler için varsayılan kargo ücreti ayarlandı",
  "shipping_fees": [...]
}
```

#### Kargo Ücreti Hesapla
```http
POST /api/seller/shipping/calculate
Authorization: Bearer {token}

Body:
{
  "city": "İstanbul",
  "subtotal": 450.00
}

Response:
{
  "fee": 25.00,
  "region": "istanbul",
  "free_shipping": false,
  "threshold": 500.00,
  "message": null
}
```

## 💰 E-Ticaret Stopaj Kesintisi

### Yeni Yasa Gereklilikleri

2024 yılından itibaren e-ticaret platformları satıcılardan stopaj kesmekle yükümlüdür:

- **Kurumsal Satıcılar (Şirket, Vergi Numarası Olan):** %1
- **Bireysel Satıcılar (Gerçek Kişi):** %2

### Hesaplama Yöntemi

Stopaj **satış tutarı üzerinden** hesaplanır (komisyon sonrası değil):

**Bireysel Satıcı Örneği:**
```
Satış Tutarı: ₺10,000
Stopaj Oranı: %2 (bireysel)
Stopaj Tutarı: ₺10,000 × 0.02 = ₺200
```

**Kurumsal Satıcı Örneği:**
```
Satış Tutarı: ₺10,000
Stopaj Oranı: %1 (kurumsal)
Stopaj Tutarı: ₺10,000 × 0.01 = ₺100
```

### Satıcı Tipi Belirleme

Sistem otomatik olarak satıcı tipini belirler:

```php
if ($seller->business_type === 'corporate' || $seller->tax_number) {
    $rate = 1.00; // %1 kurumsal
} else {
    $rate = 2.00; // %2 bireysel
}
```

### Stopaj Raporlama

#### Aylık Stopaj Özeti
```http
GET /api/admin/withholding-tax/monthly-summary?month=2025-11
Authorization: Bearer {admin-token}

Response:
{
  "month": "2025-11",
  "summary": {
    "total_withholding_tax": 45000.00,
    "total_shipping_fees": 15000.00,
    "total_commission": 80000.00,
    "total_platform_revenue": 140000.00,
    "order_count": 500
  },
  "by_tax_rate": [
    {
      "rate": 1.00,
      "count": 300,
      "total_tax": 25000.00,
      "total_sales": 2500000.00
    },
    {
      "rate": 2.00,
      "count": 200,
      "total_tax": 20000.00,
      "total_sales": 1000000.00
    }
  ],
  "by_seller": [...]
}
```

#### Yıllık Stopaj Raporu
```http
GET /api/admin/withholding-tax/annual-report?year=2025
Authorization: Bearer {admin-token}

Response:
{
  "year": 2025,
  "monthly_breakdown": [
    {
      "month": "2025-01",
      "total_tax": 35000.00,
      "total_shipping": 12000.00,
      "total_commission": 65000.00,
      "order_count": 420
    },
    ...
  ],
  "yearly_summary": {
    "total_withholding_tax": 540000.00,
    "total_shipping_fees": 180000.00,
    "total_commission": 960000.00,
    "total_orders": 6000
  }
}
```

#### Stopaj Verilerini Dışa Aktar (Muhasebe İçin)
```http
GET /api/admin/withholding-tax/export?month=2025-11
Authorization: Bearer {admin-token}

Response:
{
  "month": "2025-11",
  "data": [
    {
      "order_id": 12345,
      "order_date": "2025-11-15",
      "seller_name": "ABC Mağaza",
      "seller_tax_number": "1234567890",
      "customer_name": "Ahmet Yılmaz",
      "subtotal": 1000.00,
      "shipping_fee": 30.00,
      "commission_rate": 12,
      "commission_amount": 120.00,
      "withholding_tax_rate": 1.00,
      "withholding_tax_amount": 10.00,
      "seller_net_amount": 840.00,
      "platform_revenue": 160.00
    },
    ...
  ],
  "total_records": 500
}
```

## 🧮 Tam Finansal Hesaplama Örneği

### Senaryo: Premium Plan Satıcı, İstanbul'a Sipariş

**Girdi Değerleri:**
- Ürün fiyatı: ₺1,000
- Satıcı planı: Premium (%12 komisyon)
- Satıcı tipi: Bireysel (%2 stopaj)
- Teslimat: İstanbul (₺25 kargo)
- Ücretsiz kargo limiti: ₺500 (aşıldı ✅)

**Hesaplama:**

1. **Kargo Ücreti:**
   ```
   Sipariş tutarı: ₺1,000
   Ücretsiz kargo limiti: ₺500
   ₺1,000 > ₺500 → Ücretsiz kargo ✅
   Kargo ücreti: ₺0
   ```

2. **Komisyon:**
   ```
   Satış tutarı: ₺1,000
   Komisyon oranı: %12
   Komisyon tutarı: ₺1,000 × 0.12 = ₺120
   ```

3. **Stopaj:**
   ```
   Satış tutarı: ₺1,000
   Stopaj oranı: %2 (bireysel)
   Stopaj tutarı: ₺1,000 × 0.02 = ₺20
   ```

4. **Satıcı Net Kazancı:**
   ```
   Satış tutarı: ₺1,000
   - Komisyon: -₺120
   - Kargo: -₺0 (ücretsiz)
   - Stopaj: -₺20
   ─────────────────
   Net kazanç: ₺860
   ```

5. **Platform Geliri:**
   ```
   Komisyon: +₺120
   Kargo: +₺0
   Stopaj: +₺20
   ─────────────────
   Toplam gelir: ₺140
   ```

6. **Müşteri Ödemesi:**
   ```
   Ürün fiyatı: ₺1,000
   Kargo ücreti: ₺0 (ücretsiz)
   ─────────────────
   Toplam ödeme: ₺1,000
   ```

### Senaryo 2: Kurumsal Satıcı, Doğu Anadolu'ya Sipariş

**Girdi Değerleri:**
- Ürün fiyatı: ₺5,000
- Satıcı planı: Büyük Paket (%8 komisyon)
- Satıcı tipi: Kurumsal (%1 stopaj)
- Teslimat: Erzurum (₺60 kargo)
- Ücretsiz kargo limiti: ₺10,000 (aşılmadı ❌)

**Hesaplama:**

1. **Kargo Ücreti:**
   ```
   Sipariş tutarı: ₺5,000
   Ücretsiz kargo limiti: ₺10,000
   ₺5,000 < ₺10,000 → Normal kargo ❌
   Kargo ücreti: ₺60
   ```

2. **Komisyon:**
   ```
   Satış tutarı: ₺5,000
   Komisyon oranı: %8
   Komisyon tutarı: ₺5,000 × 0.08 = ₺400
   ```

3. **Stopaj:**
   ```
   Satış tutarı: ₺5,000
   Stopaj oranı: %1 (kurumsal)
   Stopaj tutarı: ₺5,000 × 0.01 = ₺50
   ```

4. **Satıcı Net Kazancı:**
   ```
   Satış tutarı: ₺5,000
   - Komisyon: -₺400
   - Kargo: -₺60
   - Stopaj: -₺50
   ─────────────────
   Net kazanç: ₺4,490
   ```

5. **Platform Geliri:**
   ```
   Komisyon: +₺400
   Kargo: +₺60
   Stopaj: +₺50
   ─────────────────
   Toplam gelir: ₺510
   ```

6. **Müşteri Ödemesi:**
   ```
   Ürün fiyatı: ₺5,000
   Kargo ücreti: ₺60
   ─────────────────
   Toplam ödeme: ₺5,060
   ```

## 📊 Aylık Komisyon Hesaplama (Güncellenmiş)

Aylık komisyon hesaplaması artık **kargo ve stopaj** da içeriyor:

```
Aylık Satışlar: ₺100,000
Komisyon (%12): ₺12,000
Abonelik Ücreti: ₺149
Toplam Kargo Ücretleri: ₺1,500
Toplam Stopaj: ₺2,000
─────────────────────────────────
Net Komisyon: ₺100,000 - ₺12,000 - ₺149 - ₺1,500 - ₺2,000 = ₺84,351

Satıcıya Ödenecek: ₺84,351
```

## 🗄️ Veritabanı Yapısı

### shipping_fees Tablosu
```sql
CREATE TABLE shipping_fees (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    region VARCHAR(255),
    fee DECIMAL(8,2),
    free_shipping_threshold DECIMAL(10,2),
    is_active BOOLEAN,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(user_id, region)
);
```

### orders Tablosu (Eklenen Alanlar)
```sql
ALTER TABLE orders ADD COLUMN shipping_fee DECIMAL(8,2) DEFAULT 0;
ALTER TABLE orders ADD COLUMN shipping_region VARCHAR(255);
ALTER TABLE orders ADD COLUMN free_shipping_applied BOOLEAN DEFAULT FALSE;
ALTER TABLE orders ADD COLUMN withholding_tax_rate DECIMAL(5,2) DEFAULT 1.00;
ALTER TABLE orders ADD COLUMN withholding_tax_amount DECIMAL(10,2) DEFAULT 0;
ALTER TABLE orders ADD COLUMN seller_net_amount DECIMAL(10,2) DEFAULT 0;
ALTER TABLE orders ADD COLUMN platform_revenue DECIMAL(10,2) DEFAULT 0;
```

### order_items Tablosu (Eklenen Alanlar)
```sql
ALTER TABLE order_items ADD COLUMN withholding_tax_amount DECIMAL(10,2) DEFAULT 0;
```

### monthly_commissions Tablosu (Eklenen Alanlar)
```sql
ALTER TABLE monthly_commissions ADD COLUMN total_shipping_fees DECIMAL(10,2) DEFAULT 0;
ALTER TABLE monthly_commissions ADD COLUMN total_withholding_tax DECIMAL(10,2) DEFAULT 0;
ALTER TABLE monthly_commissions ADD COLUMN adjusted_net_commission DECIMAL(10,2) DEFAULT 0;
```

## 🔒 Yasal Uyumluluk

### Stopaj Beyanı

Platform yönetimi her ay stopaj beyanı yapmakla yükümlüdür:

1. Aylık stopaj raporunu indirin (`/api/admin/withholding-tax/export`)
2. Excel'e aktarın
3. Muhasebe departmanına gönderin
4. Vergi dairesine e-beyanname ile bildirin

### Stopaj Makbuzu

Her satıcıya stopaj makbuzu verilmelidir:

```
STOPAJ MAKBUZU
─────────────────────────────────
Satıcı: ABC Mağaza Ltd.
Vergi No: 1234567890
Dönem: Kasım 2025

Toplam Satış: ₺100,000
Stopaj Oranı: %1
Stopaj Tutarı: ₺1,000

Platform: SportOnline E-Ticaret A.Ş.
Vergi No: 9876543210
```

## 📱 Frontend Kullanımı

### Satıcı Paneli - Kargo Yönetimi
```vue
<template>
  <ShippingManagement />
</template>

<script>
import ShippingManagement from '@/components/seller/ShippingManagement.vue';

export default {
  components: { ShippingManagement }
}
</script>
```

### Özellikleri:
- ⚡ Hızlı kurulum (tüm bölgelere aynı ücret)
- 🗺️ Bölge bazlı özelleştirme
- 🧮 Kargo hesaplayıcı
- ✅ Ücretsiz kargo kampanyası
- 📊 Aktif/pasif durumu

## 🎯 Önemli Notlar

1. **Kargo ücreti satıcıdan kesilir:** Müşteri kargo ödese bile, bu tutar satıcının komisyonundan düşülür
2. **Stopaj zorunludur:** Yeni yasaya göre tüm e-ticaret platformları stopaj kesmek zorundadır
3. **Kurumsal avantaj:** Kurumsal satıcılar %1 stopaj öderken, bireysel satıcılar %2 öder
4. **Ücretsiz kargo kampanyası:** Satışları artırmak için ücretsiz kargo sunulabilir
5. **Bölgesel fiyatlandırma:** Her bölge için farklı kargo ücreti belirlenebilir

## 🚀 Gelecek Geliştirmeler

- [ ] Dinamik kargo firması entegrasyonu (Yurtiçi, Aras, MNG)
- [ ] Gerçek zamanlı kargo takibi
- [ ] Toplu kargo indirimleri
- [ ] Kargo sigortası
- [ ] Değişken stopaj oranları (kategori bazlı)
- [ ] Otomatik stopaj beyannamesi oluşturma
- [ ] Satıcı stopaj makbuzu PDF'i
