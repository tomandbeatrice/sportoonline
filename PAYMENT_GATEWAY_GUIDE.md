# Ödeme Sistemi Kullanım Kılavuzu

## 🎯 Genel Bakış

Sistemde 4 farklı ödeme gateway'i yapılandırılmıştır:
- **Iyzico** - Türkiye'nin önde gelen ödeme gateway'i
- **PayTR** - Yerli ödeme çözümü
- **MokaPOS** - Akbank sanal POS
- **ZiraatPay** - Ziraat Bankası sanal POS

Her gateway aktif/pasif edilebilir, test/prod modunda çalışabilir.

---

## 📋 Admin Panel Kullanımı

### Gateway Yönetimi
```
Admin Dashboard → Payment Gateway Manager
```

**Yapılabilecekler:**
1. Gateway'leri aktif/pasif yapma
2. Test/Production modu değiştirme
3. API credentials yapılandırma
4. Komisyon oranları belirleme
5. Min/Max tutar limitleri ayarlama
6. İstatistikleri görüntüleme

---

## 🔧 Gateway Yapılandırma

### Iyzico
```php
// Admin panelinden girilecek bilgiler:
- API Key: sandbox-XXXXX (test) / XXXXX (prod)
- Secret Key: sandbox-YYYYY (test) / YYYYY (prod)
- Komisyon Oranı: 2.5%
- Sabit Komisyon: ₺0.25
```

### PayTR
```php
- Merchant ID: XXXXX
- Merchant Key: YYYYY
- Merchant Salt: ZZZZZ
- Komisyon Oranı: 2.0%
```

### MokaPOS
```php
- Dealer Code: XXXXX
- Username: YYYYY
- Password: ZZZZZ
- Checkout Key: AAAAA
- Komisyon Oranı: 2.3%
```

### ZiraatPay
```php
- Client ID: XXXXX
- Store Key: YYYYY
- API Username: ZZZZZ
- API Password: AAAAA
- Komisyon Oranı: 2.2%
```

---

## 💻 Frontend Kullanımı

### 1. Mevcut Gateway'leri Listeleme
```javascript
const { data } = await axios.get('/api/payment/gateways')

// Response:
{
  "success": true,
  "gateways": [
    {
      "name": "iyzico",
      "display_name": "Iyzico",
      "description": "Türkiye'nin önde gelen ödeme gateway'i",
      "is_test_mode": true,
      "min_amount": 1.00,
      "max_amount": 100000.00
    }
  ]
}
```

### 2. Ödeme Başlatma
```javascript
const initiatePayment = async (orderId, gatewayName) => {
  const { data } = await axios.post('/api/payment/initiate', {
    order_id: orderId,
    gateway: gatewayName, // 'iyzico', 'paytr', 'mokapos', 'ziraatpay'
    customer_data: {
      first_name: 'Ahmet',
      last_name: 'Yılmaz',
      email: 'ahmet@example.com',
      phone: '05551234567',
      identity_number: '11111111111',
      address: 'Ankara, Türkiye',
      city: 'Ankara',
      country: 'Turkey',
      zip_code: '06100',
      ip: '127.0.0.1'
    }
  })

  if (data.success) {
    // Kullanıcıyı ödeme sayfasına yönlendir
    window.location.href = data.data.payment_page_url
  }
}
```

### 3. Vue Component Örneği
```vue
<template>
  <div class="payment-selector">
    <h3>Ödeme Yöntemi Seçin</h3>
    
    <div class="gateways">
      <div 
        v-for="gateway in gateways" 
        :key="gateway.name"
        @click="selectGateway(gateway.name)"
        class="gateway-card"
      >
        <h4>{{ gateway.display_name }}</h4>
        <p>{{ gateway.description }}</p>
        <span v-if="gateway.is_test_mode" class="badge">Test Modu</span>
      </div>
    </div>

    <button @click="processPayment" :disabled="!selectedGateway">
      Ödeme Yap
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const gateways = ref([])
const selectedGateway = ref(null)
const orderId = ref(1) // Sipariş ID'si

const loadGateways = async () => {
  const { data } = await axios.get('/api/payment/gateways')
  gateways.value = data.gateways
}

const selectGateway = (gatewayName) => {
  selectedGateway.value = gatewayName
}

const processPayment = async () => {
  const { data } = await axios.post('/api/payment/initiate', {
    order_id: orderId.value,
    gateway: selectedGateway.value,
    customer_data: {
      email: 'user@example.com',
      phone: '05551234567',
      // ... diğer bilgiler
    }
  })

  if (data.success) {
    window.location.href = data.data.payment_page_url
  }
}

onMounted(() => {
  loadGateways()
})
</script>
```

