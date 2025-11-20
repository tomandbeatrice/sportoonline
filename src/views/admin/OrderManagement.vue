<template>
  <div class="order-management">
    <div class="page-header">
      <div class="header-left">
        <h1 class="flex items-center gap-2"><BadgeIcon name="box" cls="w-8 h-8 text-blue-600" /> Sipariş Yönetimi</h1>
        <p class="subtitle">Tüm siparişleri görüntüleyin ve yönetin</p>
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
          placeholder="Sipariş no, müşteri adı, e-posta..."
          @input="debounceSearch"
        />
      </div>

      <div class="filter-group">
        <label>📊 Durum</label>
        <select v-model="filters.status" @change="loadOrders">
          <option value="">Tümü</option>
          <option value="pending">Bekliyor</option>
          <option value="processing">İşleniyor</option>
          <option value="shipped">Kargoda</option>
          <option value="delivered">Teslim Edildi</option>
          <option value="cancelled">İptal</option>
        </select>
      </div>

      <div class="filter-group">
        <label>💳 Ödeme Durumu</label>
        <select v-model="filters.paymentStatus" @change="loadOrders">
          <option value="">Tümü</option>
          <option value="paid">Ödendi</option>
          <option value="pending">Bekliyor</option>
          <option value="failed">Başarısız</option>
        </select>
      </div>

      <div class="filter-group">
        <label>📅 Tarih Aralığı</label>
        <select v-model="filters.dateRange" @change="loadOrders">
          <option value="">Tüm Zamanlar</option>
          <option value="today">Bugün</option>
          <option value="week">Son 7 Gün</option>
          <option value="month">Son 30 Gün</option>
          <option value="year">Bu Yıl</option>
        </select>
      </div>

      <div class="filter-group">
        <label>🏪 Satıcı</label>
        <select v-model="filters.vendorId" @change="loadOrders">
          <option value="">Tüm Satıcılar</option>
          <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
            {{ vendor.store_name }}
          </option>
        </select>
      </div>

      <div class="filter-group">
        <label>📈 Sıralama</label>
        <select v-model="filters.sortBy" @change="loadOrders">
          <option value="newest">En Yeni</option>
          <option value="oldest">En Eski</option>
          <option value="amount_high">Tutar (Yüksek)</option>
          <option value="amount_low">Tutar (Düşük)</option>
        </select>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
      <div class="stat-card pending">
        <div class="stat-icon">⏳</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.pending }}</div>
          <div class="stat-label">Bekleyen</div>
        </div>
      </div>

      <div class="stat-card processing">
        <div class="stat-icon">⚙️</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.processing }}</div>
          <div class="stat-label">İşlenen</div>
        </div>
      </div>

      <div class="stat-card shipped">
        <div class="stat-icon">🚚</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.shipped }}</div>
          <div class="stat-label">Kargoda</div>
        </div>
      </div>

      <div class="stat-card delivered">
        <div class="stat-icon"><BadgeIcon name="check" cls="w-6 h-6 text-green-600" /></div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.delivered }}</div>
          <div class="stat-label">Teslim Edildi</div>
        </div>
      </div>

      <div class="stat-card total">
        <div class="stat-icon">💰</div>
        <div class="stat-content">
          <div class="stat-value">₺{{ formatNumber(stats.totalRevenue) }}</div>
          <div class="stat-label">Toplam Gelir</div>
        </div>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="table-container">
      <table class="orders-table">
        <thead>
          <tr>
            <th>
              <input 
                type="checkbox" 
                v-model="selectAll" 
                @change="toggleSelectAll"
              />
            </th>
            <th>Sipariş No</th>
            <th>Müşteri</th>
            <th>Satıcı</th>
            <th>Ürün Sayısı</th>
            <th>Tutar</th>
            <th>Ödeme</th>
            <th>Durum</th>
            <th>Tarih</th>
            <th>İşlemler</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="10" class="loading-cell">
              <div class="loader">⏳ Yükleniyor...</div>
            </td>
          </tr>
          <tr v-else-if="orders.length === 0">
            <td colspan="10" class="empty-cell">
              😕 Sipariş bulunamadı
            </td>
          </tr>
          <tr 
            v-else
            v-for="order in orders" 
            :key="order.id"
            :class="{ selected: selectedOrders.includes(order.id) }"
          >
            <td>
              <input 
                type="checkbox" 
                :value="order.id" 
                v-model="selectedOrders"
              />
            </td>
            <td><strong>#{{ order.id }}</strong></td>
            <td>
              <div class="customer-info">
                <div class="customer-name">{{ order.user?.name || 'Misafir' }}</div>
                <div class="customer-email">{{ order.user?.email || 'N/A' }}</div>
              </div>
            </td>
            <td>{{ order.vendor?.store_name || 'N/A' }}</td>
            <td>
              <span class="badge-count">{{ order.items_count || 0 }}</span>
            </td>
            <td><strong>₺{{ formatNumber(order.total_amount) }}</strong></td>
            <td>
              <span :class="['payment-badge', `payment-${order.payment_status}`]">
                {{ getPaymentStatusText(order.payment_status) }}
              </span>
            </td>
            <td>
              <select 
                :value="order.status" 
                @change="updateOrderStatus(order, $event.target.value)"
                :class="['status-select', `status-${order.status}`]"
              >
                <option value="pending">Bekliyor</option>
                <option value="processing">İşleniyor</option>
                <option value="shipped">Kargoda</option>
                <option value="delivered">Teslim Edildi</option>
                <option value="cancelled">İptal</option>
              </select>
            </td>
            <td>{{ formatDate(order.created_at) }}</td>
            <td>
              <div class="action-buttons">
                <button 
                  @click="viewOrder(order)" 
                  class="btn-action btn-view"
                  title="Detayları Gör"
                >
                  👁️
                </button>
                <button 
                  @click="printInvoice(order)" 
                  class="btn-action btn-print"
                  title="Fatura Yazdır"
                >
                  🖨️
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" v-if="selectedOrders.length > 0">
      <div class="bulk-info">
        {{ selectedOrders.length }} sipariş seçildi
      </div>
      <div class="bulk-buttons">
        <button @click="bulkUpdateStatus('processing')" class="btn-bulk">⚙️ İşle</button>
        <button @click="bulkUpdateStatus('shipped')" class="btn-bulk">🚚 Kargola</button>
        <button @click="bulkUpdateStatus('delivered')" class="btn-bulk"><BadgeIcon name="check" cls="w-4 h-4 inline mr-1" /> Teslim Et</button>
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

    <!-- Order Detail Modal -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="closeDetailModal">
      <div class="modal-content order-detail-modal">
        <div class="modal-header">
          <h2 class="flex items-center gap-2"><BadgeIcon name="box" cls="w-6 h-6 text-blue-600" /> Sipariş #{{ selectedOrder?.id }}</h2>
          <button @click="closeDetailModal" class="btn-close"><BadgeIcon name="close" cls="w-5 h-5" /></button>
        </div>

        <div class="modal-body">
          <div class="detail-section">
            <h3>👤 Müşteri Bilgileri</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <label>Ad Soyad</label>
                <div>{{ selectedOrder?.user?.name || 'Misafir' }}</div>
              </div>
              <div class="detail-item">
                <label>E-posta</label>
                <div>{{ selectedOrder?.user?.email || 'N/A' }}</div>
              </div>
              <div class="detail-item">
                <label>Telefon</label>
                <div>{{ selectedOrder?.shipping_phone || 'N/A' }}</div>
              </div>
              <div class="detail-item">
                <label>Adres</label>
                <div>{{ selectedOrder?.shipping_address || 'N/A' }}</div>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h3>🏪 Satıcı Bilgileri</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <label>Mağaza</label>
                <div>{{ selectedOrder?.vendor?.store_name || 'N/A' }}</div>
              </div>
              <div class="detail-item">
                <label>Telefon</label>
                <div>{{ selectedOrder?.vendor?.phone || 'N/A' }}</div>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h3 class="flex items-center gap-2"><BadgeIcon name="box" cls="w-5 h-5 text-blue-600" /> Sipariş Detayları</h3>
            <table class="items-table">
              <thead>
                <tr>
                  <th>Ürün</th>
                  <th>Birim Fiyat</th>
                  <th>Adet</th>
                  <th>Toplam</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in selectedOrder?.items" :key="item.id">
                  <td>{{ item.product?.name || 'N/A' }}</td>
                  <td>₺{{ formatNumber(item.price) }}</td>
                  <td>{{ item.quantity }}</td>
                  <td><strong>₺{{ formatNumber(item.price * item.quantity) }}</strong></td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3"><strong>Toplam</strong></td>
                  <td><strong>₺{{ formatNumber(selectedOrder?.total_amount) }}</strong></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="detail-section">
            <h3>📊 Sipariş Durumu</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <label>Durum</label>
                <div>
                  <span :class="['status-badge', `status-${selectedOrder?.status}`]">
                    {{ getStatusText(selectedOrder?.status) }}
                  </span>
                </div>
              </div>
              <div class="detail-item">
                <label>Ödeme Durumu</label>
                <div>
                  <span :class="['payment-badge', `payment-${selectedOrder?.payment_status}`]">
                    {{ getPaymentStatusText(selectedOrder?.payment_status) }}
                  </span>
                </div>
              </div>
              <div class="detail-item">
                <label>Kargo Takip No</label>
                <div>{{ selectedOrder?.tracking_number || 'Henüz atanmadı' }}</div>
              </div>
              <div class="detail-item">
                <label>Sipariş Tarihi</label>
                <div>{{ formatDateTime(selectedOrder?.created_at) }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="closeDetailModal" class="btn-secondary">Kapat</button>
          <button @click="printInvoice(selectedOrder)" class="btn-primary">🖨️ Fatura Yazdır</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

interface Order {
  id: number
  status: string
  payment_status: string
  total_amount: number
  shipping_address?: string
  shipping_phone?: string
  tracking_number?: string
  items_count?: number
  created_at: string
  user?: {
    name: string
    email: string
  }
  vendor?: {
    store_name: string
    phone: string
  }
  items?: Array<{
    id: number
    product?: {
      name: string
    }
    price: number
    quantity: number
  }>
}

const orders = ref<Order[]>([])
const vendors = ref([])
const loading = ref(false)
const selectedOrders = ref<number[]>([])
const selectAll = ref(false)
const showDetailModal = ref(false)
const selectedOrder = ref<Order | null>(null)

const filters = ref({
  search: '',
  status: '',
  paymentStatus: '',
  dateRange: '',
  vendorId: '',
  sortBy: 'newest'
})

const stats = ref({
  pending: 0,
  processing: 0,
  shipped: 0,
  delivered: 0,
  totalRevenue: 0
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
    pending: 'Bekliyor',
    processing: 'İşleniyor',
    shipped: 'Kargoda',
    delivered: 'Teslim Edildi',
    cancelled: 'İptal'
  }
  return texts[status] || status
}

const getPaymentStatusText = (status: string) => {
  const texts = {
    paid: 'Ödendi',
    pending: 'Bekliyor',
    failed: 'Başarısız'
  }
  return texts[status] || status
}

const loadOrders = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/orders', {
      params: {
        page: pagination.value.current,
        search: filters.value.search,
        status: filters.value.status,
        payment_status: filters.value.paymentStatus,
        date_range: filters.value.dateRange,
        vendor_id: filters.value.vendorId,
        sort_by: filters.value.sortBy
      }
    })

    orders.value = data.data
    pagination.value = {
      current: data.current_page,
      total: data.last_page,
      perPage: data.per_page
    }

    loadStats()
  } catch (error) {
    console.error('Siparişler yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    const { data } = await axios.get('/api/admin/orders/stats')
    stats.value = data
  } catch (error) {
    console.error('İstatistikler yüklenemedi:', error)
  }
}

