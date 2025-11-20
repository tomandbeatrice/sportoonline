<template>
  <aside class="admin-sidebar">
    <div class="sidebar-header">
      <h2><BadgeIcon name="flag" cls="inline w-5 h-5 mr-2" /> Admin Panel</h2>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section">
        <div class="nav-section-title">Ana Menü</div>
        <router-link to="/admin/dashboard" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="chart" cls="w-5 h-5" /></span>
          <span class="nav-label">Dashboard</span>
        </router-link>
        <router-link to="/admin/dashboard-new" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="target" cls="w-5 h-5" /></span>
          <span class="nav-label">Gelişmiş Dashboard</span>
        </router-link>
      </div>

      <div class="nav-section">
        <div class="nav-section-title">Yönetim</div>
        <router-link to="/admin/sellers" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="user" cls="w-5 h-5" /></span>
          <span class="nav-label">Satıcılar</span>
        </router-link>
        <router-link to="/admin/seller-applications" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="clipboard" cls="w-5 h-5" /></span>
          <span class="nav-label">Satıcı Başvuruları</span>
          <span v-if="pendingCount > 0" class="nav-badge">{{ pendingCount }}</span>
        </router-link>
        <router-link to="/admin/orders" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="cart" cls="w-5 h-5" /></span>
          <span class="nav-label">Siparişler</span>
        </router-link>
        <router-link to="/admin/customers" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="user" cls="w-5 h-5" /></span>
          <span class="nav-label">Müşteriler</span>
        </router-link>
      </div>

      <div class="nav-section">
        <div class="nav-section-title">İçerik</div>
        <router-link to="/admin/categories" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="chart" cls="w-5 h-5" /></span>
          <span class="nav-label">Kategoriler</span>
        </router-link>
        <router-link to="/admin/banners" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="image" cls="w-5 h-5" /></span>
          <span class="nav-label">Bannerlar</span>
        </router-link>
        <router-link to="/admin/pages" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="clipboard" cls="w-5 h-5" /></span>
          <span class="nav-label">Sayfalar</span>
        </router-link>
      </div>

      <div class="nav-section">
        <div class="nav-section-title">Analiz & Raporlar</div>
        <router-link to="/admin/reports" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="chart" cls="w-5 h-5" /></span>
          <span class="nav-label">Raporlar & Analiz</span>
        </router-link>
        <router-link to="/admin/turbo-winners" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="medal" cls="w-5 h-5" /></span>
          <span class="nav-label">Turbo Mod Kazananları</span>
        </router-link>
      </div>

      <div class="nav-section">
        <div class="nav-section-title">Sistem</div>
        <router-link to="/admin/settings" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="flag" cls="w-5 h-5" /></span>
          <span class="nav-label">Ayarlar</span>
        </router-link>
        <router-link to="/admin/notifications" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="bell" cls="w-5 h-5" /></span>
          <span class="nav-label">Bildirimler</span>
        </router-link>
        <router-link to="/admin/theme" class="nav-item">
          <span class="nav-icon" aria-hidden="true"><BadgeIcon name="image" cls="w-5 h-5" /></span>
          <span class="nav-label">Tema</span>
        </router-link>
      </div>
    </nav>
  </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const pendingCount = ref(0)

onMounted(async () => {
  try {
    const response = await axios.get('/api/admin/stats')
    pendingCount.value = response.data.pending_applications || 0
  } catch (error) {
    console.error('Stats yüklenemedi:', error)
  }
})
</script>

<style scoped>
.admin-sidebar {
  width: 260px;
  height: 100vh;
  background: white;
  border-right: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  position: fixed;
  left: 0;
  top: 0;
  overflow-y: auto;
}

.sidebar-header {
  padding: 20px;
  border-bottom: 1px solid #e5e7eb;
}

.sidebar-header h2 {
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.sidebar-nav {
  flex: 1;
  padding: 20px 0;
}

.nav-section {
  margin-bottom: 24px;
}

.nav-section-title {
  padding: 0 20px 8px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #9ca3af;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 20px;
  color: #6b7280;
  text-decoration: none;
  transition: all 0.2s;
  position: relative;
}

.nav-item:hover {
  background: #f9fafb;
  color: #2563eb;
}

.nav-item.router-link-active {
  background: #eff6ff;
  color: #2563eb;
  font-weight: 500;
  border-left: 3px solid #2563eb;
  padding-left: 17px;
}

.nav-icon {
  font-size: 18px;
  width: 20px;
  text-align: center;
}

.nav-label {
  flex: 1;
  font-size: 14px;
}

.nav-badge {
  background: #ef4444;
  color: white;
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 10px;
  min-width: 20px;
  text-align: center;
}

/* Scrollbar styling */
.admin-sidebar::-webkit-scrollbar {
  width: 6px;
}

.admin-sidebar::-webkit-scrollbar-track {
  background: #f9fafb;
}

.admin-sidebar::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

.admin-sidebar::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}
</style>
