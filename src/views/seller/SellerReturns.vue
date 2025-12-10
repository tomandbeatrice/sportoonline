<template>
  <div class="seller-returns">
    <div class="page-header">
      <h1>İade Talepleri</h1>
      <p class="text-muted">Müşterilerinizin iade taleplerini yönetin</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card pending">
        <div class="stat-icon">
          <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
          <span class="stat-value">{{ stats.pending }}</span>
          <span class="stat-label">Bekleyen</span>
        </div>
      </div>
      <div class="stat-card approved">
        <div class="stat-icon">
          <i class="fas fa-check"></i>
        </div>
        <div class="stat-content">
          <span class="stat-value">{{ stats.approved }}</span>
          <span class="stat-label">Onaylanan</span>
        </div>
      </div>
      <div class="stat-card shipping">
        <div class="stat-icon">
          <i class="fas fa-truck"></i>
        </div>
        <div class="stat-content">
          <span class="stat-value">{{ stats.shipped_back }}</span>
          <span class="stat-label">Kargoda</span>
        </div>
      </div>
      <div class="stat-card completed">
        <div class="stat-icon">
          <i class="fas fa-check-double"></i>
        </div>
        <div class="stat-content">
          <span class="stat-value">{{ stats.completed }}</span>
          <span class="stat-label">Tamamlanan</span>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
      <div class="filter-group">
        <label>Durum</label>
        <select v-model="filters.status" @change="fetchReturns">
          <option value="">Tümü</option>
          <option value="pending">Beklemede</option>
          <option value="approved">Onaylandı</option>
          <option value="rejected">Reddedildi</option>
          <option value="shipped_back">Kargoya Verildi</option>
          <option value="received">Teslim Alındı</option>
          <option value="refunded">İade Edildi</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Arama</label>
        <input 
          type="text" 
          v-model="filters.search" 
          placeholder="Sipariş no, müşteri adı..."
          @input="debounceSearch"
        />
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <i class="fas fa-spinner fa-spin"></i>
      <span>Yükleniyor...</span>
    </div>

    <!-- Returns Table -->
    <div v-else-if="returns.length" class="returns-table-wrapper">
      <table class="returns-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Ürün</th>
            <th>Müşteri</th>
            <th>Sebep</th>
            <th>Tutar</th>
            <th>Durum</th>
            <th>Tarih</th>
            <th>İşlemler</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ret in returns" :key="ret.id">
            <td>#{{ ret.id }}</td>
            <td>
              <div class="product-cell">
                <img :src="ret.item?.product?.image" :alt="ret.item?.product?.name" />
                <span>{{ ret.item?.product?.name }}</span>
              </div>
            </td>
            <td>{{ ret.user?.name }}</td>
            <td>{{ getReasonLabel(ret.reason) }}</td>
            <td>{{ formatPrice(ret.refund_amount) }}</td>
            <td>
              <span :class="['status-badge', ret.status]">
                {{ getStatusLabel(ret.status) }}
              </span>
            </td>
            <td>{{ formatDate(ret.created_at) }}</td>
            <td>
              <div class="action-buttons">
                <button 
                  class="btn-sm btn-view" 
                  @click="viewReturn(ret)"
                  title="Detay"
                >
                  <i class="fas fa-eye"></i>
                </button>
                <button 
                  v-if="ret.status === 'pending'"
                  class="btn-sm btn-approve" 
                  @click="approveReturn(ret)"
                  title="Onayla"
                >
                  <i class="fas fa-check"></i>
                </button>
                <button 
                  v-if="ret.status === 'pending'"
                  class="btn-sm btn-reject" 
                  @click="openRejectModal(ret)"
                  title="Reddet"
                >
                  <i class="fas fa-times"></i>
                </button>
                <button 
                  v-if="(ret.status === 'pending' || ret.status === 'approved') && !ret.return_shipping_code"
                  class="btn-sm btn-shipping" 
                  @click="openShippingCodeModal(ret)"
                  title="Kargo Kodu Gönder"
                >
                  <i class="fas fa-truck"></i>
                </button>
                <button 
                  v-if="ret.status === 'shipped_back'"
                  class="btn-sm btn-receive" 
                  @click="receiveItem(ret)"
                  title="Teslim Al"
                >
                  <i class="fas fa-box-open"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="pagination" v-if="pagination.total > pagination.perPage">
        <button 
          :disabled="pagination.currentPage === 1"
          @click="goToPage(pagination.currentPage - 1)"
        >
          <i class="fas fa-chevron-left"></i>
        </button>
        <span>{{ pagination.currentPage }} / {{ pagination.lastPage }}</span>
        <button 
          :disabled="pagination.currentPage === pagination.lastPage"
          @click="goToPage(pagination.currentPage + 1)"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="empty-state">
      <i class="fas fa-inbox"></i>
      <h3>İade talebi bulunamadı</h3>
      <p>Henüz bir iade talebi almadınız veya filtrelere uygun sonuç yok.</p>
    </div>

    <!-- View Modal -->
    <div v-if="showViewModal" class="modal-overlay" @click.self="showViewModal = false">
      <div class="modal-content modal-lg">
        <div class="modal-header">
          <h3>İade Detayı #{{ selectedReturn?.id }}</h3>
          <button class="modal-close" @click="showViewModal = false">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body" v-if="selectedReturn">
          <div class="detail-grid">
            <div class="detail-section">
              <h4>Ürün Bilgileri</h4>
              <div class="product-detail">
                <img :src="selectedReturn.item?.product?.image" :alt="selectedReturn.item?.product?.name" />
                <div>
                  <strong>{{ selectedReturn.item?.product?.name }}</strong>
                  <p>Adet: {{ selectedReturn.item?.quantity }}</p>
                  <p>Birim Fiyat: {{ formatPrice(selectedReturn.item?.price) }}</p>
                </div>
              </div>
            </div>
            
            <div class="detail-section">
              <h4>Müşteri Bilgileri</h4>
              <p><strong>Ad:</strong> {{ selectedReturn.user?.name }}</p>
              <p><strong>E-posta:</strong> {{ selectedReturn.user?.email }}</p>
            </div>

            <div class="detail-section">
              <h4>İade Bilgileri</h4>
              <p><strong>Sebep:</strong> {{ getReasonLabel(selectedReturn.reason) }}</p>
              <p><strong>Açıklama:</strong> {{ selectedReturn.description || '-' }}</p>
              <p><strong>Talep Tarihi:</strong> {{ formatDate(selectedReturn.created_at) }}</p>
              <p><strong>Durum:</strong> 
                <span :class="['status-badge', selectedReturn.status]">
                  {{ getStatusLabel(selectedReturn.status) }}
                </span>
              </p>
              <p><strong>İade Tutarı:</strong> {{ formatPrice(selectedReturn.refund_amount) }}</p>
            </div>

            <div class="detail-section" v-if="selectedReturn.images?.length">
              <h4>Ürün Fotoğrafları</h4>
              <div class="image-gallery">
                <img 
                  v-for="(img, index) in selectedReturn.images" 
                  :key="index" 
                  :src="img" 
                  @click="openImage(img)"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showViewModal = false">Kapat</button>
          <button 
            v-if="selectedReturn?.status === 'pending'" 
            class="btn btn-success" 
            @click="approveReturn(selectedReturn); showViewModal = false"
          >
            Onayla
          </button>
          <button 
            v-if="selectedReturn?.status === 'pending'" 
            class="btn btn-danger" 
            @click="openRejectModal(selectedReturn); showViewModal = false"
          >
            Reddet
          </button>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="showRejectModal" class="modal-overlay" @click.self="showRejectModal = false">
      <div class="modal-content">
        <div class="modal-header">
          <h3>İade Talebini Reddet</h3>
          <button class="modal-close" @click="showRejectModal = false">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Red Sebebi <span class="required">*</span></label>
            <textarea 
              v-model="rejectReason" 
              rows="4" 
              placeholder="Lütfen red sebebini belirtin..."
            ></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showRejectModal = false">İptal</button>
          <button 
            class="btn btn-danger" 
            @click="submitReject"
            :disabled="!rejectReason.trim()"
          >
            Reddet
          </button>
        </div>
      </div>
    </div>

    <!-- Shipping Code Modal -->
    <div v-if="showShippingCodeModal" class="modal-overlay" @click.self="showShippingCodeModal = false">
      <div class="modal-content">
        <div class="modal-header">
          <h3>İade Kargo Kodu Gönder</h3>
          <button class="modal-close" @click="showShippingCodeModal = false">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p class="text-sm text-slate-600 mb-4">
            Müşteriye ücretsiz iade kargo kodu göndererek iade sürecini hızlandırabilirsiniz.
          </p>
          <div class="form-group">
            <label>Kargo Firması <span class="required">*</span></label>
            <select v-model="shippingCodeForm.carrier" class="form-select">
              <option value="">Seçin</option>
              <option value="yurtici">Yurtiçi Kargo</option>
              <option value="aras">Aras Kargo</option>
              <option value="mng">MNG Kargo</option>
              <option value="ptt">PTT Kargo</option>
              <option value="surat">Sürat Kargo</option>
            </select>
          </div>
          <div class="form-group">
            <label>Kargo Kodu <span class="required">*</span></label>
            <input 
              type="text" 
              v-model="shippingCodeForm.code" 
              placeholder="Örn: ABC123456789"
              class="form-input"
            />
          </div>
          <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <span>Bu kod müşteriye bildirim olarak gönderilecek ve iade detay sayfasında görüntülenecektir.</span>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showShippingCodeModal = false">İptal</button>
          <button 
            class="btn btn-primary" 
            @click="submitShippingCode"
            :disabled="!shippingCodeForm.carrier || !shippingCodeForm.code.trim()"
          >
            <i class="fas fa-paper-plane"></i> Kodu Gönder
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