const loadVendors = async () => {
  try {
    const { data } = await axios.get('/api/admin/sellers?per_page=1000')
    vendors.value = data.data
  } catch (error) {
    console.error('Satıcılar yüklenemedi:', error)
  }
}

const debounceSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = window.setTimeout(() => {
    pagination.value.current = 1
    loadOrders()
  }, 500)
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedOrders.value = orders.value.map(o => o.id)
  } else {
    selectedOrders.value = []
  }
}

const changePage = (page: number) => {
  if (page >= 1 && page <= pagination.value.total) {
    pagination.value.current = page
    loadOrders()
  }
}

const viewOrder = async (order: Order) => {
  try {
    const { data } = await axios.get(`/api/admin/orders/${order.id}`)
    selectedOrder.value = data
    showDetailModal.value = true
  } catch (error) {
    console.error('Sipariş detayı yüklenemedi:', error)
  }
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedOrder.value = null
}

const updateOrderStatus = async (order: Order, newStatus: string) => {
  try {
    await axios.put(`/api/admin/orders/${order.id}/status`, {
      status: newStatus
    })
    await loadOrders()
  } catch (error) {
    console.error('Sipariş durumu güncellenemedi:', error)
    alert('Bir hata oluştu')
  }
}

const bulkUpdateStatus = async (newStatus: string) => {
  if (!confirm(`${selectedOrders.value.length} siparişin durumunu değiştirmek istediğinizden emin misiniz?`)) {
    return
  }

  try {
    await axios.post('/api/admin/orders/bulk-update-status', {
      order_ids: selectedOrders.value,
      status: newStatus
    })
    selectedOrders.value = []
    selectAll.value = false
    await loadOrders()
  } catch (error) {
    console.error('Toplu durum güncelleme başarısız:', error)
    alert('Bir hata oluştu')
  }
}

