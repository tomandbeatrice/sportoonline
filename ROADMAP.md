# 🗺️ Proje Geliştirme Yol Haritası (Roadmap)

Bu belge, sistem analizine dayanarak belirlenen eksiklikleri ve geliştirme önerilerini içerir.

## 1. 🏗️ Mimari ve Kod Yapısı (Architecture)
- [ ] **Modüler Yapıya Tam Geçiş:** `app/Http/Controllers` altındaki dağınık yapının tamamen `app/Modules/` (Ecommerce, RideShare vb.) altına taşınması.
- [ ] **Laravel 11 Yükseltmesi:** Kod sadeleştirme ve güncel özelliklerden faydalanmak için upgrade işlemi.
- [ ] **Domain Driven Design (DDD):** İş mantığının servislerden domain katmanına aktarılması.

## 2. 🚀 Yeni Özellikler (Features)
- [ ] **Cüzdan (Wallet) Sistemi:**
  - Satıcı ve alıcı bakiyeleri.
  - Escrow (Güvenli ödeme) mekanizması.
- [ ] **Mobil Deneyim:**
  - Ionic veya Capacitor ile PWA'nın mobile çevrilmesi.
  - Offline mode desteği.
- [ ] **Gelişmiş Mesajlaşma:**
  - WebSocket tabanlı gerçek zamanlı sohbet.
  - Medya paylaşımı.

## 3. ⚡ Altyapı ve Performans
- [ ] **Gelişmiş Arama (Smart Search):**
  - Elasticsearch veya Meilisearch entegrasyonu.
  - Typo-tolerance ve hızlı filtreleme.
- [ ] **Observability (İzlenebilirlik):**
  - Sentry entegrasyonu (Hata takibi).
  - Laravel Telescope kurulumu.

## 4. 🔐 Güvenlik (Security)
- [ ] **2FA (İki Faktörlü Doğrulama):** Admin ve Satıcılar için Google Authenticator desteği.
- [ ] **RBAC (Rol Tabanlı Yetkilendirme):** Spatie Permission ile detaylı izin yönetimi.

---
*Son Güncelleme: 2025-12-02*