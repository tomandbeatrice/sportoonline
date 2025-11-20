<template>
  <div class="seller-management">
    <div class="page-header">
      <div class="header-left">
        <h1>🏪 Satıcı Yönetimi</h1>
        <p class="subtitle">Tüm satıcıları görüntüleyin ve yönetin</p>
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
          placeholder="Mağaza adı, e-posta veya telefon..."
          @input="debounceSearch"
        />
      </div>

      <div class="filter-group">
        <label>📊 Durum</label>
        <select v-model="filters.status" @change="loadSellers">
          <option value="">Tümü</option>
          <option value="active">Aktif</option>
          <option value="inactive">Pasif</option>
          <option value="suspended">Askıya Alınmış</option>
        </select>
      </div>

      <div class="filter-group">
        <label>📅 Kayıt Tarihi</label>
        <select v-model="filters.dateRange" @change="loadSellers">
          <option value="">Tüm Zamanlar</option>
          <option value="today">Bugün</option>
          <option value="week">Son 7 Gün</option>
          <option value="month">Son 30 Gün</option>
          <option value="year">Bu Yıl</option>
        </select>
      </div>

      <div class="filter-group">
        <label>📈 Sıralama</label>
        <select v-model="filters.sortBy" @change="loadSellers">
          <option value="newest">En Yeni</option>
          <option value="oldest">En Eski</option>
          <option value="revenue_high">Gelir (Yüksek)</option>
          <option value="revenue_low">Gelir (Düşük)</option>
          <option value="orders_high">Sipariş (Çok)</option>
          <option value="orders_low">Sipariş (Az)</option>
        </select>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-icon active">🟢</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.active }}</div>
          <div class="stat-label">Aktif Satıcı</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon pending">🟡</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.inactive }}</div>
          <div class="stat-label">Pasif Satıcı</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon suspended">🔴</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.suspended }}</div>
          <div class="stat-label">Askıda</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon total">📊</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.total }}</div>
          <div class="stat-label">Toplam Satıcı</div>
        </div>
      </div>
    </div>

    <!-- Sellers Table -->
    <div class="table-container">
      <table class="sellers-table">
        <thead>
          <tr>
            <th>
              <input 
                type="checkbox" 
                v-model="selectAll" 
                @change="toggleSelectAll"
              />
            </th>
            <th>Satıcı</th>
            <th>İletişim</th>
            <th>Ürün</th>
            <th>Sipariş</th>
            <th>Gelir</th>
            <th>Durum</th>
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
          <tr v-else-if="sellers.length === 0">
            <td colspan="9" class="empty-cell">
              😕 Satıcı bulunamadı
            </td>
          </tr>
          <tr 
            v-else
            v-for="seller in sellers" 
            :key="seller.id"
            :class="{ selected: selectedSellers.includes(seller.id) }"
          >
            <td>
              <input 
                type="checkbox" 
                :value="seller.id" 
                v-model="selectedSellers"
              />
            </td>
            <td>
              <div class="seller-info">
                <div class="seller-avatar">
                  {{ seller.store_name?.charAt(0) || 'S' }}
                </div>
                <div class="seller-details">
                  <div class="seller-name">{{ seller.store_name }}</div>
                  <div class="seller-owner">{{ seller.user?.name || 'N/A' }}</div>
                </div>
              </div>
            </td>
            <td>
              <div class="contact-info">
                <div>📧 {{ seller.user?.email || 'N/A' }}</div>
                <div>📱 {{ seller.phone || 'N/A' }}</div>
              </div>
            </td>
            <td>
              <span class="badge-count">{{ seller.products_count || 0 }}</span>
            </td>
            <td>
              <span class="badge-count">{{ seller.orders_count || 0 }}</span>
            </td>
            <td>
              <strong>₺{{ formatNumber(seller.total_revenue || 0) }}</strong>
            </td>
            <td>
              <span :class="['status-badge', `status-${seller.status}`]">
                {{ getStatusText(seller.status) }}
              </span>
            </td>
            <td>{{ formatDate(seller.created_at) }}</td>
            <td>
              <div class="action-buttons">
                <button 
                  @click="viewSeller(seller)" 
                  class="btn-action btn-view"
                  title="Detayları Gör"
                >
                  👁️
                </button>
                <button 
                  @click="editSeller(seller)" 
                  class="btn-action btn-edit"
                  title="Düzenle"
                >
                  ✏️
                </button>
                <button 
                  v-if="seller.status === 'active'"
                  @click="suspendSeller(seller)" 
                  class="btn-action btn-suspend"
                  title="Askıya Al"
                >
                  ⏸️
                </button>
                <button 
                  v-else
                  @click="activateSeller(seller)" 
                  class="btn-action btn-activate"
                  title="Aktif Et"
                >
                  ▶️
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" v-if="selectedSellers.length > 0">
      <div class="bulk-info">
        {{ selectedSellers.length }} satıcı seçildi
      </div>
      <div class="bulk-buttons">
        <button @click="bulkActivate" class="btn-bulk inline-flex items-center gap-2"><BadgeIcon name="check" cls="w-4 h-4" /> Aktif Et</button>
        <button @click="bulkSuspend" class="btn-bulk">⏸️ Askıya Al</button>
        <button @click="bulkExport" class="btn-bulk">📊 Seçilenleri Dışa Aktar</button>
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

    <!-- Seller Detail Modal -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="closeDetailModal">
      <div class="modal-content seller-detail-modal">
        <div class="modal-header">
          <h2>🏪 {{ selectedSeller?.store_name }}</h2>
          <button @click="closeDetailModal" class="btn-close">✕</button>
        </div>

        <div class="modal-body">
          <div class="detail-section">
            <h3>📋 Genel Bilgiler</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <label>Mağaza Adı</label>
                <div>{{ selectedSeller?.store_name }}</div>
              </div>
              <div class="detail-item">
                <label>Sahip</label>
                <div>{{ selectedSeller?.user?.name }}</div>
              </div>
              <div class="detail-item">
                <label>E-posta</label>
                <div>{{ selectedSeller?.user?.email }}</div>
              </div>
              <div class="detail-item">
                <label>Telefon</label>
                <div>{{ selectedSeller?.phone || 'Belirtilmemiş' }}</div>
              </div>
              <div class="detail-item">
                <label>Vergi Numarası</label>
                <div>{{ selectedSeller?.tax_number || 'Belirtilmemiş' }}</div>
              </div>
              <div class="detail-item">
                <label>Durum</label>
                <div>
                  <span :class="['status-badge', `status-${selectedSeller?.status}`]">
                    {{ getStatusText(selectedSeller?.status) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h3>📊 İstatistikler</h3>
            <div class="stats-grid">
              <div class="stat-box">
                <div class="stat-icon"><BadgeIcon name="box" cls="w-6 h-6 text-blue-600" /></div>
                <div class="stat-value">{{ selectedSeller?.products_count || 0 }}</div>
                <div class="stat-label">Toplam Ürün</div>
              </div>
              <div class="stat-box">
                <div class="stat-icon"><BadgeIcon name="cart" cls="w-6 h-6 text-green-600" /></div>
                <div class="stat-value">{{ selectedSeller?.orders_count || 0 }}</div>
                <div class="stat-label">Toplam Sipariş</div>
              </div>
              <div class="stat-box">
                <div class="stat-icon">💰</div>
                <div class="stat-value">₺{{ formatNumber(selectedSeller?.total_revenue || 0) }}</div>
                <div class="stat-label">Toplam Gelir</div>
              </div>
              <div class="stat-box">
                <div class="stat-icon"><IconStar cls="w-6 h-6 text-yellow-400" :filled="true" /></div>
                <div class="stat-value">{{ selectedSeller?.avg_rating || 'N/A' }}</div>
                <div class="stat-label">Ortalama Puan</div>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h3>🏦 Banka Bilgileri</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <label>Banka Adı</label>
                <div>{{ selectedSeller?.bank_name || 'Belirtilmemiş' }}</div>
              </div>
              <div class="detail-item">
                <label>IBAN</label>
                <div>{{ selectedSeller?.iban || 'Belirtilmemiş' }}</div>
              </div>
              <div class="detail-item">
                <label>Hesap Sahibi</label>
                <div>{{ selectedSeller?.account_holder || 'Belirtilmemiş' }}</div>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h3>📅 Tarihler</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <label>Kayıt Tarihi</label>
                <div>{{ formatDateTime(selectedSeller?.created_at) }}</div>
              </div>
              <div class="detail-item">
                <label>Son Güncelleme</label>
                <div>{{ formatDateTime(selectedSeller?.updated_at) }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="closeDetailModal" class="btn-secondary">Kapat</button>
          <button @click="editSeller(selectedSeller)" class="btn-primary">Düzenle</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import IconStar from '@/components/icons/IconStar.vue'

interface Seller {
  id: number
  store_name: string
  status: 'active' | 'inactive' | 'suspended'
  phone?: string
  tax_number?: string
  bank_name?: string
  iban?: string
  account_holder?: string
  products_count?: number
  orders_count?: number
  total_revenue?: number
  avg_rating?: number
  created_at: string
  updated_at: string
  user?: {
    name: string
    email: string
  }
}

const sellers = ref<Seller[]>([])
const loading = ref(false)
const selectedSellers = ref<number[]>([])
const selectAll = ref(false)
const showDetailModal = ref(false)
const selectedSeller = ref<Seller | null>(null)

const filters = ref({
  search: '',
  status: '',
  dateRange: '',
  sortBy: 'newest'
})

const stats = ref({
  active: 0,
  inactive: 0,
  suspended: 0,
  total: 0
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

const getStatusText = (status: string) => {
  const texts = {
    active: 'Aktif',
    inactive: 'Pasif',
    suspended: 'Askıda'
  }
  return texts[status] || status
}

const loadSellers = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/sellers', {
      params: {
        page: pagination.value.current,
        search: filters.value.search,
        status: filters.value.status,
        date_range: filters.value.dateRange,
        sort_by: filters.value.sortBy
      }
    })

    sellers.value = data.data
    pagination.value = {
      current: data.current_page,
      total: data.last_page,
      perPage: data.per_page
    }

    loadStats()
  } catch (error) {
    console.error('Satıcılar yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    const { data } = await axios.get('/api/admin/sellers/stats')
    stats.value = data
  } catch (error) {
    console.error('İstatistikler yüklenemedi:', error)
  }
}

const debounceSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = window.setTimeout(() => {
    pagination.value.current = 1
    loadSellers()
  }, 500)
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedSellers.value = sellers.value.map(s => s.id)
  } else {
    selectedSellers.value = []
  }
}

