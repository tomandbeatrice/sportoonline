<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Blog Yönetimi</h1>
        <p class="text-gray-600">Blog yazıları ve kategorileri yönetin</p>
      </div>
      <button
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center"
      >
        <span class="mr-2">+</span>
        Yeni Yazı
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Toplam Yazı</div>
        <div class="text-2xl font-bold text-gray-900">{{ stats.total_posts || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Yayında</div>
        <div class="text-2xl font-bold text-green-600">{{ stats.published_posts || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Taslak</div>
        <div class="text-2xl font-bold text-yellow-600">{{ stats.draft_posts || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Toplam Görüntülenme</div>
        <div class="text-2xl font-bold text-blue-600">{{ stats.total_views?.toLocaleString() || 0 }}</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Yazı ara..."
          class="border rounded-lg px-4 py-2"
        />
        <select v-model="filters.status" class="border rounded-lg px-4 py-2">
          <option value="">Tüm Durumlar</option>
          <option value="published">Yayında</option>
          <option value="draft">Taslak</option>
          <option value="scheduled">Zamanlanmış</option>
        </select>
        <select v-model="filters.category_id" class="border rounded-lg px-4 py-2">
          <option value="">Tüm Kategoriler</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <button @click="fetchPosts" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">
          🔍 Filtrele
        </button>
      </div>
    </div>

    <!-- Posts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yazı</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yazar</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Görüntülenme</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="post in posts" :key="post.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center">
                <img
                  :src="post.featured_image || '/images/default-blog.jpg'"
                  :alt="post.title"
                  class="w-16 h-10 rounded object-cover"
                />
                <div class="ml-3">
                  <div class="font-medium text-gray-900 line-clamp-1">{{ post.title }}</div>
                  <div class="text-xs text-gray-500">{{ post.slug }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ post.category?.name || '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ post.author?.name || '-' }}</td>
            <td class="px-6 py-4">
              <span
                :class="getStatusClass(post.status)"
                class="px-2 py-1 text-xs rounded-full"
              >
                {{ getStatusLabel(post.status) }}
              </span>
              <span v-if="post.is_featured" class="ml-1 text-yellow-500">★</span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ post.views?.toLocaleString() || 0 }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(post.published_at) }}</td>
            <td class="px-6 py-4 text-right text-sm space-x-2">
              <button @click="toggleFeatured(post)" class="text-yellow-600 hover:text-yellow-800">
                {{ post.is_featured ? '★' : '☆' }}
              </button>
              <button @click="editPost(post)" class="text-green-600 hover:text-green-800">
                Düzenle
              </button>
              <button
                v-if="post.status === 'draft'"
                @click="publishPost(post)"
                class="text-blue-600 hover:text-blue-800"
              >
                Yayınla
              </button>
              <button
                v-if="post.status === 'published'"
                @click="unpublishPost(post)"
                class="text-orange-600 hover:text-orange-800"
              >
                Geri Al
              </button>
              <button @click="deletePost(post)" class="text-red-600 hover:text-red-800">
                Sil
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="bg-gray-50 px-6 py-3 flex justify-between items-center">
        <span class="text-sm text-gray-500">
          Toplam {{ pagination.total }} yazı
        </span>
        <div class="flex space-x-2">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1 border rounded disabled:opacity-50"
          >
            Önceki
          </button>
          <span class="px-3 py-1">{{ pagination.current_page }} / {{ pagination.last_page }}</span>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1 border rounded disabled:opacity-50"
          >
            Sonraki
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b flex justify-between items-center">
          <h2 class="text-xl font-bold">{{ isEditing ? 'Yazı Düzenle' : 'Yeni Yazı' }}</h2>
          <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <form @submit.prevent="savePost" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Başlık</label>
            <input v-model="form.title" type="text" required class="w-full border rounded-lg px-4 py-2 text-lg" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
              <input v-model="form.slug" type="text" class="w-full border rounded-lg px-4 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
              <select v-model="form.category_id" class="w-full border rounded-lg px-4 py-2">
                <option value="">Kategori Seçin</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Özet</label>
            <textarea v-model="form.excerpt" rows="2" class="w-full border rounded-lg px-4 py-2" placeholder="Kısa açıklama..."></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">İçerik</label>
            <textarea v-model="form.content" rows="10" class="w-full border rounded-lg px-4 py-2" placeholder="Blog içeriği..."></textarea>
          </div>
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
              <select v-model="form.status" class="w-full border rounded-lg px-4 py-2">
                <option value="draft">Taslak</option>
                <option value="published">Yayında</option>
                <option value="scheduled">Zamanlanmış</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Yayın Tarihi</label>
              <input v-model="form.published_at" type="datetime-local" class="w-full border rounded-lg px-4 py-2" />
            </div>
            <div class="flex items-end">
              <label class="flex items-center">
                <input v-model="form.is_featured" type="checkbox" class="mr-2" />
                <span class="text-sm">Öne Çıkar</span>
              </label>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Etiketler</label>
            <input v-model="form.tags_input" type="text" class="w-full border rounded-lg px-4 py-2" placeholder="Virgülle ayırın: seyahat, tatil, ipuçları" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SEO Başlığı</label>
            <input v-model="form.meta_title" type="text" class="w-full border rounded-lg px-4 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SEO Açıklama</label>
            <textarea v-model="form.meta_description" rows="2" class="w-full border rounded-lg px-4 py-2"></textarea>
          </div>
          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
              İptal
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              {{ isEditing ? 'Güncelle' : 'Oluştur' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const posts = ref([])
const categories = ref([])
const stats = ref({})
const showModal = ref(false)
const isEditing = ref(false)
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0
})

const filters = reactive({
  search: '',
  status: '',
  category_id: ''
})

const form = reactive({
  id: null,
  title: '',
  slug: '',
  excerpt: '',
  content: '',
  category_id: '',
  status: 'draft',
  published_at: '',
  is_featured: false,
  tags_input: '',
  meta_title: '',
  meta_description: ''
})

const fetchPosts = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.status) params.append('status', filters.status)
    if (filters.category_id) params.append('category_id', filters.category_id)
    params.append('page', pagination.value.current_page)

    const response = await axios.get(`/api/admin/blog/posts?${params}`)
    posts.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total
    }
  } catch (error) {
    console.error('Error fetching posts:', error)
  }
}

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/admin/blog/categories')
    categories.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const fetchStats = async () => {
  try {
    const response = await axios.get('/api/admin/blog/stats')
    stats.value = response.data
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

const openCreateModal = () => {
  isEditing.value = false
  Object.assign(form, {
    id: null,
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    category_id: '',
    status: 'draft',
    published_at: '',
    is_featured: false,
    tags_input: '',
    meta_title: '',
    meta_description: ''
  })
  showModal.value = true
}

const editPost = (post) => {
  isEditing.value = true
  Object.assign(form, {
    ...post,
    tags_input: post.tags?.join(', ') || ''
  })
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const savePost = async () => {
  try {
    const data = {
      ...form,
      tags: form.tags_input.split(',').map(t => t.trim()).filter(Boolean)
    }
    
    if (isEditing.value) {
      await axios.put(`/api/admin/blog/posts/${form.id}`, data)
    } else {
      await axios.post('/api/admin/blog/posts', data)
    }
    closeModal()
    fetchPosts()
    fetchStats()
  } catch (error) {
    console.error('Error saving post:', error)
    alert('Kayıt sırasında bir hata oluştu')
  }
}

const deletePost = async (post) => {
  if (!confirm(`"${post.title}" silinecek. Emin misiniz?`)) return
  
  try {
    await axios.delete(`/api/admin/blog/posts/${post.id}`)
    fetchPosts()
    fetchStats()
  } catch (error) {
    console.error('Error deleting post:', error)
  }
}

const publishPost = async (post) => {
  try {
    await axios.post(`/api/admin/blog/posts/${post.id}/publish`)
    fetchPosts()
    fetchStats()
  } catch (error) {
    console.error('Error publishing post:', error)
  }
}

const unpublishPost = async (post) => {
  try {
    await axios.post(`/api/admin/blog/posts/${post.id}/unpublish`)
    fetchPosts()
    fetchStats()
  } catch (error) {
    console.error('Error unpublishing post:', error)
  }
}

const toggleFeatured = async (post) => {
  try {
    await axios.post(`/api/admin/blog/posts/${post.id}/toggle-featured`)
    fetchPosts()
  } catch (error) {
    console.error('Error toggling featured:', error)
  }
}

const changePage = (page) => {
  pagination.value.current_page = page
  fetchPosts()
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR')
}

const getStatusClass = (status) => {
  const classes = {
    published: 'bg-green-100 text-green-800',
    draft: 'bg-gray-100 text-gray-800',
    scheduled: 'bg-blue-100 text-blue-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    published: 'Yayında',
    draft: 'Taslak',
    scheduled: 'Zamanlanmış'
  }
  return labels[status] || status
}

onMounted(() => {
  fetchPosts()
  fetchCategories()
  fetchStats()
})
</script>
