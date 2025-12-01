# Turbo Mod - Aylık Yarışma Sistemi

## 📋 Sistem Özeti

Turbo Mod, müşteriler ve satıcılar arasında aylık bazda düzenlenen bir rekabet sistemidir. Katılımcılar, alışveriş/satış performanslarına göre sıralanır ve her ayın sonunda ilk 3 sıradaki kazananlara para, puan ve özel kuponlar dağıtılır.

## 🎯 Özellikler

### Müşteri Yarışması
- **Sıralama Kriteri:** Aylık toplam alışveriş tutarı
- **Ödüller:**
  - 🥇 1. Sıra: ₺1,000 + 5,000 Turbo Puan
  - 🥈 2. Sıra: ₺500 + 3,000 Turbo Puan
  - 🥉 3. Sıra: ₺250 + 2,000 Turbo Puan

### Satıcı Yarışması
- **Sıralama Kriteri:** Aylık toplam satış geliri
- **Ödüller:**
  - 🥇 1. Sıra: ₺2,000 + 10,000 Turbo Puan + %10 Komisyon İndirimi Kuponu
  - 🥈 2. Sıra: ₺1,000 + 6,000 Turbo Puan + %7 Komisyon İndirimi Kuponu
  - 🥉 3. Sıra: ₺500 + 4,000 Turbo Puan + %5 Komisyon İndirimi Kuponu

### Kupon Sistemi
- Satıcı kazananlar otomatik olarak komisyon indirimi kuponu alır
- Kuponlar **30 gün** geçerlidir
- Her kupon **10 kez** kullanılabilir
- Kupon kodu kopyalanıp satıcı tarafından sipariş işlemleri sırasında kullanılabilir
- Kupon şartları özelleştirilebilir (minimum sipariş tutarı, geçerlilik süresi)

## 🗄️ Veritabanı Yapısı

### turbo_competitions
Aylık yarışma dönemlerini saklar.

```sql
id                  bigint (PK)
year                integer
month               integer
start_date          date
end_date            date
status              enum('active', 'ended', 'finalized')
created_at          timestamp
updated_at          timestamp

UNIQUE(year, month)
INDEX(status)
```

**Status Durumları:**
- `active`: Yarışma devam ediyor
- `ended`: Yarışma süresi doldu, henüz finalize edilmedi
- `finalized`: Kazananlar belirlendi, ödüller dağıtıldı

### turbo_winners
Yarışma kazananlarını ve ödüllerini saklar.

```sql
id                  bigint (PK)
competition_id      bigint (FK → turbo_competitions)
category            enum('customer', 'seller')
user_id             bigint (FK → users)
rank                integer (1, 2, 3)
total_amount        decimal(12,2) -- Harcama veya gelir
reward_money        decimal(10,2)
reward_points       integer
coupon_code         string (nullable)
created_at          timestamp
updated_at          timestamp

UNIQUE(competition_id, category, user_id)
INDEX(category, rank)
```

### turbo_coupons
Satıcı kuponlarını yönetir.

```sql
id                              bigint (PK)
winner_id                       bigint (FK → turbo_winners)
seller_id                       bigint (FK → users)
code                            string UNIQUE
commission_discount_percent     decimal(5,2)
min_order_amount                decimal(10,2)
max_uses                        integer
used_count                      integer
valid_from                      date
valid_until                     date
is_active                       boolean
conditions                      json (nullable)
created_at                      timestamp
updated_at                      timestamp

INDEX(seller_id)
INDEX(code, is_active)
```

**Conditions JSON Örneği:**
```json
{
  "description": "1. Sıra Turbo Mod Ödülü",
  "competition_month": 11,
  "competition_year": 2025
}
```

### turbo_coupon_usage
Kupon kullanım geçmişini saklar.

```sql
id                      bigint (PK)
coupon_id               bigint (FK → turbo_coupons)
order_id                bigint (FK → orders)
original_commission     decimal(10,2)
discounted_commission   decimal(10,2)
savings                 decimal(10,2)
created_at              timestamp
updated_at              timestamp
```

### users table extension
Kullanıcılar tablosuna eklenen alan:

```sql
turbo_points            integer DEFAULT 0
```

## 🔄 Yarışma Döngüsü

### 1. Yarışma Başlangıcı
```php
// Her ayın 1'inde otomatik olarak yeni yarışma oluşturulur
TurboCompetition::createForMonth(2025, 11);
```

### 2. Aylık Süreç
- Müşteriler alışveriş yapar → Toplam harcama hesaplanır
- Satıcılar ürün satar → Toplam gelir hesaplanır
- Sıralamalar gerçek zamanlı güncellenir
- Anasayfada canlı liderlik tablosu gösterilir

