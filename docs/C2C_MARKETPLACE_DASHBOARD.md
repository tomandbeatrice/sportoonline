# 🏪 C2C Marketplace Dashboard Sistemi

## 📋 Genel Bakış

SportoOnline platformu için **Customer-to-Customer (C2C) Marketplace** modeline dönüşüm yapılmıştır. Sistem 3 farklı kullanıcı rolüne göre dinamik olarak dashboard sağlar:

- **Satıcı (Seller)**: Ürün yönetimi, sipariş işleme, satış analizi
- **Alıcı (Buyer)**: Alışveriş, sipariş takibi, favoriler
- **Platform Admin**: Satıcı onayları, anlaşmazlık çözümü, sistem yönetimi

## 🎯 Temel Özellikler

### 1. **Rol Tabanlı Dashboard**
Her kullanıcı rolü için özelleştirilmiş:
- Farklı istatistikler
- Rol-spesifik modüller
- Özelleştirilmiş iş akışları
- Hızlı erişim menüleri

### 2. **Dinamik Modül Sistemi**

#### Satıcı Modülleri:
- 📦 Ürün Yönetimi (Ürünlerim, Yeni Ürün, Stok)
- 🛒 Sipariş İşleme (Siparişlerim, Kargo Takip)
- 🎯 Pazarlama (Kampanyalar, İndirimler)
- 📈 Analitik (Satış Analizi, Müşteri İstatistikleri)
- ⚙️ Ayarlar (Mağaza, Değerlendirmeler, Mesajlar, Ödemeler)

#### Alıcı Modülleri:
- 🔍 Alışveriş (Ürün Ara, Favoriler, Sepet)
- 📦 Siparişler (Siparişlerim, Kargo Takibi)
- 👤 Hesap (Adresler, Ödeme Yöntemleri, Değerlendirmeler)
- ❓ Destek (Mesajlar, İade & Değişim, Yardım)

#### Admin Modülleri:
- 📊 Platform (Dashboard, Satıcı/Kullanıcı Yönetimi, Ürünler)
- ⚙️ Operasyonlar (Siparişler, Anlaşmazlıklar, İadeler)
- 🎯 Pazarlama (Kampanyalar, Banner, Promosyonlar)
- 📈 Analitik (Platform Analizi, Gelir Raporu)
- 🛡️ Sistem (Moderasyon, Yorumlar, Ödeme, Ayarlar)

### 3. **İş Akışları (Workflows)**

#### Satıcı İş Akışları:
- 🚀 Yeni Ürün Lansmanı
- 🏷️ Toplu İndirim
- 📦 Sipariş İşleme
- 📊 Stok Güncelleme

#### Alıcı İş Akışları:
- ⚡ Hızlı Sipariş
- ❤️ Favorilerden Sepete
- ↩️ İade Süreci

#### Admin İş Akışları:
- ✅ Satıcı Onay Süreci
- ⚖️ Anlaşmazlık Çözümü
- 🎯 Platform Kampanyası
- 🔍 Kalite Kontrolü
- 🛡️ Dolandırıcılık Tespiti

### 4. **Gerçek Zamanlı İstatistikler**

#### Satıcı Metrikleri:
- 💰 Toplam Kazanç
- 📦 Aktif Ürünler
- 🛒 Bekleyen Siparişler
- ⭐ Mağaza Puanı

#### Alıcı Metrikleri:
- 💳 Toplam Harcama
- 📦 Aktif Siparişler
- ❤️ Favoriler
- 🎁 Kazanılan Puanlar

#### Admin Metrikleri:
- 💰 Platform Geliri
- 👥 Toplam Satıcı
- 📊 Günlük İşlem
- 🎯 Aktif Kampanyalar

## 🔧 Teknik Altyapı

### Frontend

**Dosya:** `src/views/marketplace/C2CMarketplaceDashboard.vue`

**Özellikler:**
- Vue 3 Composition API
- TypeScript
- Reactive role-based UI
- Tailwind CSS styling
- Real-time data updates