---

## 🔄 Callback & Webhook İşleme

**Callback URL'leri (otomatik tanımlı):**
- Iyzico: `/api/payment/callback/iyzico`
- PayTR: `/api/payment/callback/paytr`
- MokaPOS: `/api/payment/callback/mokapos`
- ZiraatPay: `/api/payment/callback/ziraatpay`

**Fail URL'leri:**
- PayTR: `/api/payment/fail/paytr`
- ZiraatPay: `/api/payment/fail/ziraatpay`

Sistemde callback'ler otomatik işlenir:
1. Gateway'den gelen veri doğrulanır
2. Sipariş durumu güncellenir (payment_status: 'paid')
3. PaymentTransaction kaydı oluşturulur
4. PaymentReceived event tetiklenir (bildirim gönderilir)
5. Kullanıcı success/fail sayfasına yönlendirilir

---

## 💸 İade İşlemi

```javascript
const refundPayment = async (transactionId, amount, reason) => {
  const { data } = await axios.post('/api/payment/refund', {
    transaction_id: transactionId,
    amount: amount,
    reason: reason // optional
  })

  if (data.success) {
    console.log('İade ID:', data.refund_id)
  }
}

// Kullanım:
refundPayment(123, 100.50, 'Müşteri talebi')
```

---

## 📊 Database Yapısı

### payment_gateways
```sql
- id
- name (unique: iyzico, paytr, mokapos, ziraatpay)
- provider (class adı)
- display_name
- credentials (JSON)
- is_active
- is_test_mode
- min_amount, max_amount
- commission_rate, fixed_commission
```

### payment_transactions
```sql
- id
- order_id
- payment_gateway_id
- transaction_id
- conversation_id
- status (pending, processing, success, failed, refunded)
- amount
- request_data, response_data (JSON)
- paid_at, refunded_at
```

---

## 🧪 Test Ortamı

1. **Admin Panel'den Gateway Ayarları:**
   - Test modunu aktif et
   - Test credentials gir

2. **Test Kartları:**
   
   **Iyzico Sandbox:**
   - Kart: 5528 7900 0000 0001
   - CVV: 123
   - Tarih: 12/30

   **PayTR Test:**
   - Test modu otomatik aktif

   **MokaPOS Test:**
   - Test URL kullanılır

3. **Test Siparişi:**
```bash
# Test order oluştur
POST /api/orders
{
  "items": [...],
  "total": 100.00
}

# Ödeme başlat
POST /api/payment/initiate
{
  "order_id": 1,
  "gateway": "iyzico"
}
```

---

## 🔐 Güvenlik

1. **Credentials:** Admin panel üzerinden şifrelenmiş saklanır
2. **Hash Doğrulama:** Tüm callback'lerde hash verify yapılır
3. **HTTPS:** Production'da zorunlu
4. **IP Whitelist:** Gateway ayarlarından yapılandırılabilir
5. **Rate Limiting:** API endpoint'lerde aktif

---

## 📝 Log Dosyaları

```bash
storage/logs/payment.log
```

Loglama seviyeleri:
- `info`: Normal işlemler
- `error`: Hata durumları
- `warning`: Dikkat gerektiren durumlar

---

## 🚀 Production'a Geçiş

1. Admin panelden gateway'leri test modundan çıkar
2. Production credentials gir
3. Gateway'i aktif et
4. Test ödeme yap
5. Callback URL'lerini gateway admin panellerinde ayarla
6. SSL sertifikası kontrol et
7. Rate limit ayarla
8. Monitoring aktif et

---

## ❓ Sık Sorulan Sorular

**S: Birden fazla gateway aynı anda aktif olabilir mi?**
C: Evet, kullanıcı checkout sayfasında seçim yapabilir.

**S: Komisyon oranları nasıl hesaplanır?**
C: `(amount * commission_rate / 100) + fixed_commission`

**S: Webhook'lar nasıl test edilir?**
C: Ngrok veya LocalTunnel ile local URL'i public yap.

**S: İade süresi var mı?**
C: Gateway'e bağlı (genelde 180-365 gün).

**S: Taksit desteği var mı?**
C: Evet, her gateway'de max_installment parametresi ile ayarlanabilir.
