<template>
  <div class="category-management">
    <div class="page-header">
      <div class="header-left">
        <h1>📁 Kategori Yönetimi</h1>
        <p class="subtitle">Ürün kategorilerini yönetin</p>
      </div>
      <div class="header-actions">
        <button @click="refreshData" class="btn-refresh">🔄 Yenile</button>
        <button @click="openCreateModal" class="btn-create">➕ Yeni Kategori</button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-icon">📁</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.total }}</div>
          <div class="stat-label">Toplam Kategori</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><BadgeIcon name="check" cls="w-6 h-6 text-green-600" /></div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.active }}</div>
          <div class="stat-label">Aktif Kategori</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">🌳</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.parents }}</div>
          <div class="stat-label">Ana Kategori</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><BadgeIcon name="box" cls="w-6 h-6 text-blue-600" /></div>
        <div class="stat-content">
          <div class="stat-value">{{ formatNumber(stats.totalProducts) }}</div>
          <div class="stat-label">Toplam Ürün</div>
        </div>
      </div>
    </div>

    <!-- Categories Tree -->
    <div class="categories-container">
      <div class="tree-header">
        <h2>🌳 Kategori Ağacı</h2>
        <div class="view-toggle">
          <button 
            :class="['toggle-btn', { active: viewMode === 'tree' }]"
            @click="viewMode = 'tree'"
          >
            🌳 Ağaç
          </button>
          <button 
            :class="['toggle-btn', { active: viewMode === 'list' }]"
            @click="viewMode = 'list'"
          >
            📋 Liste
          </button>
        </div>
      </div>

      <!-- Tree View -->
      <div v-if="viewMode === 'tree'" class="tree-view">
        <div v-if="loading" class="loading-message">⏳ Yükleniyor...</div>
        <div v-else-if="categories.length === 0" class="empty-message">
          😕 Kategori bulunamadı
        </div>
        <div v-else class="category-tree">
          <CategoryNode
            v-for="category in rootCategories"
            :key="category.id"
            :category="category"
            :level="0"
            @edit="openEditModal"
            @delete="deleteCategory"
            @toggle-status="toggleStatus"
          />
        </div>
      </div>

      <!-- List View -->
      <div v-else class="list-view">
        <table class="categories-table">
          <thead>
            <tr>
              <th>Kategori</th>
              <th>Slug</th>
              <th>Üst Kategori</th>
              <th>Ürün Sayısı</th>
              <th>Durum</th>
              <th>Sıra</th>
              <th>İşlemler</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="7" class="loading-cell">⏳ Yükleniyor...</td>
            </tr>
            <tr v-else-if="flatCategories.length === 0">
              <td colspan="7" class="empty-cell">😕 Kategori bulunamadı</td>
            </tr>
            <tr v-else v-for="category in flatCategories" :key="category.id">
              <td>
                <div class="category-info">
                  <span v-if="category.icon" class="category-icon">{{ category.icon }}</span>
                  <span class="category-name">{{ category.name }}</span>
                </div>
              </td>
              <td>{{ category.slug }}</td>
              <td>{{ category.parent?.name || '-' }}</td>
              <td>
                <span class="badge-count">{{ category.products_count || 0 }}</span>
              </td>
              <td>
                <span :class="['status-badge', category.is_active ? 'active' : 'inactive']">
                  {{ category.is_active ? 'Aktif' : 'Pasif' }}
                </span>
              </td>
              <td>{{ category.order || 0 }}</td>
              <td>
                <div class="action-buttons">
                  <button @click="openEditModal(category)" class="btn-action btn-edit">✏️</button>
                  <button @click="toggleStatus(category)" class="btn-action btn-toggle">
                    {{ category.is_active ? '⏸️' : '▶️' }}
                  </button>
                  <button @click="deleteCategory(category)" class="btn-action btn-delete">🗑️</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ isEditing ? '✏️ Kategori Düzenle' : '➕ Yeni Kategori' }}</h2>
          <button @click="closeModal" class="btn-close">✕</button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Kategori Adı *</label>
            <input 
              v-model="form.name" 
              type="text" 
              placeholder="Örn: Spor Ayakkabıları"
              @input="generateSlug"
            />
          </div>

          <div class="form-group">
            <label>Slug *</label>
            <input 
              v-model="form.slug" 
              type="text" 
              placeholder="spor-ayakkabilari"
            />
            <small>URL'de kullanılacak benzersiz isim</small>
          </div>

          <div class="form-group">
            <label>Üst Kategori</label>
            <select v-model="form.parent_id">
              <option :value="null">Ana Kategori</option>
              <option 
                v-for="category in selectableCategories" 
                :key="category.id" 
                :value="category.id"
                :disabled="isEditing && category.id === form.id"
              >
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>İkon (Emoji)</label>
            <input 
              v-model="form.icon" 
              type="text" 
              placeholder="⚽"
              maxlength="2"
            />
          </div>

          <div class="form-group">
            <label>Açıklama</label>
            <textarea 
              v-model="form.description" 
              rows="3"
              placeholder="Kategori açıklaması..."
            ></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Sıra</label>
              <input 
                v-model.number="form.order" 
                type="number" 
                min="0"
              />
            </div>

            <div class="form-group">
              <label>Durum</label>
              <div class="toggle-switch">
                <input 
                  type="checkbox" 
                  v-model="form.is_active" 
                  id="is_active"
                />
                <label for="is_active">
                  {{ form.is_active ? 'Aktif' : 'Pasif' }}
                </label>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Meta Başlık (SEO)</label>
            <input 
              v-model="form.meta_title" 
              type="text" 
              placeholder="SEO için sayfa başlığı"
            />
          </div>

          <div class="form-group">
            <label>Meta Açıklama (SEO)</label>
            <textarea 
              v-model="form.meta_description" 
              rows="2"
              placeholder="SEO için sayfa açıklaması..."
            ></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="closeModal" class="btn-secondary">İptal</button>
          <button @click="saveCategory" class="btn-primary" :disabled="saving">
            {{ saving ? '⏳ Kaydediliyor...' : (isEditing ? '💾 Güncelle' : '➕ Oluştur') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'
import CategoryNode from './CategoryNode.vue'

interface Category {
  id: number
  name: string
  slug: string
  parent_id?: number | null
  icon?: string
  description?: string
  order?: number
  is_active: boolean
  meta_title?: string
  meta_description?: string
  products_count?: number
  parent?: Category
  children?: Category[]
}

const categories = ref<Category[]>([])
const loading = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const saving = ref(false)
const viewMode = ref<'tree' | 'list'>('tree')

const stats = ref({
  total: 0,
  active: 0,
  parents: 0,
  totalProducts: 0
})

const form = ref<Partial<Category>>({
  name: '',
  slug: '',
  parent_id: null,
  icon: '',
  description: '',
  order: 0,
  is_active: true,
  meta_title: '',
  meta_description: ''
})

const rootCategories = computed(() => {
  return categories.value.filter(cat => !cat.parent_id)
})

const flatCategories = computed(() => {
  return categories.value
})

const selectableCategories = computed(() => {
  return categories.value.filter(cat => !cat.parent_id || cat.id !== form.value.id)
})

const formatNumber = (num: number) => {
  return new Intl.NumberFormat('tr-TR').format(num)
}

const generateSlug = () => {
  if (!form.value.name) return
  
  const turkishMap: Record<string, string> = {
    'ç': 'c', 'Ç': 'c',
    'ğ': 'g', 'Ğ': 'g',
    'ı': 'i', 'İ': 'i',
    'ö': 'o', 'Ö': 'o',
    'ş': 's', 'Ş': 's',
    'ü': 'u', 'Ü': 'u'
  }
  
  let slug = form.value.name.toLowerCase()
  Object.keys(turkishMap).forEach(key => {
    slug = slug.replace(new RegExp(key, 'g'), turkishMap[key])
  })
  
  slug = slug
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim()
  
  form.value.slug = slug
}

const loadCategories = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/categories')
    categories.value = buildTree(data)
    loadStats()
  } catch (error) {
    console.error('Kategoriler yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const buildTree = (items: Category[]): Category[] => {
  const map = new Map<number, Category>()
  const roots: Category[] = []

  items.forEach(item => {
    map.set(item.id, { ...item, children: [] })
  })

  map.forEach(item => {
    if (item.parent_id) {
      const parent = map.get(item.parent_id)
      if (parent) {
        parent.children = parent.children || []
        parent.children.push(item)
      }
    } else {
      roots.push(item)
    }
  })

  return roots
}

const loadStats = async () => {
  try {
    const { data } = await axios.get('/api/admin/categories/stats')
    stats.value = data
  } catch (error) {
    console.error('İstatistikler yüklenemedi:', error)
  }
}

const openCreateModal = () => {
  isEditing.value = false
  form.value = {
    name: '',
    slug: '',
    parent_id: null,
    icon: '',
    description: '',
    order: 0,
    is_active: true,
    meta_title: '',
    meta_description: ''
  }
  showModal.value = true
}

const openEditModal = (category: Category) => {
  isEditing.value = true
  form.value = {
    id: category.id,
    name: category.name,
    slug: category.slug,
    parent_id: category.parent_id,
    icon: category.icon,
    description: category.description,
    order: category.order || 0,
    is_active: category.is_active,
    meta_title: category.meta_title,
    meta_description: category.meta_description
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  form.value = {}
}

const saveCategory = async () => {
  if (!form.value.name || !form.value.slug) {
    alert('Kategori adı ve slug zorunludur')
    return
  }

  saving.value = true
  try {
    if (isEditing.value) {
      await axios.put(`/api/admin/categories/${form.value.id}`, form.value)
    } else {
      await axios.post('/api/admin/categories', form.value)
    }
    
    closeModal()
    await loadCategories()
  } catch (error: any) {
    console.error('Kategori kaydedilemedi:', error)
    alert(error.response?.data?.message || 'Bir hata oluştu')
  } finally {
    saving.value = false
  }
}

const toggleStatus = async (category: Category) => {
  try {
    await axios.put(`/api/admin/categories/${category.id}`, {
      is_active: !category.is_active
    })
    await loadCategories()
  } catch (error) {
    console.error('Durum güncellenemedi:', error)
    alert('Bir hata oluştu')
  }
}

const deleteCategory = async (category: Category) => {
  if (category.products_count && category.products_count > 0) {
    alert('Bu kategoride ürün bulunduğu için silemezsiniz. Önce ürünleri taşıyın.')
    return
  }

  if (category.children && category.children.length > 0) {
    alert('Bu kategorinin alt kategorileri var. Önce alt kategorileri silin.')
    return
  }

  if (!confirm(`"${category.name}" kategorisini silmek istediğinizden emin misiniz?`)) {
    return
  }

  try {
    await axios.delete(`/api/admin/categories/${category.id}`)
    await loadCategories()
  } catch (error) {
    console.error('Kategori silinemedi:', error)
    alert('Bir hata oluştu')
  }
}

const refreshData = () => {
  loadCategories()
}

onMounted(() => {
  loadCategories()
})
</script>

<script lang="ts">
import { defineComponent, PropType } from 'vue'

export const CategoryNode = defineComponent({
  name: 'CategoryNode',
  props: {
    category: {
      type: Object as PropType<Category>,
      required: true
    },
    level: {
      type: Number,
      default: 0
    }
  },
  emits: ['edit', 'delete', 'toggle-status'],
  setup(props, { emit }) {
    const expanded = ref(true)

    const toggleExpand = () => {
      expanded.value = !expanded.value
    }

    return {
      expanded,
      toggleExpand
    }
  },
  template: `
    <div class="tree-node">
      <div class="node-content" :style="{ paddingLeft: (level * 2) + 'rem' }">
        <button 
          v-if="category.children && category.children.length > 0" 
          @click="toggleExpand"
          class="expand-btn"
        >
          {{ expanded ? '▼' : '▶' }}
        </button>
        <span v-else class="expand-spacer"></span>
        
        <div class="node-info">
          <span v-if="category.icon" class="node-icon">{{ category.icon }}</span>
          <span class="node-name">{{ category.name }}</span>
          <span class="node-count">({{ category.products_count || 0 }})</span>
          <span :class="['node-status', category.is_active ? 'active' : 'inactive']">
            {{ category.is_active ? '✓' : '✗' }}
          </span>
        </div>

        <div class="node-actions">
          <button @click="$emit('edit', category)" class="btn-action btn-edit">✏️</button>
          <button @click="$emit('toggle-status', category)" class="btn-action btn-toggle">
            {{ category.is_active ? '⏸️' : '▶️' }}
          </button>
          <button @click="$emit('delete', category)" class="btn-action btn-delete">🗑️</button>
        </div>
      </div>

      <div v-if="expanded && category.children && category.children.length > 0" class="node-children">
        <CategoryNode
          v-for="child in category.children"
          :key="child.id"
          :category="child"
          :level="level + 1"
          @edit="$emit('edit', $event)"
          @delete="$emit('delete', $event)"
          @toggle-status="$emit('toggle-status', $event)"
        />
      </div>
    </div>
  `
})
</script>

<style scoped>
.category-management {
  padding: 2rem;
  max-width: 1400px;
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
.btn-create {
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

.btn-create {
  background: #8b5cf6;
  color: white;
}

.btn-create:hover {
  background: #7c3aed;
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

.categories-container {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.tree-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.tree-header h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
}

.view-toggle {
  display: flex;
  gap: 0.5rem;
  background: #f3f4f6;
  padding: 0.25rem;
  border-radius: 8px;
}

.toggle-btn {
  padding: 0.5rem 1rem;
  border: none;
  background: transparent;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  color: #6b7280;
  transition: all 0.3s;
}

.toggle-btn.active {
  background: white;
  color: #8b5cf6;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.loading-message,
.empty-message {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
  font-size: 1rem;
}

.tree-node {
  border-left: 2px solid #e5e7eb;
  margin-left: 1rem;
}

.node-content {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #f3f4f6;
  transition: background 0.3s;
}

.node-content:hover {
  background: #f9fafb;
}

.expand-btn {
  width: 24px;
  height: 24px;
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 0.875rem;
  color: #6b7280;
}

.expand-spacer {
  width: 24px;
}

.node-info {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.node-icon {
  font-size: 1.5rem;
}

.node-name {
  font-weight: 500;
  color: #111827;
}

.node-count {
  font-size: 0.875rem;
  color: #6b7280;
}

.node-status {
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
}

.node-status.active {
  background: #d1fae5;
  color: #065f46;
}

.node-status.inactive {
  background: #fee2e2;
  color: #991b1b;
}

.node-actions {
  display: flex;
  gap: 0.5rem;
}

.categories-table {
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

.loading-cell,
.empty-cell {
  text-align: center;
  padding: 3rem !important;
  color: #6b7280;
}

.category-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.category-icon {
  font-size: 1.5rem;
}

.category-name {
  font-weight: 500;
  color: #111827;
}

.status-badge {
  display: inline-block;
  padding: 0.375rem 0.875rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.status-badge.active {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.inactive {
  background: #fee2e2;
  color: #991b1b;
}

.badge-count {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background: #f3f4f6;
  border-radius: 12px;
  font-weight: 500;
  font-size: 0.875rem;
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

.btn-edit {
  background: #fef3c7;
}

.btn-edit:hover {
  background: #fde68a;
}

.btn-toggle {
  background: #dbeafe;
}

.btn-toggle:hover {
  background: #bfdbfe;
}

.btn-delete {
  background: #fee2e2;
}

.btn-delete:hover {
  background: #fecaca;
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
  max-width: 600px;
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

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #8b5cf6;
}

.form-group small {
  display: block;
  margin-top: 0.25rem;
  font-size: 0.75rem;
  color: #6b7280;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.toggle-switch {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.toggle-switch input[type="checkbox"] {
  width: 48px;
  height: 24px;
  appearance: none;
  background: #e5e7eb;
  border-radius: 12px;
  position: relative;
  cursor: pointer;
  transition: background 0.3s;
}

.toggle-switch input[type="checkbox"]:checked {
  background: #10b981;
}

.toggle-switch input[type="checkbox"]::before {
  content: '';
  position: absolute;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: white;
  top: 2px;
  left: 2px;
  transition: transform 0.3s;
}

.toggle-switch input[type="checkbox"]:checked::before {
  transform: translateX(24px);
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

.btn-primary:hover:not(:disabled) {
  background: #7c3aed;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .category-management {
    padding: 1rem;
  }

  .page-header {
    flex-direction: column;
    gap: 1rem;
  }

  .stats-cards {
    grid-template-columns: 1fr;
  }

  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