### 3. Ay Sonu (Her Ayın 1'inde 01:00)
```bash
php artisan turbo:finalize
```

**Otomatik İşlemler:**
1. Geçen ayın yarışması `ended` → `finalized` durumuna geçer
2. İlk 3 müşteri belirlenir
3. İlk 3 satıcı belirlenir
4. `turbo_winners` tablosuna kazananlar kaydedilir
5. Ödüller dağıtılır:
   - Para → `users.wallet_balance` artırılır
   - Puan → `users.turbo_points` artırılır
   - Kupon → `turbo_coupons` tablosuna eklenir
6. Yeni ay için yarışma oluşturulur

## 📡 API Endpoints

### Public Endpoints (Kimlik Doğrulama Gerektirmez)

#### GET /api/turbo/current
Güncel yarışma istatistikleri ve liderlik tabloları.

**Response:**
```json
{
  "success": true,
  "data": {
    "current_competition": {
      "id": 1,
      "month": 11,
      "year": 2025,
      "start_date": "2025-11-01",
      "end_date": "2025-11-30",
      "days_remaining": 12,
      "status": "active"
    },
    "top_customers": [
      {
        "id": 5,
        "name": "Ahmet Yılmaz",
        "email": "ahmet@example.com",
        "total_spending": "15000.00",
        "order_count": 12,
        "turbo_points": 3500
      }
    ],
    "top_sellers": [
      {
        "id": 3,
        "name": "Mehmet Satıcı",
        "email": "mehmet@example.com",
        "total_revenue": "85000.00",
        "order_count": 45,
        "turbo_points": 8200
      }
    ],
    "total_active_coupons": 15,
    "total_competitions": 6
  }
}
```

#### GET /api/turbo/leaderboard/customers?limit=5
Müşteri liderlik tablosu (ilk 5).

#### GET /api/turbo/leaderboard/sellers?limit=5
Satıcı liderlik tablosu (ilk 5).

#### GET /api/turbo/history?limit=12
Geçmiş yarışmalar ve kazananlar.

### Authenticated Endpoints (Token Gerektirir)

#### GET /api/turbo/my-position
Kullanıcının mevcut sıralaması.

**Response:**
```json
{
  "success": true,
  "data": {
    "rank": 7,
    "user": {
      "id": 12,
      "name": "Ali Demir",
      "total_spending": "8500.00",
      "order_count": 8
    },
    "total": "8500.00",
    "order_count": 8,
    "turbo_points": 1200
  }
}
```

#### GET /api/turbo/my-winnings?limit=10
Kullanıcının kazanma geçmişi.

### Seller Coupon Management

#### GET /api/seller/turbo-coupons
Satıcının kuponlarını listele.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "code": "TURBO8XJ3K2LP",
      "commission_discount_percent": "10.00",
      "min_order_amount": "0.00",
      "max_uses": 10,
      "used_count": 3,
      "remaining_uses": 7,
      "valid_from": "2025-11-01",
      "valid_until": "2025-12-01",
      "is_active": true,
      "status_label": "Aktif",
      "is_expired": false,
      "total_savings": "450.00",
      "competition": {
        "month": 10,
        "year": 2025,
        "rank": 1,
        "rank_badge": "🥇"
      }
    }
  ]
}
```

#### POST /api/seller/turbo-coupons/{id}/toggle
Kupon aktif/pasif durumunu değiştir.

#### GET /api/seller/turbo-coupons/{id}/usage
Kupon kullanım detayları.

#### POST /api/seller/turbo-coupons/validate
Kupon geçerliliğini kontrol et.

**Request:**
```json
{
  "code": "TURBO8XJ3K2LP",
  "order_amount": 1500
}
```

**Response:**
```json
{
  "success": true,
  "message": "Kupon geçerli.",
  "data": {
    "code": "TURBO8XJ3K2LP",
    "commission_discount_percent": "10.00",
    "remaining_uses": 7,
    "valid_until": "2025-12-01"
  }
}
```

## 🎨 Frontend Bileşenleri

### TurboMode.vue (Anasayfa Paneli)
**Konum:** `src/components/home/TurboMode.vue`

**Özellikler:**
- Yarışma geri sayım sayacı
- İki sütunlu liderlik tablosu (müşteri/satıcı)
- Podyum görünümü (1., 2., 3. sıralar özel tasarım)
- 4. ve 5. sıralar liste görünümü
- Kullanıcının kendi sıralaması (giriş yaptıysa)
- İlerleme çubuğu (ilk 3'e girme hedefi)
- Yarışma kuralları açıklaması
- Ödül badge'leri
- Responsive tasarım

**Kullanım:**
```vue
<template>
  <div class="home-page">
    <TurboMode />
    <!-- Diğer anasayfa içeriği -->
  </div>
