<template>
  <div class="page-management">
    <!-- Header -->
    <div class="header">
      <div>
        <h1>📄 Sayfa Yönetimi</h1>
        <p class="subtitle">Statik sayfaları düzenleyin</p>
      </div>
      <button @click="openCreateModal" class="btn btn-primary">
        <span>➕</span> Yeni Sayfa
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-content">
          <div class="stat-label">Toplam Sayfa</div>
          <div class="stat-value">{{ stats.total }}</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon"><BadgeIcon name="check" cls="w-6 h-6 text-green-600" /></div>
        <div class="stat-content">
          <div class="stat-label">Yayında</div>
          <div class="stat-value">{{ stats.published }}</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-content">
          <div class="stat-label">Taslak</div>
          <div class="stat-value">{{ stats.draft }}</div>
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

    <!-- Quick Access - Default Pages -->
    <div class="quick-pages">
      <h3>⚡ Hızlı Erişim</h3>
      <div class="page-cards">
        <div 
          v-for="page in defaultPages" 
          :key="page.slug"
          class="page-card"
          @click="editPageBySlug(page.slug)"
        >
          <div class="page-card-icon">
            <BadgeIcon v-if="page.iconName" :name="page.iconName" cls="w-8 h-8" />
            <span v-else>{{ page.icon }}</span>
          </div>
          <div class="page-card-title">{{ page.title }}</div>
          <div class="page-card-status" :class="page.exists ? 'exists' : 'missing'">
            {{ page.exists ? '✓ Mevcut' : '+ Oluştur' }}
          </div>
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
          placeholder="Sayfa adı veya slug..."
          @input="loadPages"
        />
      </div>
      <div class="filter-group">
        <label>📊 Durum</label>
        <select v-model="filters.status" @change="loadPages">
          <option value="">Tümü</option>
          <option value="published">Yayında</option>
          <option value="draft">Taslak</option>
        </select>
      </div>
      <div class="filter-group">
        <label>📂 Tip</label>
        <select v-model="filters.type" @change="loadPages">
          <option value="">Tümü</option>
          <option value="system">Sistem</option>
          <option value="custom">Özel</option>
        </select>
      </div>
      <div class="filter-group">
        <label>📅 Sıralama</label>
        <select v-model="filters.sort_by" @change="loadPages">
          <option value="updated_desc">Son Güncelleme (Yeni → Eski)</option>
          <option value="updated_asc">Son Güncelleme (Eski → Yeni)</option>
          <option value="title_asc">Başlık (A → Z)</option>
          <option value="title_desc">Başlık (Z → A)</option>
          <option value="views_desc">Görüntüleme (Çok → Az)</option>
        </select>
      </div>
    </div>

    <!-- Pages Table -->
    <div class="table-container">
      <table v-if="!loading && pages.length > 0">
        <thead>
          <tr>
            <th>Sayfa Bilgileri</th>
            <th>Slug</th>
            <th>Tip</th>
            <th>Durum</th>
            <th>Görüntüleme</th>
            <th>Son Güncelleme</th>
            <th>İşlemler</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="page in pages" :key="page.id">
            <td>
              <div class="page-info">
                <strong>{{ page.title }}</strong>
                <div class="page-meta">
                  <span v-if="page.meta_title" class="meta">SEO: {{ page.meta_title }}</span>
                </div>
              </div>
            </td>
            <td>
              <code class="slug">{{ page.slug }}</code>
            </td>
            <td>
              <span class="type-badge" :class="page.type">
                {{ page.type === 'system' ? '🔒 Sistem' : '✏️ Özel' }}
              </span>
            </td>
            <td>
              <span class="status-badge flex items-center gap-1" :class="page.status">
                <BadgeIcon v-if="page.status === 'published'" name="check" cls="w-3 h-3" />
                <span v-else>📝</span>
                {{ page.status === 'published' ? 'Yayında' : 'Taslak' }}
              </span>
            </td>
            <td>
              <strong>{{ page.views.toLocaleString('tr-TR') }}</strong>
            </td>
            <td>
              <small>{{ formatDate(page.updated_at) }}</small>
            </td>
            <td>
              <div class="actions">
                <button 
                  @click="editPage(page)" 
                  class="btn-icon"
                  title="Düzenle"
                >
                  ✏️
                </button>
                <button 
                  @click="toggleStatus(page)" 
                  class="btn-icon"
                  :title="page.status === 'published' ? 'Taslağa Al' : 'Yayınla'"
                >
                  <span v-if="page.status === 'published'">📝</span>
                  <BadgeIcon v-else name="check" cls="w-4 h-4" />
                </button>
                <button 
                  v-if="page.type === 'custom'"
                  @click="deletePage(page)" 
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

      <div v-if="!loading && pages.length === 0" class="empty-state">
        <div class="empty-icon">📄</div>
        <h3>Sayfa Bulunamadı</h3>
        <p>Yeni bir sayfa oluşturun</p>
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
        ({{ pagination.total }} sayfa)
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
      <div class="modal large">
        <div class="modal-header">
          <h2>{{ isEditing ? '✏️ Sayfa Düzenle' : '➕ Yeni Sayfa' }}</h2>
          <button @click="closeModal" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="savePage">
            <!-- Basic Info -->
            <div class="form-section">
              <h3>📋 Temel Bilgiler</h3>
              <div class="form-row">
                <div class="form-group">
                  <label>Sayfa Başlığı *</label>
                  <input 
                    v-model="form.title" 
                    type="text" 
                    placeholder="Örn: Hakkımızda"
                    required
                    @input="generateSlug"
                  />
                </div>
                <div class="form-group">
                  <label>Slug *</label>
                  <input 
                    v-model="form.slug" 
                    type="text" 
                    placeholder="hakkimizda"
                    required
                  />
                  <small>URL'de görünecek</small>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="form-section">
              <h3>📝 İçerik</h3>
              <div class="form-row">
                <div class="form-group full">
                  <label>Sayfa İçeriği *</label>
                  <div class="editor-toolbar">
                    <button type="button" @click="insertFormat('**', '**')" title="Kalın">
                      <strong>B</strong>
                    </button>
                    <button type="button" @click="insertFormat('*', '*')" title="İtalik">
                      <em>I</em>
                    </button>
                    <button type="button" @click="insertFormat('### ', '')" title="Başlık">
                      H
                    </button>
                    <button type="button" @click="insertFormat('[', '](url)')" title="Link">
                      🔗
                    </button>
                    <button type="button" @click="insertFormat('\n- ', '')" title="Liste">
                      ☰
                    </button>
                    <button type="button" @click="insertFormat('\n> ', '')" title="Alıntı">
                      "
                    </button>
                  </div>
                  <textarea 
                    ref="contentEditor"
                    v-model="form.content" 
                    rows="15"
                    placeholder="Markdown formatında yazabilirsiniz..."
                    required
                  ></textarea>
                  <small>Markdown formatı desteklenir: **kalın**, *italik*, ### başlık</small>
                </div>
              </div>

              <!-- Preview -->
              <div class="form-row">
                <div class="form-group full">
                  <label>
                    <input v-model="showPreview" type="checkbox" />
                    Önizleme Göster
                  </label>
                  <div v-if="showPreview" class="content-preview" v-html="renderedContent"></div>
                </div>
              </div>
            </div>

            <!-- SEO Settings -->
            <div class="form-section">
              <h3>🔍 SEO Ayarları</h3>
              <div class="form-row">
                <div class="form-group full">
                  <label>Meta Başlık</label>
                  <input 
                    v-model="form.meta_title" 
                    type="text" 
                    placeholder="Arama motorlarında görünecek başlık"
                    maxlength="60"
                  />
                  <small>{{ form.meta_title.length }}/60 karakter</small>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group full">
                  <label>Meta Açıklama</label>
                  <textarea 
                    v-model="form.meta_description" 
                    rows="3"
                    placeholder="Arama motorlarında görünecek açıklama"
                    maxlength="160"
                  ></textarea>
                  <small>{{ form.meta_description.length }}/160 karakter</small>
                </div>
              </div>
            </div>

            <!-- Additional Settings -->
            <div class="form-section">
              <h3>⚙️ Ek Ayarlar</h3>
              <div class="form-row">
                <div class="form-group">
                  <label>Sayfa Tipi</label>
                  <select v-model="form.type" :disabled="isEditing && selectedPage?.type === 'system'">
                    <option value="custom">Özel Sayfa</option>
                    <option value="system">Sistem Sayfası</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Durum</label>
                  <select v-model="form.status">
                    <option value="draft">Taslak</option>
                    <option value="published">Yayında</option>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>
                    <input v-model="form.show_in_footer" type="checkbox" />
                    Footer'da Göster
                  </label>
                </div>
                <div class="form-group">
                  <label>
                    <input v-model="form.show_in_menu" type="checkbox" />
                    Menüde Göster
                  </label>
                </div>
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
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import axios from 'axios'
import { marked } from 'marked'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

