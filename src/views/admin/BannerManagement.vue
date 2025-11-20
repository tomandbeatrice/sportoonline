<template>
  <div class="banner-management">
    <!-- Header -->
    <div class="header">
      <div>
        <h1>🎨 Banner Yönetimi</h1>
        <p class="subtitle">Site içi banner'ları yönetin</p>
      </div>
      <button @click="openCreateModal" class="btn btn-primary">
        <span>➕</span> Yeni Banner
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-content">
          <div class="stat-label">Toplam Banner</div>
          <div class="stat-value">{{ stats.total }}</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
          <div class="stat-label">Aktif</div>
          <div class="stat-value">{{ stats.active }}</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">⏰</div>
        <div class="stat-content">
          <div class="stat-label">Zamanlanmış</div>
          <div class="stat-value">{{ stats.scheduled }}</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👁️</div>
        <div class="stat-content">
          <div class="stat-label">Toplam Görüntüleme</div>
          <div class="stat-value">{{ stats.totalViews.toLocaleString('tr-TR') }}</div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters">
      <div class="filter-group">
        <label>🔍 Ara</label>
        <input 
          v-model="filters.search" 
          type="text" 
          placeholder="Banner adı..."
          @input="loadBanners"
        />
      </div>
      <div class="filter-group">
        <label>📍 Pozisyon</label>
        <select v-model="filters.position" @change="loadBanners">
          <option value="">Tümü</option>
          <option value="home_top">Ana Sayfa Üst</option>
          <option value="home_middle">Ana Sayfa Orta</option>
          <option value="home_bottom">Ana Sayfa Alt</option>
          <option value="sidebar">Kenar Çubuğu</option>
          <option value="category">Kategori Sayfası</option>
          <option value="product">Ürün Detay</option>
        </select>
      </div>
      <div class="filter-group">
        <label>📊 Durum</label>
        <select v-model="filters.status" @change="loadBanners">
          <option value="">Tümü</option>
          <option value="active">Aktif</option>
          <option value="scheduled">Zamanlanmış</option>
          <option value="expired">Süresi Dolmuş</option>
          <option value="inactive">Pasif</option>
        </select>
      </div>
      <div class="filter-group">
        <label>📅 Sıralama</label>
        <select v-model="filters.sort_by" @change="loadBanners">
          <option value="order_asc">Sıra (Artan)</option>
          <option value="order_desc">Sıra (Azalan)</option>
          <option value="views_desc">Görüntüleme (Çok → Az)</option>
          <option value="views_asc">Görüntüleme (Az → Çok)</option>
          <option value="created_desc">Yeni → Eski</option>
          <option value="created_asc">Eski → Yeni</option>
        </select>
      </div>
    </div>

    <!-- Banners Table -->
    <div class="table-container">
      <table v-if="!loading && banners.length > 0">
        <thead>
          <tr>
            <th>Önizleme</th>
            <th>Banner Bilgileri</th>
            <th>Pozisyon</th>
            <th>Zamanlama</th>
            <th>Görüntüleme</th>
            <th>Tıklama</th>
            <th>CTR</th>
            <th>Durum</th>
            <th>İşlemler</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="banner in banners" :key="banner.id">
            <td>
              <img 
                :src="banner.image_url" 
                :alt="banner.title"
                class="banner-preview"
              />
            </td>
            <td>
              <div class="banner-info">
                <strong>{{ banner.title }}</strong>
                <div class="banner-meta">
                  <span v-if="banner.link_url" class="link">🔗 {{ banner.link_url }}</span>
                  <span class="order">📊 Sıra: {{ banner.order }}</span>
                </div>
              </div>
            </td>
            <td>
              <span class="position-badge" :class="banner.position">
                {{ getPositionLabel(banner.position) }}
              </span>
            </td>
            <td>
              <div class="schedule">
                <div v-if="banner.start_date">
                  <small>Başlangıç</small>
                  <div>{{ formatDate(banner.start_date) }}</div>
                </div>
                <div v-if="banner.end_date">
                  <small>Bitiş</small>
                  <div>{{ formatDate(banner.end_date) }}</div>
                </div>
                <div v-if="!banner.start_date && !banner.end_date" class="no-schedule">
                  ∞ Süresiz
                </div>
              </div>
            </td>
            <td>
              <strong>{{ banner.views.toLocaleString('tr-TR') }}</strong>
            </td>
            <td>
              <strong>{{ banner.clicks.toLocaleString('tr-TR') }}</strong>
            </td>
            <td>
              <span class="ctr" :class="getCTRClass(banner.ctr)">
                {{ banner.ctr.toFixed(2) }}%
              </span>
            </td>
            <td>
              <span 
                class="status-badge" 
                :class="getStatusClass(banner)"
              >
                {{ getStatusLabel(banner) }}
              </span>
            </td>
            <td>
              <div class="actions">
                <button 
                  @click="toggleStatus(banner)" 
                  class="btn-icon"
                  :title="banner.is_active ? 'Pasife Al' : 'Aktif Et'"
                >
                  {{ banner.is_active ? '⏸️' : '▶️' }}
                </button>
                <button 
                  @click="editBanner(banner)" 
                  class="btn-icon"
                  title="Düzenle"
                >
                  ✏️
                </button>
                <button 
                  @click="viewStats(banner)" 
                  class="btn-icon"
                  title="İstatistikler"
                >
                  📊
                </button>
                <button 
                  @click="deleteBanner(banner)" 
                  class="btn-icon danger"
                  title="Sil"
                >
                  🗑️
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Yükleniyor...</p>
      </div>

      <div v-if="!loading && banners.length === 0" class="empty-state">
        <div class="empty-icon">🎨</div>
        <h3>Banner Bulunamadı</h3>
        <p>Yeni bir banner oluşturun</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > pagination.per_page" class="pagination">
      <button 
        @click="changePage(pagination.current_page - 1)"
        :disabled="pagination.current_page === 1"
        class="btn-page"
      >
        ← Önceki
      </button>
      <span class="page-info">
        Sayfa {{ pagination.current_page }} / {{ pagination.last_page }}
        ({{ pagination.total }} banner)
      </span>
      <button 
        @click="changePage(pagination.current_page + 1)"
        :disabled="pagination.current_page === pagination.last_page"
        class="btn-page"
      >
        Sonraki →
      </button>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h2>{{ isEditing ? '✏️ Banner Düzenle' : '➕ Yeni Banner' }}</h2>
          <button @click="closeModal" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveBanner">
            <div class="form-row">
              <div class="form-group full">
                <label>Banner Başlığı *</label>
                <input 
                  v-model="form.title" 
                  type="text" 
                  placeholder="Örn: Yaz İndirimleri 2025"
                  required
                />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Pozisyon *</label>
                <select v-model="form.position" required>
                  <option value="">Seçiniz</option>
                  <option value="home_top">Ana Sayfa Üst</option>
                  <option value="home_middle">Ana Sayfa Orta</option>
                  <option value="home_bottom">Ana Sayfa Alt</option>
                  <option value="sidebar">Kenar Çubuğu</option>
                  <option value="category">Kategori Sayfası</option>
                  <option value="product">Ürün Detay</option>
                </select>
              </div>
              <div class="form-group">
                <label>Sıra *</label>
                <input 
                  v-model.number="form.order" 
                  type="number" 
                  min="0"
                  placeholder="0"
                  required
                />
                <small>Küçük değerler önce gösterilir</small>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group full">
                <label>Banner Görseli *</label>
                <div class="image-upload">
                  <input 
                    type="file" 
                    ref="imageInput"
                    accept="image/*"
                    @change="handleImageUpload"
                    :required="!isEditing && !form.image_url"
                  />
                  <div v-if="imagePreview || form.image_url" class="image-preview">
                    <img :src="imagePreview || form.image_url" alt="Preview" />
                    <button 
                      type="button" 
                      @click="removeImage" 
                      class="btn-remove-image"
                    >
                      ✕
                    </button>
                  </div>
                  <div v-else class="upload-placeholder">
                    <span class="upload-icon">📤</span>
                    <p>Görsel yüklemek için tıklayın</p>
                    <small>PNG, JPG, GIF - Max 2MB</small>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group full">
                <label>Link URL</label>
                <input 
                  v-model="form.link_url" 
                  type="url" 
                  placeholder="https://..."
                />
                <small>Banner'a tıklandığında yönlendirilecek adres</small>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>
                  <input 
                    v-model="form.open_new_tab" 
                    type="checkbox"
                  />
                  Yeni sekmede aç
                </label>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Başlangıç Tarihi</label>
                <input 
                  v-model="form.start_date" 
                  type="datetime-local"
                />
                <small>Boş bırakılırsa hemen yayınlanır</small>
              </div>
              <div class="form-group">
                <label>Bitiş Tarihi</label>
                <input 
                  v-model="form.end_date" 
                  type="datetime-local"
                />
                <small>Boş bırakılırsa süresiz yayınlanır</small>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group full">
                <label>Açıklama</label>
                <textarea 
                  v-model="form.description" 
                  rows="3"
                  placeholder="Banner hakkında notlar (isteğe bağlı)"
                ></textarea>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>
                  <input 
                    v-model="form.is_active" 
                    type="checkbox"
                  />
                  Aktif
                </label>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" @click="closeModal" class="btn btn-secondary">
                İptal
              </button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Kaydediliyor...' : (isEditing ? 'Güncelle' : 'Oluştur') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Stats Modal -->
    <div v-if="showStatsModal" class="modal-overlay" @click.self="showStatsModal = false">
      <div class="modal">
        <div class="modal-header">
          <h2>📊 Banner İstatistikleri</h2>
          <button @click="showStatsModal = false" class="btn-close">✕</button>
        </div>
        <div class="modal-body" v-if="selectedBanner">
          <div class="stats-detail">
            <h3>{{ selectedBanner.title }}</h3>
            
            <div class="stats-grid">
              <div class="stat-box">
                <div class="stat-label">Toplam Görüntüleme</div>
                <div class="stat-value">{{ selectedBanner.views.toLocaleString('tr-TR') }}</div>
              </div>
              <div class="stat-box">
                <div class="stat-label">Toplam Tıklama</div>
                <div class="stat-value">{{ selectedBanner.clicks.toLocaleString('tr-TR') }}</div>
              </div>
              <div class="stat-box">
                <div class="stat-label">CTR (Tıklama Oranı)</div>
                <div class="stat-value">{{ selectedBanner.ctr.toFixed(2) }}%</div>
              </div>
              <div class="stat-box">
                <div class="stat-label">Pozisyon</div>
                <div class="stat-value">{{ getPositionLabel(selectedBanner.position) }}</div>
              </div>
            </div>

            <div class="stat-row">
              <strong>Oluşturulma:</strong>
              <span>{{ formatDate(selectedBanner.created_at) }}</span>
            </div>
            <div class="stat-row" v-if="selectedBanner.start_date">
              <strong>Başlangıç:</strong>
              <span>{{ formatDate(selectedBanner.start_date) }}</span>
            </div>
            <div class="stat-row" v-if="selectedBanner.end_date">
              <strong>Bitiş:</strong>
              <span>{{ formatDate(selectedBanner.end_date) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import axios from 'axios'

interface Banner {
  id: number
  title: string
  image_url: string
  link_url: string | null
  open_new_tab: boolean
  position: string
  order: number
  start_date: string | null
  end_date: string | null
  description: string | null
  is_active: boolean
  views: number
  clicks: number
  ctr: number
  created_at: string
  updated_at: string
}

interface Stats {
  total: number
  active: number
  scheduled: number
  totalViews: number
}

const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const showStatsModal = ref(false)
const isEditing = ref(false)
const selectedBanner = ref<Banner | null>(null)
const imageInput = ref<HTMLInputElement>()
const imagePreview = ref<string>('')
const imageFile = ref<File | null>(null)

const banners = ref<Banner[]>([])
const stats = ref<Stats>({
  total: 0,
  active: 0,
  scheduled: 0,
  totalViews: 0
})

const filters = reactive({
  search: '',
  position: '',
  status: '',
  sort_by: 'order_asc',
  page: 1
})

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 20,
  total: 0
})