const changePage = (page: number) => {
  if (page >= 1 && page <= pagination.value.total) {
    pagination.value.current = page
    loadSellers()
  }
}

const viewSeller = (seller: Seller) => {
  selectedSeller.value = seller
  showDetailModal.value = true
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedSeller.value = null
}

const editSeller = (seller: Seller) => {
  // TODO: Implement edit modal
  console.log('Edit seller:', seller.id)
}

const suspendSeller = async (seller: Seller) => {
  if (!confirm(`${seller.store_name} satıcısını askıya almak istediğinizden emin misiniz?`)) {
    return
  }

  try {
    await axios.post(`/api/admin/sellers/${seller.id}/suspend`)
    await loadSellers()
  } catch (error) {
    console.error('Satıcı askıya alınamadı:', error)
    alert('Bir hata oluştu')
  }
}

const activateSeller = async (seller: Seller) => {
  try {
    await axios.post(`/api/admin/sellers/${seller.id}/activate`)
    await loadSellers()
  } catch (error) {
    console.error('Satıcı aktif edilemedi:', error)
    alert('Bir hata oluştu')
  }
}

const bulkActivate = async () => {
  if (!confirm(`${selectedSellers.value.length} satıcıyı aktif etmek istediğinizden emin misiniz?`)) {
    return
  }

  try {
    await axios.post('/api/admin/sellers/bulk-activate', {
      seller_ids: selectedSellers.value
    })
    selectedSellers.value = []
    selectAll.value = false
    await loadSellers()
  } catch (error) {
    console.error('Toplu aktif etme başarısız:', error)
    alert('Bir hata oluştu')
  }
}