interface ReturnItem {
  id: number
  order_id: number
  user_id: number
  status: string
  reason: string
  description: string
  refund_amount: number
  images: string[]
  return_shipping_code?: string
  return_shipping_carrier?: string
  created_at: string
  item?: any
  user?: any
  order?: any
}

const loading = ref(true)
const returns = ref<ReturnItem[]>([])
const selectedReturn = ref<ReturnItem | null>(null)
const showViewModal = ref(false)
const showRejectModal = ref(false)
const showShippingCodeModal = ref(false)
const rejectReason = ref('')
const shippingCodeForm = reactive({
  carrier: '',
  code: ''
})

const stats = reactive({
  pending: 0,
  approved: 0,
  shipped_back: 0,
  completed: 0
})

const filters = reactive({
  status: '',
  search: ''
})

const pagination = reactive({
  currentPage: 1,
  lastPage: 1,
  perPage: 15,
  total: 0
})

let searchTimeout: ReturnType<typeof setTimeout> | null = null

const fetchReturns = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.status) params.append('status', filters.status)
    if (filters.search) params.append('search', filters.search)
    params.append('page', pagination.currentPage.toString())

    const response = await axios.get(`/api/seller/returns?${params.toString()}`)
    returns.value = response.data.data
    pagination.currentPage = response.data.current_page
    pagination.lastPage = response.data.last_page
    pagination.total = response.data.total
    pagination.perPage = response.data.per_page
  } catch (error) {
    console.error('İade listesi alınamadı:', error)
  } finally {
    loading.value = false
  }
}