</template>

<script>
import TurboMode from '@/components/home/TurboMode.vue';

export default {
  components: { TurboMode }
};
</script>
```

### TurboWinners.vue (Admin Paneli)
**Konum:** `src/components/admin/TurboWinners.vue`

**Özellikler:**
- Ay seçici (geçmiş ayları görüntüleme)
- Kazananları görüntüleme
- Ödül tutarlarını düzenleme (para/puan)
- Kupon kodlarını kopyalama
- Yarışma geçmişi istatistikleri
- Toplam dağıtılan ödüller
- Aktif kupon sayısı

## 🛠️ Servisler

### TurboCompetitionService

#### getCurrentCompetition()
Aktif yarışmayı döndürür. Yoksa oluşturur.

#### getCustomerLeaderboard($limit = 5)
Müşteri sıralamasını getirir.

#### getSellerLeaderboard($limit = 5)
Satıcı sıralamasını getirir.

#### getUserPosition($userId)
Kullanıcının sırasını ve istatistiklerini döndürür.

#### finalizeCompetition($competitionId = null)
Yarışmayı sonlandırır ve ödülleri dağıtır.

**Süreç:**
```php
DB::transaction(function() {
    // 1. İlk 3 müşteriyi bul
    $topCustomers = $competition->getTopCustomers(3);
    
    // 2. İlk 3 satıcıyı bul
    $topSellers = $competition->getTopSellers(3);
    
    // 3. Kazanan kayıtları oluştur
    foreach ($topCustomers as $index => $customer) {
        TurboWinner::create([
            'competition_id' => $competition->id,
            'category' => 'customer',
            'user_id' => $customer->id,
            'rank' => $index + 1,
            'total_amount' => $customer->total_spending
        ]);
    }
    
    // 4. Ödülleri dağıt
    $this->distributeRewards($competition);
    
    // 5. Durumu güncelle
    $competition->update(['status' => 'finalized']);
});
```

#### checkAndFinalizeEndedCompetitions()
Süresi dolan yarışmaları kontrol edip sonlandırır.

#### applyCoupon($couponCode, $order)
Kuponu siparişe uygular ve komisyon indirimini hesaplar.

**Örnek:**
```php
$result = $turboService->applyCoupon('TURBO8XJ3K2LP', $order);

// Result:
[
    'success' => true,
    'original_commission' => 120.00,
    'discounted_commission' => 108.00,
    'savings' => 12.00,
    'message' => 'Turbo kupon uygulandı! ₺12.00 komisyon indirimi.'
]
```

## ⚙️ Scheduled Job Kurulumu

### Console Kernel
**Dosya:** `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule): void
{
    // Her ayın 1'inde 01:00'da yarışmayı sonlandır
    $schedule->command('turbo:finalize')->monthlyOn(1, '01:00');
}
```

### Cron Ayarı
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Manuel Çalıştırma
```bash
# Yarışmayı manuel olarak sonlandır
php artisan turbo:finalize