interface Page {
  id: number
  title: string
  slug: string
  content: string
  type: 'system' | 'custom'
  status: 'published' | 'draft'
  meta_title: string | null
  meta_description: string | null
  show_in_footer: boolean
  show_in_menu: boolean
  views: number
  created_at: string
  updated_at: string
}

interface Stats {
  total: number
  published: number
  draft: number
  totalViews: number
}

interface DefaultPage {
  slug: string
  title: string
  icon: string
  iconName?: string
  exists: boolean
}

const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const showPreview = ref(false)
const selectedPage = ref<Page | null>(null)
const contentEditor = ref<HTMLTextAreaElement>()

const pages = ref<Page[]>([])
const stats = ref<Stats>({
  total: 0,
  published: 0,
  draft: 0,
  totalViews: 0
})

const defaultPages = ref<DefaultPage[]>([
  { slug: 'about', title: 'Hakkımızda', icon: 'ℹ️', exists: false },
  { slug: 'contact', title: 'İletişim', icon: '📧', exists: false },
  { slug: 'faq', title: 'Sıkça Sorulan Sorular', icon: '❓', exists: false },
  { slug: 'terms', title: 'Kullanım Koşulları', icon: '📜', exists: false },
  { slug: 'privacy', title: 'Gizlilik Politikası', icon: '🔒', exists: false },
  { slug: 'shipping', title: 'Kargo ve Teslimat', icon: '📦', iconName: 'box', exists: false },
  { slug: 'returns', title: 'İade ve Değişim', icon: '↩️', exists: false },
  { slug: 'payment', title: 'Ödeme Yöntemleri', icon: '💳', exists: false },
])