const bulkSuspend = async () => {
  if (!confirm(`${selectedSellers.value.length} satıcıyı askıya almak istediğinizden emin misiniz?`)) {
    return
  }

  try {
    await axios.post('/api/admin/sellers/bulk-suspend', {
      seller_ids: selectedSellers.value
    })
    selectedSellers.value = []
    selectAll.value = false
    await loadSellers()
  } catch (error) {
    console.error('Toplu askıya alma başarısız:', error)
    alert('Bir hata oluştu')
  }
}

const bulkExport = () => {
  // TODO: Implement export
  console.log('Export selected sellers:', selectedSellers.value)
}

const exportToExcel = () => {
  // TODO: Implement full export
  console.log('Export all sellers to Excel')
}

const refreshData = () => {
  loadSellers()
}

onMounted(() => {
  loadSellers()
})
</script>

<style scoped>
.seller-management {
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
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

.table-container {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  margin-bottom: 1rem;
}

.sellers-table {
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

.seller-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.seller-avatar {
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

.seller-name {
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.25rem;
}

.seller-owner {
  font-size: 0.875rem;
  color: #6b7280;
}

.contact-info div {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.badge-count {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background: #f3f4f6;
  border-radius: 12px;
  font-weight: 500;
  font-size: 0.875rem;
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

.status-suspended {
  background: #fef3c7;
  color: #92400e;
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

.btn-edit {
  background: #fef3c7;
}

.btn-edit:hover {
  background: #fde68a;
}

.btn-suspend {
  background: #fee2e2;
}

.btn-suspend:hover {
  background: #fecaca;
}

.btn-activate {
  background: #d1fae5;
}

.btn-activate:hover {
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
  max-width: 800px;
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
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

@media (max-width: 1200px) {
  .sellers-table {
    font-size: 0.875rem;
  }

  th,
  td {
    padding: 0.75rem 0.5rem;
  }
}

@media (max-width: 768px) {
  .seller-management {
    padding: 1rem;
  }

  .page-header {
    flex-direction: column;
    gap: 1rem;
  }

  .filters-section {
    grid-template-columns: 1fr;
  }

  .stats-cards {
    grid-template-columns: 1fr;
  }

  .table-container {
    overflow-x: auto;
  }

  .sellers-table {
    min-width: 1000px;
  }

  .bulk-actions {
    flex-direction: column;
    width: 90%;
  }
}
</style>