**Ana Bileşenler:**
```typescript
- currentRole: ref<UserRole> // 'seller' | 'buyer' | 'admin'
- stats: computed() // Role-based statistics
- modules: computed() // Role-based modules
- workflows: computed() // Role-based workflows
- quickActions: computed() // Role-based quick actions
```

### Backend

**Dosya:** `app/Http/Controllers/Api/C2CDashboardController.php`

**Endpoint'ler:**
```php
GET  /api/c2c/dashboard           // Role-based dashboard data
POST /api/c2c/workflow            // Execute workflow
POST /api/c2c/quick-action        // Execute quick action
```

**Metodlar:**
- `getSellerDashboard()` - Satıcı verilerini getirir
- `getBuyerDashboard()` - Alıcı verilerini getirir
- `getAdminDashboard()` - Admin verilerini getirir
- `executeWorkflow()` - İş akışı çalıştırır
- `quickAction()` - Hızlı aksiyonları yürütür

### Service Layer

**Dosya:** `src/services/c2cMarketplace.ts`

**Fonksiyonlar:**
```typescript
getDashboardData()              // Dashboard verisini çek
executeWorkflow()               // İş akışı çalıştır
executeQuickAction()            // Hızlı aksiyon
getSellerProductPerformance()   // Satıcı ürün performansı
getBuyerOrders()                // Alıcı siparişleri
getAdminPendingApprovals()      // Admin onay bekleyenler
getAdminDisputes()              // Admin anlaşmazlıklar
```

## 📊 Veritabanı Yapısı

### Gerekli Tablolar

```sql
-- Users (role column: 'seller', 'buyer', 'admin')
users: id, name, email, role, status, loyalty_points, last_activity_at

-- Products
products: id, seller_id, name, image, price, stock, status, approval_status

-- Orders
orders: id, user_id, status, total_amount, commission_amount, created_at

-- Order Items
order_items: id, order_id, product_id, quantity, subtotal

-- Reviews
reviews: id, product_id, user_id, rating, comment

-- Favorites
favorites: id, user_id, product_id

-- Seller Applications
seller_applications: id, user_id, status, created_at

-- Disputes
disputes: id, order_id, issue, status, created_at

-- Refund Requests
refund_requests: id, order_id, status, amount

-- Campaigns
campaigns: id, type, status, start_date, end_date
```

## 🚀 Kullanım

### 1. Dashboard'a Erişim

```typescript
// Router
router.push('/dashboard')

// Otomatik olarak kullanıcı rolüne göre içerik gösterilir
```

### 2. Rol Değiştirme (Demo Mode)

```vue
<!-- Component içinde -->
<select v-model="currentRole">
  <option value="seller">Satıcı</option>
  <option value="buyer">Alıcı</option>
  <option value="admin">Platform Admin</option>
</select>
```

### 3. İş Akışı Çalıştırma

```typescript
const executeWorkflow = async (workflow) => {
  try {
    const result = await c2cService.executeWorkflow({
      workflow_id: workflow.id,
      params: { /* workflow parameters */ }
    })
    console.log('Workflow result:', result)
  } catch (error) {
    console.error('Workflow failed:', error)
  }
}
```

### 4. Hızlı Aksiyon

```typescript
const executeQuickAction = async (action) => {
  try {
    const result = await c2cService.executeQuickAction({
      action_id: action.id
    })
    
    if (result.redirect) {
      router.push(result.redirect)
    }
  } catch (error) {
    console.error('Quick action failed:', error)
  }
}
```

## 🔐 Güvenlik

### Middleware
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('c2c')->group(function () {
        Route::get('/dashboard', [C2CDashboardController::class, 'index']);
    });
});
```

### Role Kontrolü
```php
// Controller içinde
$user = Auth::user();
$role = $user->role;

if ($role !== 'admin') {
    return response()->json(['error' => 'Unauthorized'], 403);
}
```

## 📱 Responsive Design

Dashboard tamamen responsive:
- Mobile: 1 kolon grid
- Tablet: 2 kolon grid
- Desktop: 3-4 kolon grid

```vue
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
  <!-- Stats cards -->
