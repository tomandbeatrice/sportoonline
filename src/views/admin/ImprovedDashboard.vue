<template>
  <div class="admin-dashboard-improved">
    <!-- Quick Stats Cards -->
    <div class="quick-stats">
      <div class="stat-card sales">
        <div class="stat-icon">💰</div>
        <div class="stat-content">
          <div class="stat-label">Toplam Satış</div>
          <div class="stat-value">₺{{ formatNumber(stats.total_revenue) }}</div>
          <div class="stat-change positive">↗ +12% bu ay</div>
        </div>
      </div>

      <div class="stat-card orders">
        <div class="stat-icon">📦</div>
        <div class="stat-content">
          <div class="stat-label">Toplam Sipariş</div>
          <div class="stat-value">{{ formatNumber(stats.total_orders) }}</div>
          <div class="stat-change positive">↗ +8% bu ay</div>
        </div>
      </div>

      <div class="stat-card sellers">
        <div class="stat-icon">🏪</div>
        <div class="stat-content">
          <div class="stat-label">Aktif Satıcı</div>
          <div class="stat-value">{{ stats.active_sellers }}</div>
          <div class="stat-change neutral">{{ stats.pending_applications }} beklemede</div>
        </div>
      </div>

      <div class="stat-card users">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
          <div class="stat-label">Toplam Kullanıcı</div>
          <div class="stat-value">{{ formatNumber(stats.total_users) }}</div>
          <div class="stat-change positive">↗ +15% bu ay</div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-section">
      <h2>⚡ Hızlı İşlemler</h2>
      <div class="quick-actions">
        <router-link to="/admin/seller-applications" class="action-card">
          <div class="action-icon">📋</div>
          <div class="action-content">
            <div class="action-title">Satıcı Başvuruları</div>
            <div class="action-badge" v-if="stats.pending_applications > 0">
              {{ stats.pending_applications }} bekliyor
            </div>
          </div>
        </router-link>

        <router-link to="/admin/orders" class="action-card">
          <div class="action-icon">🚚</div>
          <div class="action-content">
            <div class="action-title">Sipariş Yönetimi</div>
            <div class="action-badge">{{ stats.pending_orders }} işlemde</div>
          </div>
        </router-link>

        <router-link to="/admin/sellers" class="action-card">
          <div class="action-icon">👔</div>
          <div class="action-content">
            <div class="action-title">Satıcı Yönetimi</div>
            <div class="action-badge">{{ stats.active_sellers }} aktif</div>
          </div>
        </router-link>

        <router-link to="/admin/products" class="action-card">
          <div class="action-icon">🛍️</div>
          <div class="action-content">
            <div class="action-title">Ürün Yönetimi</div>
            <div class="action-badge">{{ stats.total_products }} ürün</div>
          </div>
        </router-link>

        <router-link to="/admin/campaigns" class="action-card">
          <div class="action-icon">🎯</div>
          <div class="action-content">
            <div class="action-title">Kampanya Yönetimi</div>
            <div class="action-badge">{{ stats.active_campaigns }} aktif</div>
          </div>
        </router-link>

        <router-link to="/admin/reports" class="action-card">
          <div class="action-icon">📊</div>
          <div class="action-content">
            <div class="action-title">Raporlar & Analiz</div>
            <div class="action-badge">Finansal analiz</div>
          </div>
        </router-link>

        <router-link to="/admin/categories" class="action-card">
          <div class="action-icon">📑</div>
          <div class="action-content">
            <div class="action-title">Kategori Yönetimi</div>
            <div class="action-badge">Hiyerarşik yapı</div>
          </div>
        </router-link>

        <router-link to="/admin/banners" class="action-card">
          <div class="action-icon">🖼️</div>
          <div class="action-content">
            <div class="action-title">Banner Yönetimi</div>
            <div class="action-badge">Zamanlama</div>
          </div>
        </router-link>

        <router-link to="/admin/pages" class="action-card">
          <div class="action-icon">📄</div>
          <div class="action-content">
            <div class="action-title">Sayfa Yönetimi</div>
            <div class="action-badge">CMS</div>
          </div>
        </router-link>

        <router-link to="/admin/customers" class="action-card">
          <div class="action-icon">👤</div>
          <div class="action-content">
            <div class="action-title">Müşteri Yönetimi</div>
            <div class="action-badge">Segmentasyon</div>
          </div>
        </router-link>

        <router-link to="/admin/settings" class="action-card">
          <div class="action-icon">⚙️</div>
          <div class="action-content">
            <div class="action-title">Sistem Ayarları</div>
            <div class="action-badge">Yapılandırma</div>
          </div>
        </router-link>

        <router-link to="/admin/notifications" class="action-card">
          <div class="action-icon">🔔</div>
          <div class="action-content">
            <div class="action-title">Bildirim Merkezi</div>
            <div class="action-badge">Email, SMS, Push</div>
          </div>
        </router-link>

        <router-link to="/admin/theme" class="action-card">
          <div class="action-icon">🎨</div>
          <div class="action-content">
            <div class="action-title">Tema Yönetimi</div>
            <div class="action-badge">Görünüm</div>
          </div>
        </router-link>
      </div>
    </div>

    <!-- Recent Activities -->
    <div class="activities-section">
      <div class="section-header">
        <h2>🕐 Son Aktiviteler</h2>
        <button @click="loadActivities" class="refresh-btn">🔄 Yenile</button>
      </div>

      <div class="activities-list">
        <div v-for="activity in recentActivities" :key="activity.id" class="activity-item">
          <div class="activity-icon" :class="`type-${activity.type}`">
            {{ getActivityIcon(activity.type) }}
          </div>
          <div class="activity-content">
            <div class="activity-text">{{ activity.description }}</div>
            <div class="activity-time">{{ formatTime(activity.created_at) }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="recent-orders-section">
      <div class="section-header">
        <h2>📦 Son Siparişler</h2>
        <router-link to="/admin/orders" class="view-all-link">Tümünü Gör →</router-link>
      </div>

      <div class="orders-table">
        <table>
          <thead>
            <tr>
              <th>Sipariş No</th>
              <th>Müşteri</th>
              <th>Tutar</th>
              <th>Durum</th>
              <th>Tarih</th>
              <th>İşlemler</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in recentOrders" :key="order.id">
              <td><strong>#{{ order.id }}</strong></td>
              <td>{{ order.user?.name || 'Misafir' }}</td>
              <td>₺{{ formatNumber(order.total_amount) }}</td>
              <td>
                <span :class="['status-badge', `status-${order.status}`]">
                  {{ getStatusText(order.status) }}
                </span>
              </td>
              <td>{{ formatDate(order.created_at) }}</td>
              <td>
                <button @click="viewOrder(order)" class="btn-view">
                  👁️ Görüntüle
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Alerts & Notifications -->
    <div class="alerts-section" v-if="systemAlerts.length > 0">
      <h2>⚠️ Sistem Uyarıları</h2>
      <div class="alerts-list">
        <div 
          v-for="alert in systemAlerts" 
          :key="alert.id"
          :class="['alert-item', `alert-${alert.type}`]"
        >
          <div class="alert-icon">{{ getAlertIcon(alert.type) }}</div>
          <div class="alert-content">
            <div class="alert-title">{{ alert.title }}</div>
            <div class="alert-message">{{ alert.message }}</div>
          </div>
          <button @click="dismissAlert(alert.id)" class="alert-dismiss">✕</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const stats = ref({
  total_revenue: 0,
  total_orders: 0,
  active_sellers: 0,
  total_users: 0,
  pending_applications: 0,
  pending_orders: 0,
  total_products: 0,
  active_campaigns: 0,
})

const recentActivities = ref([])
const recentOrders = ref([])
const systemAlerts = ref([])

const formatNumber = (num: number) => {
  return new Intl.NumberFormat('tr-TR').format(num)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  })
}