const filters = reactive({
  search: '',
  status: '',
  type: '',
  sort_by: 'updated_desc',
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
  slug: '',
  content: '',
  type: 'custom' as 'system' | 'custom',
  status: 'published' as 'published' | 'draft',
  meta_title: '',
  meta_description: '',
  show_in_footer: false,
  show_in_menu: false
})

const renderedContent = computed(() => {
  try {
    return marked(form.content)
  } catch (error) {
    return '<p>Önizleme hatası</p>'
  }
})

onMounted(() => {
  loadPages()
  loadStats()
  checkDefaultPages()
})

const loadPages = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.status) params.append('status', filters.status)
    if (filters.type) params.append('type', filters.type)
    if (filters.sort_by) params.append('sort_by', filters.sort_by)
    params.append('page', filters.page.toString())

    const response = await axios.get(`/api/admin/pages?${params}`)
    pages.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total
    }
  } catch (error) {
    console.error('Sayfa yükleme hatası:', error)
    alert('Sayfalar yüklenirken bir hata oluştu')
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    const response = await axios.get('/api/admin/pages/stats')
    stats.value = response.data
  } catch (error) {
    console.error('İstatistik yükleme hatası:', error)
  }
}

const checkDefaultPages = async () => {
  try {
    const response = await axios.get('/api/admin/pages?per_page=100')
    const existingSlugs = response.data.data.map((p: Page) => p.slug)
    
    defaultPages.value.forEach(page => {
      page.exists = existingSlugs.includes(page.slug)
    })
  } catch (error) {
    console.error('Default sayfa kontrolü hatası:', error)
  }
}

