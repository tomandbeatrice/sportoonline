<template>
  <div class="customer-management">
    <div class="page-header">
      <div class="header-left">
        <h1>👥 Müşteri Yönetimi</h1>
        <p class="subtitle">Tüm müşterileri görüntüleyin ve yönetin</p>
      </div>
      <div class="header-actions">
        <button @click="refreshData" class="btn-refresh">🔄 Yenile</button>
        <button @click="exportToExcel" class="btn-export">📊 Excel'e Aktar</button>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
      <div class="filter-group">
        <label>🔍 Ara</label>
        <input 
          v-model="filters.search" 
          type="text" 
          placeholder="Ad, e-posta veya telefon..."
          @input="debounceSearch"
        />
      </div>

      <div class="filter-group">
        <label>📊 Durum</label>
        <select v-model="filters.status" @change="loadCustomers">
          <option value="">Tümü</option>
          <option value="active">Aktif</option>
          <option value="inactive">Pasif</option>
          <option value="blocked">Bloklu</option>
        </select>
      </div>

      <div class="filter-group">
        <label>✉️ E-posta Doğrulama</label>
        <select v-model="filters.emailVerified" @change="loadCustomers">
          <option value="">Tümü</option>
          <option value="1">Doğrulanmış</option>
          <option value="0">Doğrulanmamış</option>
        </select>
      </div>

      <div class="filter-group">
        <label>📅 Kayıt Tarihi</label>
        <select v-model="filters.dateRange" @change="loadCustomers">
          <option value="">Tüm Zamanlar</option>
          <option value="today">Bugün</option>
          <option value="week">Son 7 Gün</option>
          <option value="month">Son 30 Gün</option>
          <option value="year">Bu Yıl</option>
        </select>
      </div>

      <div class="filter-group">
        <label>🛒 Sipariş Durumu</label>
        <select v-model="filters.orderStatus" @change="loadCustomers">
          <option value="">Tümü</option>
          <option value="with_orders">Sipariş Vermiş</option>
          <option value="without_orders">Sipariş Vermemiş</option>
        </select>
      </div>

      <div class="filter-group">
        <label>📈 Sıralama</label>
        <select v-model="filters.sortBy" @change="loadCustomers">
          <option value="newest">En Yeni</option>
          <option value="oldest">En Eski</option>
          <option value="orders_high">Sipariş (Çok)</option>
          <option value="orders_low">Sipariş (Az)</option>
          <option value="spent_high">Harcama (Yüksek)</option>
          <option value="spent_low">Harcama (Düşük)</option>
        </select>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
      <div class="stat-card total">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
          <div class="stat-value">{{ formatNumber(stats.total) }}</div>
          <div class="stat-label">Toplam Müşteri</div>
        </div>
      </div>

      <div class="stat-card active">
        <div class="stat-icon"><BadgeIcon name="check" cls="w-6 h-6 text-green-600" /></div>
        <div class="stat-content">
          <div class="stat-value">{{ formatNumber(stats.active) }}</div>
          <div class="stat-label">Aktif Müşteri</div>
        </div>
      </div>

      <div class="stat-card verified">
        <div class="stat-icon">📧</div>
        <div class="stat-content">
          <div class="stat-value">{{ formatNumber(stats.verified) }}</div>
          <div class="stat-label">Doğrulanmış</div>
        </div>
      </div>

      <div class="stat-card with-orders">
        <div class="stat-icon">🛒</div>
        <div class="stat-content">
          <div class="stat-value">{{ formatNumber(stats.withOrders) }}</div>
          <div class="stat-label">Sipariş Vermiş</div>
        </div>
      </div>

      <div class="stat-card revenue">
        <div class="stat-icon">💰</div>
        <div class="stat-content">
          <div class="stat-value">₺{{ formatNumber(stats.totalRevenue) }}</div>
          <div class="stat-label">Toplam Gelir</div>
        </div>
      </div>
    </div>

    <!-- Customer Segments -->
    <div class="segments-section">
      <h3>📊 Müşteri Segmentleri</h3>
      <div class="segment-cards">
        <div class="segment-card vip">
          <div class="segment-info">
            <div class="segment-icon">👑</div>
            <div class="segment-details">
              <div class="segment-name">VIP Müşteriler</div>
              <div class="segment-desc">10+ sipariş veya 5000₺+ harcama</div>
            </div>
          </div>
          <div class="segment-count">{{ stats.vipCustomers || 0 }}</div>
        </div>

        <div class="segment-card regular">
          <div class="segment-info">
            <div class="segment-icon"><IconStar cls="w-5 h-5 text-yellow-400" :filled="true" /></div>
            <div class="segment-details">
              <div class="segment-name">Düzenli Müşteriler</div>
              <div class="segment-desc">3-9 sipariş</div>
            </div>
          </div>
          <div class="segment-count">{{ stats.regularCustomers || 0 }}</div>
        </div>

        <div class="segment-card new">
          <div class="segment-info">
            <div class="segment-icon">🆕</div>
            <div class="segment-details">
              <div class="segment-name">Yeni Müşteriler</div>
              <div class="segment-desc">1-2 sipariş</div>
            </div>
          </div>
          <div class="segment-count">{{ stats.newCustomers || 0 }}</div>
        </div>

        <div class="segment-card inactive">
          <div class="segment-info">
            <div class="segment-icon">😴</div>
            <div class="segment-details">
              <div class="segment-name">Pasif Müşteriler</div>
              <div class="segment-desc">90+ gün sipariş yok</div>
            </div>
          </div>
          <div class="segment-count">{{ stats.inactiveCustomers || 0 }}</div>
        </div>
      </div>
    </div>

    <!-- Customers Table -->
    <div class="table-container">
      <table class="customers-table">
        <thead>
          <tr>
            <th>
              <input 
                type="checkbox" 
                v-model="selectAll" 
                @change="toggleSelectAll"
              />
            </th>
            <th>Müşteri</th>
            <th>İletişim</th>
            <th>Durum</th>
            <th>Sipariş</th>
            <th>Toplam Harcama</th>
            <th>Son Sipariş</th>
            <th>Kayıt Tarihi</th>
            <th>İşlemler</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="9" class="loading-cell">
              <div class="loader">⏳ Yükleniyor...</div>
            </td>
          </tr>
          <tr v-else-if="customers.length === 0">
            <td colspan="9" class="empty-cell">
              😕 Müşteri bulunamadı
            </td>
          </tr>
          <tr 
            v-else
            v-for="customer in customers" 
            :key="customer.id"
            :class="{ selected: selectedCustomers.includes(customer.id) }"
          >
            <td>
              <input 
                type="checkbox" 
                :value="customer.id" 
                v-model="selectedCustomers"
              />
            </td>
            <td>
              <div class="customer-info">
                <div class="customer-avatar">
                  {{ customer.name?.charAt(0) || 'M' }}
                </div>
                <div class="customer-details">
                  <div class="customer-name">
                    {{ customer.name }}
                    <span v-if="customer.segment === 'vip'" class="badge-vip">👑 VIP</span>
                  </div>
                  <div class="customer-email">{{ customer.email }}</div>
                </div>
              </div>
            </td>
            <td>
              <div class="contact-info">
                <div v-if="customer.phone">📱 {{ customer.phone }}</div>
                <div v-else class="text-muted">Telefon yok</div>
              </div>
            </td>
            <td>
              <div class="status-badges">
                <span :class="['status-badge', `status-${customer.status || 'active'}`]">
                  {{ getStatusText(customer.status) }}
                </span>
                <span v-if="customer.email_verified_at" class="verified-badge flex items-center gap-1">
                  <BadgeIcon name="check" cls="w-3 h-3" /> Doğrulanmış
                </span>
              </div>
            </td>
            <td>
              <span class="badge-count">{{ customer.orders_count || 0 }}</span>
            </td>
            <td>
              <strong>₺{{ formatNumber(customer.total_spent || 0) }}</strong>
            </td>
            <td>
              <span v-if="customer.last_order_date" class="date-text">
                {{ formatDate(customer.last_order_date) }}
              </span>
              <span v-else class="text-muted">-</span>
            </td>
            <td>{{ formatDate(customer.created_at) }}</td>
            <td>
              <div class="action-buttons">
                <button 
                  @click="viewCustomer(customer)" 
                  class="btn-action btn-view"
                  title="Detayları Gör"
                >
                  👁️
                </button>
                <button 
                  @click="sendEmail(customer)" 
                  class="btn-action btn-email"
                  title="E-posta Gönder"
                >
                  ✉️
                </button>
                <button 
                  v-if="customer.status !== 'blocked'"
                  @click="blockCustomer(customer)" 
                  class="btn-action btn-block"
                  title="Blokla"
                >
                  🚫
                </button>
                <button 
                  v-else
                  @click="unblockCustomer(customer)" 
                  class="btn-action btn-unblock"
                  title="Blok Kaldır"
                >
                  <BadgeIcon name="check" cls="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" v-if="selectedCustomers.length > 0">
      <div class="bulk-info">
        {{ selectedCustomers.length }} müşteri seçildi
      </div>
      <div class="bulk-buttons">
        <button @click="bulkSendEmail" class="btn-bulk">✉️ E-posta Gönder</button>
        <button @click="bulkBlock" class="btn-bulk">🚫 Blokla</button>
        <button @click="bulkExport" class="btn-bulk">📊 Dışa Aktar</button>
      </div>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <button 
        @click="changePage(pagination.current - 1)" 
        :disabled="pagination.current === 1"
        class="btn-page"
      >
        ← Önceki
      </button>
      
      <div class="page-numbers">
        <button
          v-for="page in visiblePages"
          :key="page"
          @click="changePage(page)"
          :class="['btn-page', { active: page === pagination.current }]"
        >
          {{ page }}
        </button>
      </div>

      <button 
        @click="changePage(pagination.current + 1)" 
        :disabled="pagination.current === pagination.total"
        class="btn-page"
      >
        Sonraki →
      </button>
    </div>

    <!-- Customer Detail Modal -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="closeDetailModal">
      <div class="modal-content customer-detail-modal">
        <div class="modal-header">
          <h2>👤 {{ selectedCustomer?.name }}</h2>
          <button @click="closeDetailModal" class="btn-close"><BadgeIcon name="close" cls="w-5 h-5" /></button>
        </div>

        <div class="modal-body">
          <div class="detail-section">
            <h3>📋 Genel Bilgiler</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <label>Ad Soyad</label>
                <div>{{ selectedCustomer?.name }}</div>
              </div>
              <div class="detail-item">
                <label>E-posta</label>
                <div>{{ selectedCustomer?.email }}</div>
              </div>
              <div class="detail-item">
                <label>Telefon</label>
                <div>{{ selectedCustomer?.phone || 'Belirtilmemiş' }}</div>
              </div>
              <div class="detail-item">
                <label>Durum</label>
                <div>
                  <span :class="['status-badge', `status-${selectedCustomer?.status || 'active'}`]">
                    {{ getStatusText(selectedCustomer?.status) }}
                  </span>
                </div>
              </div>
              <div class="detail-item">
                <label>E-posta Doğrulama</label>
                <div>
                  <span v-if="selectedCustomer?.email_verified_at" class="verified-badge flex items-center gap-1">
                    <BadgeIcon name="check" cls="w-3 h-3" /> Doğrulanmış
                  </span>
                  <span v-else class="unverified-badge flex items-center gap-1"><BadgeIcon name="close" cls="w-3 h-3" /> Doğrulanmamış</span>
                </div>
              </div>
              <div class="detail-item">
                <label>Segment</label>
                <div>
                  <span v-if="selectedCustomer?.segment === 'vip'" class="badge-vip">👑 VIP</span>
                  <span v-else-if="selectedCustomer?.segment === 'regular'" class="badge-regular inline-flex items-center gap-1"><IconStar cls="w-4 h-4 text-yellow-400" :filled="true" /> Düzenli</span>
                  <span v-else-if="selectedCustomer?.segment === 'new'" class="badge-new">🆕 Yeni</span>
                  <span v-else class="badge-inactive">😴 Pasif</span>
                </div>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h3>📊 İstatistikler</h3>
            <div class="stats-grid">
              <div class="stat-box">
                <div class="stat-icon">🛒</div>
                <div class="stat-value">{{ selectedCustomer?.orders_count || 0 }}</div>
                <div class="stat-label">Toplam Sipariş</div>
              </div>
              <div class="stat-box">
                <div class="stat-icon">💰</div>
                <div class="stat-value">₺{{ formatNumber(selectedCustomer?.total_spent || 0) }}</div>
                <div class="stat-label">Toplam Harcama</div>
              </div>
              <div class="stat-box">
                <div class="stat-icon">📈</div>
                <div class="stat-value">₺{{ formatNumber(selectedCustomer?.avg_order_value || 0) }}</div>
                <div class="stat-label">Ortalama Sepet</div>
              </div>
              <div class="stat-box">
                <div class="stat-icon">❤️</div>
                <div class="stat-value">{{ selectedCustomer?.favorites_count || 0 }}</div>
                <div class="stat-label">Favori Ürün</div>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h3 class="flex items-center gap-2"><BadgeIcon name="box" cls="w-5 h-5 text-blue-600" /> Son Siparişler</h3>
            <table class="orders-table" v-if="selectedCustomer?.recent_orders?.length">
              <thead>
                <tr>
                  <th>Sipariş No</th>
                  <th>Tarih</th>
                  <th>Tutar</th>
                  <th>Durum</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in selectedCustomer.recent_orders" :key="order.id">
                  <td><strong>#{{ order.id }}</strong></td>
                  <td>{{ formatDate(order.created_at) }}</td>
                  <td>₺{{ formatNumber(order.total_amount) }}</td>
                  <td>
                    <span :class="['status-badge', `status-${order.status}`]">
                      {{ getOrderStatusText(order.status) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-else class="empty-message">Henüz sipariş yok</div>
          </div>

          <div class="detail-section">
            <h3>📅 Tarihler</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <label>Kayıt Tarihi</label>
                <div>{{ formatDateTime(selectedCustomer?.created_at) }}</div>
              </div>
              <div class="detail-item">
                <label>Son Sipariş</label>
                <div>{{ selectedCustomer?.last_order_date ? formatDateTime(selectedCustomer.last_order_date) : 'Sipariş yok' }}</div>
              </div>
              <div class="detail-item">
                <label>Son Giriş</label>
                <div>{{ selectedCustomer?.last_login ? formatDateTime(selectedCustomer.last_login) : 'Bilinmiyor' }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="closeDetailModal" class="btn-secondary">Kapat</button>
          <button @click="sendEmail(selectedCustomer)" class="btn-primary">✉️ E-posta Gönder</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'
import IconStar from '@/components/icons/IconStar.vue'

interface Customer {
  id: number
  name: string
  email: string
  phone?: string
  status?: string
  email_verified_at?: string
  segment?: string
  orders_count?: number
  total_spent?: number
  avg_order_value?: number
  favorites_count?: number
  last_order_date?: string
  last_login?: string
  created_at: string
  recent_orders?: Array<{
    id: number
    total_amount: number
    status: string
    created_at: string
  }>
}

const customers = ref<Customer[]>([])
const loading = ref(false)
const selectedCustomers = ref<number[]>([])
const selectAll = ref(false)
const showDetailModal = ref(false)
const selectedCustomer = ref<Customer | null>(null)

const filters = ref({
  search: '',
  status: '',
  emailVerified: '',
  dateRange: '',
  orderStatus: '',
  sortBy: 'newest'
})

const stats = ref({
  total: 0,
  active: 0,
  verified: 0,
  withOrders: 0,
  totalRevenue: 0,
  vipCustomers: 0,
  regularCustomers: 0,
  newCustomers: 0,
  inactiveCustomers: 0
})

const pagination = ref({
  current: 1,
  total: 1,
  perPage: 20
})

let searchTimeout: number | null = null

const visiblePages = computed(() => {
  const pages = []
  const current = pagination.value.current
  const total = pagination.value.total
  const delta = 2

  for (let i = Math.max(1, current - delta); i <= Math.min(total, current + delta); i++) {
    pages.push(i)
  }

  return pages
})

const formatNumber = (num: number) => {
  return new Intl.NumberFormat('tr-TR').format(num)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

const formatDateTime = (date: string) => {
  return new Date(date).toLocaleString('tr-TR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusText = (status?: string) => {
  const texts = {
    active: 'Aktif',
    inactive: 'Pasif',
    blocked: 'Bloklu'
  }
  return texts[status || 'active'] || 'Aktif'
}

const getOrderStatusText = (status: string) => {
  const texts = {
    pending: 'Bekliyor',
    processing: 'İşleniyor',
    shipped: 'Kargoda',
    delivered: 'Teslim Edildi',
    cancelled: 'İptal'
  }
  return texts[status] || status
}

const loadCustomers = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/customers', {
      params: {
        page: pagination.value.current,
        search: filters.value.search,
        status: filters.value.status,
        email_verified: filters.value.emailVerified,
        date_range: filters.value.dateRange,
        order_status: filters.value.orderStatus,
        sort_by: filters.value.sortBy
      }
    })

    customers.value = data.data
    pagination.value = {
      current: data.current_page,
      total: data.last_page,
      perPage: data.per_page
    }

    loadStats()
  } catch (error) {
    console.error('Müşteriler yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    const { data } = await axios.get('/api/admin/customers/stats')
    stats.value = data
  } catch (error) {
    console.error('İstatistikler yüklenemedi:', error)
  }
}

const debounceSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = window.setTimeout(() => {
    pagination.value.current = 1
    loadCustomers()
  }, 500)
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedCustomers.value = customers.value.map(c => c.id)
  } else {
    selectedCustomers.value = []
  }
}