const bulkExport = () => {
  // TODO: Implement export
  console.log('Export selected orders:', selectedOrders.value)
}

const exportToExcel = () => {
  // TODO: Implement full export
  console.log('Export all orders to Excel')
}

const printInvoice = (order: Order) => {
  // TODO: Implement invoice print
  console.log('Print invoice for order:', order.id)
}

const refreshData = () => {
  loadOrders()
  loadVendors()
}

onMounted(() => {
  loadOrders()
  loadVendors()
})
</script>

<style scoped>
.order-management {
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
  border-color: #3b82f6;
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
  overflow-x: auto;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  margin-bottom: 1rem;
}

.orders-table {
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
  flex-direction: column;
  gap: 0.25rem;
}

.customer-name {
  font-weight: 500;
  color: #111827;
}

.customer-email {
  font-size: 0.875rem;
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

.payment-badge,
.status-badge {
  display: inline-block;
  padding: 0.375rem 0.875rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.payment-paid {
  background: #d1fae5;
  color: #065f46;
}

.payment-pending {
  background: #fef3c7;
  color: #92400e;
}

.payment-failed {
  background: #fee2e2;
  color: #991b1b;
}

.status-select {
  padding: 0.5rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: border-color 0.3s;
}

.status-select:focus {
  outline: none;
  border-color: #3b82f6;
}

.status-pending {
  border-color: #fbbf24;
  background: #fef3c7;
}

.status-processing {
  border-color: #3b82f6;
  background: #dbeafe;
}

.status-shipped {
  border-color: #8b5cf6;
  background: #e0e7ff;
}

.status-delivered {
  border-color: #10b981;
  background: #d1fae5;
}

.status-cancelled {
  border-color: #ef4444;
  background: #fee2e2;
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

.btn-print {
  background: #fef3c7;
}

.btn-print:hover {
  background: #fde68a;
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
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
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

.items-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

.items-table thead {
  background: #f9fafb;
}

.items-table th,
.items-table td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.items-table tfoot td {
  border-top: 2px solid #111827;
  padding-top: 1rem;
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
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}

@media (max-width: 768px) {
  .order-management {
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

  .bulk-actions {
    flex-direction: column;
    width: 90%;
  }
}
</style>