const form = reactive({
  title: '',
  position: '',
  order: 0,
  image_url: '',
  link_url: '',
  open_new_tab: true,
  start_date: '',
  end_date: '',
  description: '',
  is_active: true
})

onMounted(() => {
  loadBanners()
  loadStats()
})

const loadBanners = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.position) params.append('position', filters.position)
    if (filters.status) params.append('status', filters.status)
    if (filters.sort_by) params.append('sort_by', filters.sort_by)
    params.append('page', filters.page.toString())

    const response = await axios.get(`/api/admin/banners?${params}`)
    banners.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total
    }
  } catch (error) {
    console.error('Banner yükleme hatası:', error)
    alert('Banner\'lar yüklenirken bir hata oluştu')
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    const response = await axios.get('/api/admin/banners/stats')
    stats.value = response.data
  } catch (error) {
    console.error('İstatistik yükleme hatası:', error)
  }
}

const openCreateModal = () => {
  resetForm()
  isEditing.value = false
  showModal.value = true
}

const editBanner = (banner: Banner) => {
  Object.assign(form, {
    title: banner.title,
    position: banner.position,
    order: banner.order,
    image_url: banner.image_url,
    link_url: banner.link_url || '',
    open_new_tab: banner.open_new_tab,
    start_date: banner.start_date ? banner.start_date.slice(0, 16) : '',
    end_date: banner.end_date ? banner.end_date.slice(0, 16) : '',
    description: banner.description || '',
    is_active: banner.is_active
  })
  selectedBanner.value = banner
  isEditing.value = true
  showModal.value = true
}

const handleImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    // Validate file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
      alert('Dosya boyutu en fazla 2MB olmalıdır')
      return
    }

    // Validate file type
    if (!file.type.startsWith('image/')) {
      alert('Sadece resim dosyaları yüklenebilir')
      return
    }

    imageFile.value = file
    
    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

const removeImage = () => {
  imagePreview.value = ''
  imageFile.value = null
  form.image_url = ''
  if (imageInput.value) {
    imageInput.value.value = ''
  }
}

const saveBanner = async () => {
  saving.value = true
  try {
    const formData = new FormData()
    
    // Add all form fields
    Object.entries(form).forEach(([key, value]) => {
      if (value !== null && value !== '') {
        formData.append(key, value.toString())
      }
    })

    // Add image file if selected
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }

    if (isEditing.value && selectedBanner.value) {
      formData.append('_method', 'PUT')
      await axios.post(`/api/admin/banners/${selectedBanner.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      alert('Banner güncellendi')
    } else {
      await axios.post('/api/admin/banners', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      alert('Banner oluşturuldu')
    }

    closeModal()
    loadBanners()
    loadStats()
  } catch (error: any) {
    console.error('Banner kaydetme hatası:', error)
    alert(error.response?.data?.message || 'Bir hata oluştu')
  } finally {
    saving.value = false
  }
}

const toggleStatus = async (banner: Banner) => {
  try {
    await axios.put(`/api/admin/banners/${banner.id}`, {
      is_active: !banner.is_active
    })
    banner.is_active = !banner.is_active
    loadStats()
  } catch (error) {
    console.error('Durum güncelleme hatası:', error)
    alert('Durum güncellenirken bir hata oluştu')
  }
}

const deleteBanner = async (banner: Banner) => {
  if (!confirm(`"${banner.title}" banner'ını silmek istediğinize emin misiniz?`)) {
    return
  }

  try {
    await axios.delete(`/api/admin/banners/${banner.id}`)
    alert('Banner silindi')
    loadBanners()
    loadStats()
  } catch (error) {
    console.error('Banner silme hatası:', error)
    alert('Banner silinirken bir hata oluştu')
  }
}

const viewStats = (banner: Banner) => {
  selectedBanner.value = banner
  showStatsModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const resetForm = () => {
  form.title = ''
  form.position = ''
  form.order = 0
  form.image_url = ''
  form.link_url = ''
  form.open_new_tab = true
  form.start_date = ''
  form.end_date = ''
  form.description = ''
  form.is_active = true
  imagePreview.value = ''
  imageFile.value = null
  selectedBanner.value = null
}

const changePage = (page: number) => {
  filters.page = page
  loadBanners()
}

const getPositionLabel = (position: string): string => {
  const labels: Record<string, string> = {
    home_top: 'Ana Sayfa Üst',
    home_middle: 'Ana Sayfa Orta',
    home_bottom: 'Ana Sayfa Alt',
    sidebar: 'Kenar Çubuğu',
    category: 'Kategori Sayfası',
    product: 'Ürün Detay'
  }
  return labels[position] || position
}

const getStatusLabel = (banner: Banner): string => {
  const now = new Date()
  const start = banner.start_date ? new Date(banner.start_date) : null
  const end = banner.end_date ? new Date(banner.end_date) : null

  if (!banner.is_active) return 'Pasif'
  if (end && end < now) return 'Süresi Dolmuş'
  if (start && start > now) return 'Zamanlanmış'
  return 'Aktif'
}

const getStatusClass = (banner: Banner): string => {
  const status = getStatusLabel(banner)
  return {
    'Aktif': 'active',
    'Zamanlanmış': 'scheduled',
    'Süresi Dolmuş': 'expired',
    'Pasif': 'inactive'
  }[status] || ''
}

const getCTRClass = (ctr: number): string => {
  if (ctr >= 5) return 'excellent'
  if (ctr >= 2) return 'good'
  if (ctr >= 1) return 'average'
  return 'low'
}

const formatDate = (date: string): string => {
  return new Date(date).toLocaleString('tr-TR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<style scoped>
.banner-management {
  padding: 24px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.header h1 {
  font-size: 28px;
  font-weight: 600;
  margin: 0 0 4px 0;
}

.subtitle {
  color: #666;
  margin: 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-icon {
  font-size: 32px;
  width: 56px;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  border-radius: 8px;
}

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 14px;
  color: #666;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 24px;
  font-weight: 600;
  color: #111;
}

.filters {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
  padding: 20px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
}

.filter-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 6px;
  color: #374151;
}

.filter-group input,
.filter-group select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

.table-container {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

th {
  padding: 12px 16px;
  text-align: left;
  font-weight: 600;
  font-size: 13px;
  color: #374151;
}

td {
  padding: 12px 16px;
  border-bottom: 1px solid #f3f4f6;
}

tbody tr:hover {
  background: #f9fafb;
}

.banner-preview {
  width: 120px;
  height: 60px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
}

.banner-info strong {
  display: block;
  margin-bottom: 4px;
}

.banner-meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
  font-size: 12px;
  color: #6b7280;
}

.position-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.position-badge.home_top { background: #dbeafe; color: #1e40af; }
.position-badge.home_middle { background: #e0e7ff; color: #4338ca; }
.position-badge.home_bottom { background: #ddd6fe; color: #6d28d9; }
.position-badge.sidebar { background: #fce7f3; color: #be185d; }
.position-badge.category { background: #fef3c7; color: #92400e; }
.position-badge.product { background: #d1fae5; color: #065f46; }

.schedule {
  font-size: 12px;
}

.schedule > div {
  margin-bottom: 4px;
}

.schedule small {
  color: #6b7280;
  display: block;
}

.no-schedule {
  color: #6b7280;
  font-style: italic;
}

.ctr {
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 4px;
}

.ctr.excellent { background: #d1fae5; color: #065f46; }
.ctr.good { background: #dbeafe; color: #1e40af; }
.ctr.average { background: #fef3c7; color: #92400e; }
.ctr.low { background: #fee2e2; color: #991b1b; }

.status-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.active { background: #d1fae5; color: #065f46; }
.status-badge.scheduled { background: #dbeafe; color: #1e40af; }
.status-badge.expired { background: #fee2e2; color: #991b1b; }
.status-badge.inactive { background: #f3f4f6; color: #6b7280; }

.actions {
  display: flex;
  gap: 8px;
}

.btn-icon {
  padding: 6px 10px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.btn-icon:hover {
  background: #f3f4f6;
  border-color: #9ca3af;
}

.btn-icon.danger:hover {
  background: #fee2e2;
  border-color: #fca5a5;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-primary {
  background: #2563eb;
  color: white;
}

.btn-primary:hover {
  background: #1d4ed8;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
  margin-top: 24px;
  padding: 16px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
}

.btn-page {
  padding: 8px 16px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.btn-page:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 14px;
  color: #6b7280;
}

.loading,
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #f3f4f6;
  border-top-color: #2563eb;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.empty-icon {
  font-size: 64px;
  margin-bottom: 16px;
}

.empty-state h3 {
  margin: 0 0 8px 0;
  font-size: 20px;
}

.empty-state p {
  color: #6b7280;
  margin: 0;
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
  padding: 20px;
}

.modal {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 700px;
  max-height: 90vh;
  overflow: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  margin: 0;
  font-size: 20px;
}

.btn-close {
  padding: 4px 8px;
  border: none;
  background: none;
  font-size: 24px;
  cursor: pointer;
  color: #6b7280;
}

.btn-close:hover {
  color: #111;
}

.modal-body {
  padding: 24px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}

.form-group.full {
  grid-column: 1 / -1;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 6px;
  color: #374151;
}

.form-group input[type="text"],
.form-group input[type="url"],
.form-group input[type="number"],
.form-group input[type="datetime-local"],
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

.form-group small {
  display: block;
  margin-top: 4px;
  font-size: 12px;
  color: #6b7280;
}

.image-upload {
  border: 2px dashed #d1d5db;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
}

.image-upload input[type="file"] {
  display: none;
}

.upload-placeholder {
  cursor: pointer;
}

.upload-icon {
  font-size: 48px;
  display: block;
  margin-bottom: 12px;
}

.image-preview {
  position: relative;
  display: inline-block;
}

.image-preview img {
  max-width: 100%;
  max-height: 300px;
  border-radius: 8px;
}

.btn-remove-image {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: none;
  background: rgba(0, 0, 0, 0.6);
  color: white;
  cursor: pointer;
  font-size: 18px;
}

.btn-remove-image:hover {
  background: rgba(0, 0, 0, 0.8);
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
  margin-top: 20px;
}

.stats-detail h3 {
  margin: 0 0 20px 0;
  font-size: 18px;
}

.stat-box {
  background: #f9fafb;
  padding: 16px;
  border-radius: 8px;
  text-align: center;
}

.stat-box .stat-label {
  font-size: 13px;
  color: #6b7280;
  margin-bottom: 8px;
}

.stat-box .stat-value {
  font-size: 24px;
  font-weight: 600;
  color: #111;
}

.stat-row {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid #f3f4f6;
}

.stat-row:last-child {
  border-bottom: none;
}
</style>