const fetchStats = async () => {
  try {
    // İstatistikleri her durum için ayrı ayrı hesapla
    const statuses = ['pending', 'approved', 'shipped_back', 'received', 'refunded']
    for (const status of statuses) {
      const response = await axios.get(`/api/seller/returns?status=${status}&per_page=1`)
      if (status === 'received' || status === 'refunded') {
        stats.completed += response.data.total
      } else {
        stats[status as keyof typeof stats] = response.data.total
      }
    }
  } catch (error) {
    console.error('İstatistikler alınamadı:', error)
  }
}

const debounceSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    pagination.currentPage = 1
    fetchReturns()
  }, 500)
}

const goToPage = (page: number) => {
  pagination.currentPage = page
  fetchReturns()
}

const viewReturn = (ret: ReturnItem) => {
  selectedReturn.value = ret
  showViewModal.value = true
}

const approveReturn = async (ret: ReturnItem) => {
  if (!confirm('Bu iade talebini onaylamak istediğinize emin misiniz?')) return
  
  try {
    await axios.post(`/api/seller/returns/${ret.id}/process`, { 
      action: 'approve' 
    })
    fetchReturns()
    fetchStats()
  } catch (error) {
    console.error('Onaylama başarısız:', error)
    alert('İade onaylanamadı. Lütfen tekrar deneyin.')
  }
}