const openCreateModal = () => {
  resetForm()
  isEditing.value = false
  showModal.value = true
}

const editPage = (page: Page) => {
  Object.assign(form, {
    title: page.title,
    slug: page.slug,
    content: page.content,
    type: page.type,
    status: page.status,
    meta_title: page.meta_title || '',
    meta_description: page.meta_description || '',
    show_in_footer: page.show_in_footer,
    show_in_menu: page.show_in_menu
  })
  selectedPage.value = page
  isEditing.value = true
  showModal.value = true
}

const editPageBySlug = async (slug: string) => {
  try {
    const response = await axios.get(`/api/admin/pages/by-slug/${slug}`)
    if (response.data) {
      editPage(response.data)
    } else {
      // Create new page with default slug
      resetForm()
      form.slug = slug
      form.title = defaultPages.value.find(p => p.slug === slug)?.title || ''
      form.type = 'system'
      isEditing.value = false
      showModal.value = true
    }
  } catch (error: any) {
    if (error.response?.status === 404) {
      // Page doesn't exist, open create form
      resetForm()
      form.slug = slug
      form.title = defaultPages.value.find(p => p.slug === slug)?.title || ''
      form.type = 'system'
      isEditing.value = false
      showModal.value = true
    } else {
      console.error('Sayfa yükleme hatası:', error)
      alert('Sayfa yüklenirken bir hata oluştu')
    }
  }
}

const generateSlug = () => {
  if (!isEditing.value) {
    form.slug = form.title
      .toLowerCase()
      .replace(/ç/g, 'c')
      .replace(/ğ/g, 'g')
      .replace(/ı/g, 'i')
      .replace(/ö/g, 'o')
      .replace(/ş/g, 's')
      .replace(/ü/g, 'u')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '')
  }
}

const insertFormat = (before: string, after: string) => {
  const textarea = contentEditor.value
  if (!textarea) return

  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const selectedText = form.content.substring(start, end)
  const newText = before + selectedText + after

  form.content = 
    form.content.substring(0, start) + 
    newText + 
    form.content.substring(end)

  // Set cursor position
  setTimeout(() => {
    textarea.focus()
    const newCursorPos = start + before.length + selectedText.length
    textarea.setSelectionRange(newCursorPos, newCursorPos)
  }, 0)
}

const savePage = async () => {
  saving.value = true
  try {
    if (isEditing.value && selectedPage.value) {
      await axios.put(`/api/admin/pages/${selectedPage.value.id}`, form)
      alert('Sayfa güncellendi')
    } else {
      await axios.post('/api/admin/pages', form)
      alert('Sayfa oluşturuldu')
    }

    closeModal()
    loadPages()
    loadStats()
    checkDefaultPages()
  } catch (error: any) {
    console.error('Sayfa kaydetme hatası:', error)
    alert(error.response?.data?.message || 'Bir hata oluştu')
  } finally {
    saving.value = false
  }
}

const toggleStatus = async (page: Page) => {
  try {
    const newStatus = page.status === 'published' ? 'draft' : 'published'
    await axios.put(`/api/admin/pages/${page.id}`, { status: newStatus })
    page.status = newStatus
    loadStats()
  } catch (error) {
    console.error('Durum güncelleme hatası:', error)
    alert('Durum güncellenirken bir hata oluştu')
  }
}