const formatTime = (date: string) => {
  const now = new Date()
  const then = new Date(date)
  const diff = Math.floor((now.getTime() - then.getTime()) / 1000)

  if (diff < 60) return 'Az önce'
  if (diff < 3600) return `${Math.floor(diff / 60)} dakika önce`
  if (diff < 86400) return `${Math.floor(diff / 3600)} saat önce`
  return `${Math.floor(diff / 86400)} gün önce`
}

const getActivityIcon = (type: string) => {
  const icons = {
    order: '🛒',
    seller: '🏪',
    product: '📦',
    campaign: '🎯',
    user: '👤',
  }
  return icons[type] || '📌'
}

const getStatusText = (status: string) => {
  const texts = {
    pending: 'Bekliyor',
    processing: 'İşleniyor',
    shipped: 'Kargoda',
    delivered: 'Teslim Edildi',
    cancelled: 'İptal',
  }
  return texts[status] || status
}

const getAlertIcon = (type: string) => {
  const icons = {
    warning: '⚠️',
    error: '❌',
    info: 'ℹ️',
    success: '✅',
  }
  return icons[type] || 'ℹ️'
}

const loadStats = async () => {
  try {
    const { data } = await axios.get('/api/admin/stats')
    stats.value = {
      total_revenue: data.total_revenue || 0,
      total_orders: data.total_orders || 0,
      active_sellers: data.active_sellers || 0,
      total_users: data.total_users || 0,
      pending_applications: data.pending_applications || 0,
      pending_orders: data.pending_orders || 0,
      total_products: data.total_products || 0,
      active_campaigns: data.active_campaigns || 0,
    }
  } catch (error) {
    console.error('Stats yüklenemedi:', error)
  }
}