const openRejectModal = (ret: ReturnItem) => {
  selectedReturn.value = ret
  rejectReason.value = ''
  showRejectModal.value = true
}

const submitReject = async () => {
  if (!selectedReturn.value || !rejectReason.value.trim()) return
  
  try {
    await axios.post(`/api/seller/returns/${selectedReturn.value.id}/process`, { 
      action: 'reject',
      reason: rejectReason.value
    })
    showRejectModal.value = false
    fetchReturns()
    fetchStats()
  } catch (error) {
    console.error('Red işlemi başarısız:', error)
    alert('İade reddedilemedi. Lütfen tekrar deneyin.')
  }
}

const openShippingCodeModal = (ret: ReturnItem) => {
  selectedReturn.value = ret
  shippingCodeForm.carrier = ''
  shippingCodeForm.code = ''
  showShippingCodeModal.value = true
}

const submitShippingCode = async () => {
  if (!selectedReturn.value || !shippingCodeForm.carrier || !shippingCodeForm.code.trim()) return
  
  try {
    await axios.post(`/api/seller/returns/${selectedReturn.value.id}/shipping-code`, { 
      return_shipping_code: shippingCodeForm.code,
      return_shipping_carrier: shippingCodeForm.carrier
    })
    showShippingCodeModal.value = false
    alert('Kargo kodu müşteriye gönderildi!')
    fetchReturns()
  } catch (error) {
    console.error('Kargo kodu gönderimi başarısız:', error)
    alert('Kargo kodu gönderilemedi. Lütfen tekrar deneyin.')
  }
}

const receiveItem = async (ret: ReturnItem) => {
  if (!confirm('Ürünü teslim aldığınızı onaylıyor musunuz?')) return
  
  try {
    await axios.post(`/api/seller/returns/${ret.id}/receive`)
    fetchReturns()
    fetchStats()
  } catch (error) {
    console.error('Teslim alma başarısız:', error)
    alert('İşlem başarısız. Lütfen tekrar deneyin.')
  }
}

const openImage = (url: string) => {
  window.open(url, '_blank')
}

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    pending: 'Beklemede',
    approved: 'Onaylandı',
    rejected: 'Reddedildi',
    shipped_back: 'Kargoya Verildi',
    received: 'Teslim Alındı',
    refunded: 'İade Edildi',
    cancelled: 'İptal Edildi'
  }
  return labels[status] || status
}

const getReasonLabel = (reason: string) => {
  const labels: Record<string, string> = {
    damaged: 'Hasarlı/Kusurlu Ürün',
    wrong_item: 'Yanlış Ürün Gönderildi',
    not_as_described: 'Açıklamaya Uygun Değil',
    changed_mind: 'Fikir Değişikliği',
    better_price: 'Daha İyi Fiyat Buldum',
    not_needed: 'Artık İhtiyacım Yok',
    other: 'Diğer'
  }
  return labels[reason] || reason
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY'
  }).format(price)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  fetchReturns()
  fetchStats()
})
</script>

<style scoped>
.seller-returns {
  padding: 24px;
}

.page-header {
  margin-bottom: 24px;
}

.page-header h1 {
  font-size: 24px;
  font-weight: 600;
  margin: 0 0 4px 0;
}

.text-muted {
  color: #6b7280;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.stat-card.pending .stat-icon {
  background: #fef3c7;
  color: #d97706;
}

.stat-card.approved .stat-icon {
  background: #dbeafe;
  color: #2563eb;
}

.stat-card.shipping .stat-icon {
  background: #e0e7ff;
  color: #4f46e5;
}

.stat-card.completed .stat-icon {
  background: #d1fae5;
  color: #059669;
}

.stat-content {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
}

.stat-label {
  font-size: 14px;
  color: #6b7280;
}