</div>
```

## 🎨 Stil Kılavuzu

### Rol Badge Colors
- **Satıcı**: Blue (`bg-blue-100 text-blue-700`)
- **Alıcı**: Green (`bg-green-100 text-green-700`)
- **Admin**: Purple (`bg-purple-100 text-purple-700`)

### Modül Card Colors
- **Blue**: Ürün/Alışveriş modülleri
- **Green**: Sipariş modülleri
- **Purple**: Pazarlama modülleri
- **Orange**: Analitik modülleri
- **Red**: Ayarlar/Sistem modülleri

## 🔄 Veri Akışı

```
User Login → Auth Store → Dashboard Mount
     ↓
API Call: /api/c2c/dashboard
     ↓
Backend: Detect User Role
     ↓
Return Role-Specific Data
     ↓
Frontend: Update Reactive Refs
     ↓
UI: Render Role-Based Components
```

## 📈 Genişletme Noktaları

### 1. Yeni Rol Ekleme
```typescript
// Add to UserRole type
type UserRole = 'seller' | 'buyer' | 'admin' | 'moderator'

// Add computed properties
const isModerator = computed(() => currentRole.value === 'moderator')
```

### 2. Yeni Modül Ekleme
```typescript
// In modules computed
if (isSeller.value) {
  return [
    ...existingModules,
    { id: 'analytics-v2', name: 'Gelişmiş Analitik', ... }
  ]
}
```

### 3. Yeni İş Akışı Ekleme
```typescript
// In workflows computed
workflows.value.push({
  id: 'bulk-update',
  name: 'Toplu Ürün Güncelleme',
  steps: ['Ürün Seç', 'Değişiklikleri Belirle', 'Önizle', 'Uygula'],
  icon: '🔄'
})
```

## 🧪 Test Etme

### 1. Local Development
```bash
# Ana sayfa > C2C Dashboard butonu
http://localhost:5173/ → C2C Dashboard

# Veya direkt
http://localhost:5173/dashboard
```

### 2. Rol Değiştirme
Dashboard'da üst menüden rol değiştirin ve içeriğin dinamik olarak değiştiğini gözlemleyin.

### 3. API Test
```bash
# Dashboard data
curl -H "Authorization: Bearer TOKEN" \
  http://localhost:8000/api/c2c/dashboard

# Workflow
curl -X POST -H "Authorization: Bearer TOKEN" \
  -d '{"workflow_id":"product-launch"}' \
  http://localhost:8000/api/c2c/workflow
```

## 📚 Bağımlılıklar

### Frontend
- Vue 3
- TypeScript
- Vue Router
- Axios
- Tailwind CSS

### Backend
- Laravel 10+
- Laravel Sanctum (Auth)
- MySQL/PostgreSQL

## 🎯 Gelecek Geliştirmeler

1. **Real-time Updates**: WebSocket ile canlı bildirimler
2. **Advanced Analytics**: Grafik ve chart'lar
3. **AI Recommendations**: ML tabanlı ürün önerileri
4. **Multi-language**: i18n desteği
5. **Dark Mode**: Tema değiştirme
6. **Mobile App**: React Native versiyonu
7. **Seller Tier System**: Bronz/Gümüş/Altın satıcı seviyeleri
8. **Gamification**: Rozet ve başarım sistemi

## 🐛 Bilinen Sorunlar

- [ ] Mock data hala kullanılıyor (API entegrasyonu devam ediyor)
- [ ] Bazı iş akışları henüz implement edilmedi
- [ ] Admin approval sistemi backend'de eksik

## 👨‍💻 Geliştirici Notları

### Environment Variables
```env
VITE_API_BASE_URL=http://localhost:8000
VITE_C2C_DEMO_MODE=true  # Demo için rol değiştirebilme
```

### Debug Mode
```typescript
// Component içinde
console.log('Current role:', currentRole.value)
console.log('Stats:', stats.value)
console.log('Modules:', modules.value)
```

## 📞 Destek

Sorularınız için:
- GitHub Issues
- Email: dev@sportoonline.com
- Slack: #c2c-marketplace

---

**Son Güncelleme:** 19 Kasım 2025  
**Versiyon:** 1.0.0  
**Yazar:** SportoOnline Dev Team