const deletePage = async (page: Page) => {
  if (!confirm(`"${page.title}" sayfasını silmek istediğinize emin misiniz?`)) {
    return
  }

  try {
    await axios.delete(`/api/admin/pages/${page.id}`)
    alert('Sayfa silindi')
    loadPages()
    loadStats()
    checkDefaultPages()
  } catch (error) {
    console.error('Sayfa silme hatası:', error)
    alert('Sayfa silinirken bir hata oluştu')
  }
}

const closeModal = () => {
  showModal.value = false
  showPreview.value = false
  resetForm()
}

const resetForm = () => {
  form.title = ''
  form.slug = ''
  form.content = ''
  form.type = 'custom'
  form.status = 'published'
  form.meta_title = ''
  form.meta_description = ''
  form.show_in_footer = false
  form.show_in_menu = false
  selectedPage.value = null
}

const changePage = (page: number) => {
  filters.page = page
  loadPages()
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
.page-management {
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

.quick-pages {
  margin-bottom: 24px;
}

.quick-pages h3 {
  font-size: 18px;
  margin: 0 0 16px 0;
}

.page-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 12px;
}

.page-card {
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  padding: 16px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
}

.page-card:hover {
  border-color: #2563eb;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.page-card-icon {
  font-size: 32px;
  margin-bottom: 8px;
}

.page-card-title {
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 8px;
  color: #111;
}

.page-card-status {
  font-size: 12px;
  padding: 4px 8px;
  border-radius: 4px;
  display: inline-block;
}

.page-card-status.exists {
  background: #d1fae5;
  color: #065f46;
}

.page-card-status.missing {
  background: #fef3c7;
  color: #92400e;
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

.page-info strong {
  display: block;
  margin-bottom: 4px;
}

.page-meta {
  font-size: 12px;
  color: #6b7280;
}

.slug {
  font-family: 'Courier New', monospace;
  background: #f3f4f6;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 13px;
}

.type-badge,
.status-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.type-badge.system {
  background: #e0e7ff;
  color: #4338ca;
}

.type-badge.custom {
  background: #dbeafe;
  color: #1e40af;
}

.status-badge.published {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.draft {
  background: #fef3c7;
  color: #92400e;
}

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
  max-width: 900px;
  max-height: 90vh;
  overflow: auto;
}

.modal.large {
  max-width: 1200px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  background: white;
  z-index: 1;
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

.form-section {
  margin-bottom: 32px;
  padding-bottom: 32px;
  border-bottom: 1px solid #e5e7eb;
}

.form-section:last-of-type {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.form-section h3 {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 16px 0;
  color: #111;
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
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  font-family: inherit;
}

.form-group textarea {
  resize: vertical;
  font-family: 'Courier New', monospace;
}

.form-group small {
  display: block;
  margin-top: 4px;
  font-size: 12px;
  color: #6b7280;
}

.editor-toolbar {
  display: flex;
  gap: 4px;
  margin-bottom: 8px;
  padding: 8px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 6px 6px 0 0;
}

.editor-toolbar button {
  padding: 6px 12px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.editor-toolbar button:hover {
  background: #f3f4f6;
  border-color: #9ca3af;
}

.content-preview {
  margin-top: 16px;
  padding: 20px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  line-height: 1.6;
}

.content-preview :deep(h1),
.content-preview :deep(h2),
.content-preview :deep(h3) {
  margin-top: 1em;
  margin-bottom: 0.5em;
}

.content-preview :deep(p) {
  margin-bottom: 1em;
}

.content-preview :deep(ul),
.content-preview :deep(ol) {
  margin-left: 1.5em;
  margin-bottom: 1em;
}

.content-preview :deep(blockquote) {
  border-left: 4px solid #e5e7eb;
  padding-left: 1em;
  margin: 1em 0;
  color: #6b7280;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
  margin-top: 20px;
  position: sticky;
  bottom: 0;
  background: white;
}
</style>