const changePage = (page: number) => {
  if (page >= 1 && page <= pagination.value.total) {
    pagination.value.current = page
    loadCustomers()
  }
}

const viewCustomer = async (customer: Customer) => {
  try {
    const { data } = await axios.get(`/api/admin/customers/${customer.id}`)
    selectedCustomer.value = data
    showDetailModal.value = true
  } catch (error) {
    console.error('Müşteri detayı yüklenemedi:', error)
  }
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedCustomer.value = null
}

const sendEmail = (customer: Customer) => {
  // TODO: Implement email send
  console.log('Send email to:', customer.email)
}

const blockCustomer = async (customer: Customer) => {
  if (!confirm(`${customer.name} müşterisini bloklamak istediğinizden emin misiniz?`)) {
    return
  }

  try {
    await axios.post(`/api/admin/customers/${customer.id}/block`)
    await loadCustomers()
  } catch (error) {
    console.error('Müşteri bloklanamadı:', error)
    alert('Bir hata oluştu')
  }
}

const unblockCustomer = async (customer: Customer) => {
  try {
    await axios.post(`/api/admin/customers/${customer.id}/unblock`)
    await loadCustomers()
  } catch (error) {
    console.error('Blok kaldırılamadı:', error)
    alert('Bir hata oluştu')
  }
}