const loadActivities = async () => {
  try {
    const { data } = await axios.get('/api/admin/activities')
    recentActivities.value = data.slice(0, 10)
  } catch (error) {
    console.error('Aktiviteler yüklenemedi:', error)
  }
}

const loadRecentOrders = async () => {
  try {
    const { data } = await axios.get('/api/admin/orders/recent')
    recentOrders.value = data.slice(0, 10)
  } catch (error) {
    console.error('Siparişler yüklenemedi:', error)
  }
}

const loadSystemAlerts = async () => {
  try {
    const { data } = await axios.get('/api/admin/alerts')
    systemAlerts.value = data
  } catch (error) {
    console.error('Uyarılar yüklenemedi:', error)
  }
}

const viewOrder = (order: any) => {
  router.push(`/admin/orders/${order.id}`)
}

const dismissAlert = (alertId: number) => {
  systemAlerts.value = systemAlerts.value.filter((a: any) => a.id !== alertId)
}

onMounted(() => {
  loadStats()
  loadActivities()
  loadRecentOrders()
  loadSystemAlerts()
})
</script>

<style scoped>
.admin-dashboard-improved {
  padding: 2rem;
  max-width: 1600px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.quick-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  gap: 1.5rem;
  transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.stat-icon {
  font-size: 3rem;
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
}

.stat-card.sales .stat-icon {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-card.orders .stat-icon {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.stat-card.sellers .stat-icon {
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.stat-card.users .stat-icon {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #111827;
  margin-bottom: 0.25rem;
}

.stat-change {
  font-size: 0.875rem;
}

.stat-change.positive {
  color: #10b981;
}

.stat-change.neutral {
  color: #6b7280;
}

.quick-actions-section h2,
.activities-section h2,
.recent-orders-section h2,
.alerts-section h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 1.5rem;
}

.quick-actions {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
}

.action-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  text-decoration: none;
  transition: all 0.3s;
}

.action-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.action-icon {
  font-size: 2.5rem;
}

.action-content {
  text-align: center;
}

.action-title {
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.5rem;
}

.action-badge {
  font-size: 0.875rem;
  color: #6b7280;
  padding: 0.25rem 0.75rem;
  background: #f3f4f6;
  border-radius: 12px;
  display: inline-block;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.refresh-btn,
.view-all-link {
  padding: 0.5rem 1rem;
  background: #f3f4f6;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.3s;
}

.refresh-btn:hover,
.view-all-link:hover {
  background: #e5e7eb;
}

.activities-list {
  background: white;
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.activity-item:last-child {
  border-bottom: none;
}

.activity-icon {
  font-size: 1.5rem;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background: #f3f4f6;
}

.activity-content {
  flex: 1;
}

.activity-text {
  color: #111827;
  margin-bottom: 0.25rem;
}

.activity-time {
  font-size: 0.875rem;
  color: #6b7280;
}

.orders-table {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f9fafb;
}

th {
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
}

td {
  padding: 1rem;
  border-top: 1px solid #f3f4f6;
  color: #111827;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-processing {
  background: #dbeafe;
  color: #1e40af;
}

.status-shipped {
  background: #e0e7ff;
  color: #4338ca;
}

.status-delivered {
  background: #d1fae5;
  color: #065f46;
}

.status-cancelled {
  background: #fee2e2;
  color: #991b1b;
}

.btn-view {
  padding: 0.5rem 1rem;
  background: #f3f4f6;
  border: none;
  border-radius: 6px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-view:hover {
  background: #e5e7eb;
}

.alerts-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.alert-item {
  background: white;
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
  display: flex;
  align-items: center;
  gap: 1rem;
  border-left: 4px solid;
}

.alert-warning {
  border-left-color: #f59e0b;
}

.alert-error {
  border-left-color: #ef4444;
}

.alert-info {
  border-left-color: #3b82f6;
}

.alert-success {
  border-left-color: #10b981;
}

.alert-icon {
  font-size: 1.5rem;
}

.alert-content {
  flex: 1;
}

.alert-title {
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.25rem;
}

.alert-message {
  font-size: 0.875rem;
  color: #6b7280;
}

.alert-dismiss {
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #9ca3af;
  cursor: pointer;
  padding: 0.5rem;
  transition: color 0.3s;
}

.alert-dismiss:hover {
  color: #6b7280;
}

@media (max-width: 768px) {
  .admin-dashboard-improved {
    padding: 1rem;
  }

  .quick-stats,
  .quick-actions {
    grid-template-columns: 1fr;
  }

  table {
    font-size: 0.875rem;
  }

  th,
  td {
    padding: 0.75rem 0.5rem;
  }
}
</style>
