# Feature Flag Rehberi

Bu doküman, öncelik sırası, kullanılan anahtarlar ve override yöntemlerini özetler.

## Öncelik Sırası
1. Runtime override (localStorage): `feature:<flag>` → `"true" | "false"`
2. Ortam değişkeni: `VITE_FEATURE_<FLAG>` (nokta → altçizgi, büyük harf)
3. Kod içi varsayılanlar (`ENV_FLAGS` / reactive flags)

## Kullanım Kalıbı
- Basit toggles (noktalı anahtarlar): `import { isFeatureEnabled } from '@/utils/featureToggle'`
  - Örnek: `isFeatureEnabled('admin.services.hotels')`
- Gelişmiş/rol/rollout: `import { useFeatureFlags } from '@/services/featureFlags'`
  - Örnek: `useFeatureFlags().isEnabled('gemini_3_pro', userRole)`
- Admin menüsü ve homepage, noktalı anahtarları `featureToggle` üzerinden okur; `useFeatureFlags` bu anahtarları otomatik olarak helper’a delege eder.

## Tanımlı Anahtarlar (Basit)
- `homepage.experimentalWidgets` (varsayılan: false)
- `admin.observability` (varsayılan: true)
- Admin Hizmet menüsü (varsayılan: true):
  - `admin.services.restaurants`
  - `admin.services.foodOrders`
  - `admin.services.hotels`
  - `admin.services.reservations`
- Kampanya: `campaign.autoPublish` (varsayılan: true)
- Satıcı: `seller.newModule` (varsayılan: false)
- Ödeme: `payment.newFlow` (varsayılan: false)

## Tanımlı Anahtarlar (Gelişmiş)
- `gemini_3_pro` (rollout/role destekli)
- `new_buyer_dashboard`
- `admin_v2`
- `seller_analytics`
- `service_booking`
- `career_portal`
- `turbo_mode`
- `wallet_system`
- `ai_recommendations`
- `real_time_chat`

## Override Örnekleri
- localStorage (tarayıcı konsolu):
  - `localStorage.setItem('feature:admin.services.hotels', 'false')`
- .env: `VITE_FEATURE_ADMIN_SERVICES_HOTELS=false`

## Ekleme Rehberi
1. Noktalı anahtar ekle: `utils/featureToggle.ts` → `FeatureFlag` birliğine ve `ENV_FLAGS` varsayılanına ekle.
2. Gelişmiş/rollout anahtar ekle: `services/featureFlags.ts` içine tanımla.
3. Kullanım: Noktalı anahtarlar için `isFeatureEnabled`, diğerleri için `useFeatureFlags().isEnabled`.
4. UI şartı: Menü/section görünürlüğünü flag ile sar; yoksa varsayılan davranış (true/false) dokümana uygun olmalı.