const bulkSendEmail = () => {
  // TODO: Implement bulk email
  console.log('Send bulk email to:', selectedCustomers.value)
}

const bulkBlock = async () => {
  if (!confirm(`${selectedCustomers.value.length} müşteriyi bloklamak istediğinizden emin misiniz?`)) {
    return
  }

  try {
    await axios.post('/api/admin/customers/bulk-block', {
      customer_ids: selectedCustomers.value
    })
    selectedCustomers.value = []
    selectAll.value = false
    await loadCustomers()
  } catch (error) {
    console.error('Toplu bloklama başarısız:', error)
    alert('Bir hata oluştu')
  }
}

const bulkExport = () => {
  // TODO: Implement export
  console.log('Export customers:', selectedCustomers.value)
}

const exportToExcel = () => {
  // TODO: Implement export
  console.log('Export all customers')
}

const refreshData = () => {
  loadCustomers()
}

onMounted(() => {
  loadCustomers()
})
</script>

<style scoped>
.customer-management {
  padding: 2rem;
  max-width: 1800px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
}

.header-left h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #6b7280;
  font-size: 1rem;
}

.header-actions {
  display: flex;
  gap: 1rem;
}

.btn-refresh,
.btn-export {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-refresh {
  background: #f3f4f6;
  color: #374151;
}

.btn-refresh:hover {
  background: #e5e7eb;
}

.btn-export {
  background: #10b981;
  color: white;
}

.btn-export:hover {
  background: #059669;
}

.filters-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-group label {
  font-weight: 500;
  font-size: 0.875rem;
  color: #374151;
}

.filter-group input,
.filter-group select {
  padding: 0.75rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: border-color 0.3s;
}

.filter-group input:focus,
.filter-group select:focus {
  outline: none;
  border-color: #8b5cf6;
}

.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  font-size: 2rem;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: #f3f4f6;
}

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #111827;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
}