.filters-bar {
  display: flex;
  gap: 16px;
  margin-bottom: 24px;
  padding: 16px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.filter-group label {
  font-size: 12px;
  font-weight: 500;
  color: #6b7280;
}

.filter-group select,
.filter-group input {
  padding: 8px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  min-width: 180px;
}

.loading-state {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 48px;
  color: #6b7280;
}

.returns-table-wrapper {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  overflow: hidden;
}

.returns-table {
  width: 100%;
  border-collapse: collapse;
}

.returns-table th,
.returns-table td {
  padding: 12px 16px;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.returns-table th {
  background: #f9fafb;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  color: #6b7280;
}

.product-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.product-cell img {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  object-fit: cover;
}

.product-cell span {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.status-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.pending {
  background: #fef3c7;
  color: #d97706;
}

.status-badge.approved {
  background: #dbeafe;
  color: #2563eb;
}

.status-badge.rejected {
  background: #fee2e2;
  color: #dc2626;
}

.status-badge.shipped_back {
  background: #e0e7ff;
  color: #4f46e5;
}

.status-badge.received {
  background: #d1fae5;
  color: #059669;
}

.status-badge.refunded {
  background: #d1fae5;
  color: #059669;
}

.status-badge.cancelled {
  background: #f3f4f6;
  color: #6b7280;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-sm {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.btn-view {
  background: #f3f4f6;
  color: #374151;
}

.btn-view:hover {
  background: #e5e7eb;
}

.btn-approve {
  background: #d1fae5;
  color: #059669;
}

.btn-approve:hover {
  background: #a7f3d0;
}

.btn-reject {
  background: #fee2e2;
  color: #dc2626;
}

.btn-reject:hover {
  background: #fecaca;
}

.btn-shipping {
  background: #fef3c7;
  color: #d97706;
}

.btn-shipping:hover {
  background: #fde68a;
}

.btn-receive {
  background: #dbeafe;
  color: #2563eb;
}

.btn-receive:hover {
  background: #bfdbfe;
}

.form-select,
.form-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
}

.form-select:focus,
.form-input:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.info-box {
  display: flex;
  gap: 8px;
  padding: 12px;
  background: #eff6ff;
  border-radius: 8px;
  color: #1e40af;
  font-size: 13px;
  margin-top: 12px;
}

.info-box i {
  flex-shrink: 0;
}

.btn-primary {
  background: #6366f1;
  color: white;
}

.btn-primary:hover {
  background: #4f46e5;
}

.btn-primary i {
  margin-right: 6px;
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  padding: 16px;
}

.pagination button {
  width: 36px;
  height: 36px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: white;
  cursor: pointer;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.empty-state {
  text-align: center;
  padding: 64px 24px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.empty-state i {
  font-size: 48px;
  color: #d1d5db;
  margin-bottom: 16px;
}

.empty-state h3 {
  font-size: 18px;
  margin: 0 0 8px 0;
}

.empty-state p {
  color: #6b7280;
  margin: 0;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-content.modal-lg {
  max-width: 700px;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
}

.modal-close {
  width: 32px;
  height: 32px;
  border: none;
  background: #f3f4f6;
  border-radius: 8px;
  cursor: pointer;
}

.modal-body {
  padding: 24px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 16px 24px;
  border-top: 1px solid #e5e7eb;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-success {
  background: #059669;
  color: white;
}

.btn-danger {
  background: #dc2626;
  color: white;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
}

.form-group textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  resize: vertical;
}

.required {
  color: #dc2626;
}

.detail-grid {
  display: grid;
  gap: 24px;
}

.detail-section h4 {
  font-size: 14px;
  font-weight: 600;
  color: #6b7280;
  margin: 0 0 12px 0;
  text-transform: uppercase;
}

.detail-section p {
  margin: 8px 0;
}

.product-detail {
  display: flex;
  gap: 16px;
}

.product-detail img {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  object-fit: cover;
}

.image-gallery {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.image-gallery img {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  object-fit: cover;
  cursor: pointer;
  transition: transform 0.2s;
}

.image-gallery img:hover {
  transform: scale(1.05);
}

@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .filters-bar {
    flex-direction: column;
  }
  
  .returns-table-wrapper {
    overflow-x: auto;
  }
}
</style>