# Scheduler'ı test et
php artisan schedule:run
```

## 💡 Kullanım Senaryoları

### Senaryo 1: Müşteri Yarışmaya Katılıyor
1. Müşteri Kasım ayında ₺15,000 alışveriş yapar
2. Sistem otomatik olarak toplamı hesaplar
3. Liderlik tablosunda 2. sıraya yükselir
4. Anasayfada sıralama güncellenir
5. Kendi pozisyonunu görebilir

### Senaryo 2: Satıcı Ödül Kazanıyor
1. Satıcı Kasım ayında ₺85,000 satış yapar
2. Ay sonunda 1. sırada bitirir
3. 1 Aralık'ta otomatik finalizasyon:
   - Cüzdana ₺2,000 eklenir
   - Turbo puanı 10,000 artar
   - %10 indirim kuponu oluşturulur
4. Satıcı kupon kodunu kopyalar: `TURBO8XJ3K2LP`
5. Aralık ayında siparişlerinde kullanır

### Senaryo 3: Kupon Kullanımı
1. Satıcı sipariş alır (komisyon: ₺120)
2. Kupon kodunu uygular: `TURBO8XJ3K2LP`
3. Sistem kuponu doğrular:
   - Geçerlilik tarihi ✓
   - Kullanım limiti (7/10 kalmış) ✓
   - Minimum tutar ✓
4. Komisyon %10 indirimli hesaplanır:
   - Orijinal: ₺120
   - İndirimli: ₺108
   - Kazanç: ₺12
5. Kupon kullanım sayısı 1 artar
6. `turbo_coupon_usage` tablosuna kayıt düşer

## 🎯 Ödül Yapısı

### Müşteri Ödülleri
| Sıra | Para | Turbo Puan | Toplam Değer |
|------|------|------------|--------------|
| 🥇 1 | ₺1,000 | 5,000 | ~₺1,500 |
| 🥈 2 | ₺500 | 3,000 | ~₺800 |
| 🥉 3 | ₺250 | 2,000 | ~₺450 |

### Satıcı Ödülleri
| Sıra | Para | Turbo Puan | Kupon İndirimi | Toplam Değer |
|------|------|------------|----------------|--------------|
| 🥇 1 | ₺2,000 | 10,000 | %10 (10 kullanım) | ~₺4,000+ |
| 🥈 2 | ₺1,000 | 6,000 | %7 (10 kullanım) | ~₺2,500+ |
| 🥉 3 | ₺500 | 4,000 | %5 (10 kullanım) | ~₺1,500+ |

**Not:** Kupon değeri satıcının gelecekteki satışlarına bağlıdır. Örnek: 10 adet ₺1,000 komisyonlu satışta %10 kupon = ₺1,000 tasarruf.

## 🔒 Güvenlik

### Kupon Doğrulama
```php
public function isValid($orderAmount = null)
{
    // Aktiflik kontrolü
    if (!$this->is_active) {
        return ['valid' => false, 'message' => 'Kupon aktif değil.'];
    }
    
    // Tarih kontrolü
    if (now()->lt($this->valid_from)) {
        return ['valid' => false, 'message' => 'Kupon henüz geçerli değil.'];
    }
    
    if (now()->gt($this->valid_until)) {
        return ['valid' => false, 'message' => 'Kupon süresi dolmuş.'];
    }
    
    // Kullanım limiti
    if ($this->used_count >= $this->max_uses) {
        return ['valid' => false, 'message' => 'Kupon kullanım limiti dolmuş.'];
    }
    
    // Minimum tutar
    if ($orderAmount !== null && $orderAmount < $this->min_order_amount) {
        return [
            'valid' => false,
            'message' => sprintf('Minimum sipariş tutarı: ₺%s', number_format($this->min_order_amount, 2))
        ];
    }
    
    return ['valid' => true];
}
```

### Sahiplik Kontrolü
```php
// Kupon sadece sahibi tarafından kullanılabilir
if ($order->seller_id !== $coupon->seller_id) {
    return [
        'success' => false,
        'message' => 'Bu kupon sadece kodu alan satıcının siparişlerinde geçerlidir.'
    ];
}
```

## 📊 İstatistikler ve Raporlama

### Admin Dashboard Metrikleri
- Toplam tamamlanan yarışma sayısı
- Dağıtılan toplam para ödülleri
- Dağıtılan toplam Turbo puanları
- Aktif kupon sayısı
- Aylık katılımcı sayısı

### Kullanıcı Metrikleri
- Kazanma geçmişi
- Toplam kazanılan para
- Toplam Turbo puanı
- Kupon kullanım istatistikleri
- Aylık sıralama trendleri

## 🚀 Gelecek Geliştirmeler

### Planlanan Özellikler
1. **Haftalık Yarışmalar:** Aylık yerine haftalık mini yarışmalar
2. **Kategori Bazlı Yarışmalar:** Belirli kategorilerde özel yarışmalar
3. **Takım Yarışmaları:** Grup halinde yarışma
4. **Rozet Sistemi:** Başarı rozetleri
5. **Liderlik Değişim Bildirimleri:** Sıralamada değişiklik olduğunda bildirim
6. **Sosyal Paylaşım:** Kazananların sosyal medyada paylaşım yapabilmesi
7. **Dinamik Ödüller:** Admin tarafından ödül yapısını özelleştirme
8. **Bölgesel Yarışmalar:** Şehir/bölge bazlı yarışmalar

## 📞 Destek

Turbo Mod sistemi hakkında sorularınız için:
- Teknik destek: dev@sportoonline.com
- Dokümantasyon: /docs/turbo-mode
- API referansı: /api/documentation

## 📝 Changelog

### v1.0.0 (2025-11-19)
- ✅ İlk sürüm
- ✅ Aylık yarışma sistemi
- ✅ Müşteri ve satıcı liderlik tabloları
- ✅ Otomatik ödül dağıtımı
- ✅ Kupon sistemi
- ✅ Anasayfa paneli
- ✅ Admin yönetim paneli
- ✅ Scheduled job (aylık finalizasyon)
- ✅ API endpoints (10+ endpoint)
- ✅ Responsive frontend bileşenleri