.segments-section {
  margin-bottom: 2rem;
}

.segments-section h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 1rem;
}

.segment-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.segment-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.segment-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.segment-icon {
  font-size: 2rem;
}

.segment-name {
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.25rem;
}

.segment-desc {
  font-size: 0.875rem;
  color: #6b7280;
}

.segment-count {
  font-size: 1.5rem;
  font-weight: bold;
  color: #8b5cf6;
}

.table-container {
  background: white;
  border-radius: 12px;
  overflow-x: auto;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  margin-bottom: 1rem;
}

.customers-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 1200px;
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
  border-bottom: 2px solid #e5e7eb;
}

td {
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

tr.selected {
  background: #f3f4f6;
}

.loading-cell,
.empty-cell {
  text-align: center;
  padding: 3rem !important;
  color: #6b7280;
  font-size: 1rem;
}

.customer-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.customer-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1.25rem;
}

.customer-name {
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.25rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.customer-email {
  font-size: 0.875rem;
  color: #6b7280;
}

.contact-info {
  font-size: 0.875rem;
  color: #6b7280;
}

.text-muted {
  color: #9ca3af;
}

.status-badges {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.status-badge {
  display: inline-block;
  padding: 0.375rem 0.875rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-active {
  background: #d1fae5;
  color: #065f46;
}

.status-inactive {
  background: #fee2e2;
  color: #991b1b;
}

.status-blocked {
  background: #fecaca;
  color: #7f1d1d;
}

.verified-badge {
  display: inline-block;
  padding: 0.25rem 0.625rem;
  background: #d1fae5;
  color: #065f46;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 600;
}

.unverified-badge {
  display: inline-block;
  padding: 0.25rem 0.625rem;
  background: #fee2e2;
  color: #991b1b;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-vip,
.badge-regular,
.badge-new,
.badge-inactive {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 8px;
  font-weight: 600;
}

.badge-vip {
  background: #fef3c7;
  color: #92400e;
}

.badge-regular {
  background: #dbeafe;
  color: #1e40af;
}

.badge-new {
  background: #d1fae5;
  color: #065f46;
}

.badge-inactive {
  background: #f3f4f6;
  color: #6b7280;
}

.badge-count {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background: #f3f4f6;
  border-radius: 12px;
  font-weight: 500;
  font-size: 0.875rem;
}

.date-text {
  font-size: 0.875rem;
  color: #6b7280;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-action {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s;
}

.btn-view {
  background: #dbeafe;
}

.btn-view:hover {
  background: #bfdbfe;
}

.btn-email {
  background: #fef3c7;
}

.btn-email:hover {
  background: #fde68a;
}

.btn-block {
  background: #fee2e2;
}

.btn-block:hover {
  background: #fecaca;
}

.btn-unblock {
  background: #d1fae5;
}

.btn-unblock:hover {
  background: #a7f3d0;
}

.bulk-actions {
  position: fixed;
  bottom: 2rem;
  left: 50%;
  transform: translateX(-50%);
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
  padding: 1rem 2rem;
  display: flex;
  align-items: center;
  gap: 2rem;
  z-index: 100;
}

.bulk-info {
  font-weight: 600;
  color: #111827;
}

.bulk-buttons {
  display: flex;
  gap: 1rem;
}

.btn-bulk {
  padding: 0.625rem 1.25rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  background: #f3f4f6;
  color: #374151;
  transition: background 0.3s;
}

.btn-bulk:hover {
  background: #e5e7eb;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 2rem;
}

.page-numbers {
  display: flex;
  gap: 0.5rem;
}

.btn-page {
  padding: 0.5rem 1rem;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s;
}

.btn-page:hover:not(:disabled) {
  background: #f3f4f6;
}

.btn-page.active {
  background: #8b5cf6;
  color: white;
  border-color: #8b5cf6;
}

.btn-page:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 2rem;
}

.modal-content {
  background: white;
  border-radius: 16px;
  max-width: 900px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
  padding: 2rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
}

.btn-close {
  width: 36px;
  height: 36px;
  border: none;
  background: #f3f4f6;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1.25rem;
  transition: background 0.3s;
}

.btn-close:hover {
  background: #e5e7eb;
}

.modal-body {
  padding: 2rem;
}

.detail-section {
  margin-bottom: 2rem;
}

.detail-section:last-child {
  margin-bottom: 0;
}

.detail-section h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 1rem;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.detail-item label {
  display: block;
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.detail-item div {
  font-weight: 500;
  color: #111827;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.stat-box {
  background: #f9fafb;
  border-radius: 12px;
  padding: 1.5rem;
  text-align: center;
}

.stat-box .stat-icon {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.stat-box .stat-value {
  font-size: 1.5rem;
  font-weight: bold;
  color: #111827;
  margin-bottom: 0.25rem;
}

.stat-box .stat-label {
  font-size: 0.875rem;
  color: #6b7280;
}

.orders-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

.orders-table thead {
  background: #f9fafb;
}

.orders-table th,
.orders-table td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.empty-message {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
}

.modal-footer {
  padding: 1.5rem 2rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.btn-secondary,
.btn-primary {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

.btn-primary {
  background: #8b5cf6;
  color: white;
}

.btn-primary:hover {
  background: #7c3aed;
}

@media (max-width: 768px) {
  .customer-management {
    padding: 1rem;
  }

  .page-header {
    flex-direction: column;
    gap: 1rem;
  }

  .filters-section {
    grid-template-columns: 1fr;
  }

  .stats-cards,
  .segment-cards {
    grid-template-columns: 1fr;
  }

  .bulk-actions {
    flex-direction: column;
    width: 90%;
  }
}
</style>
