<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Blog Kategorileri</h1>
        <p class="text-gray-600">Blog kategorilerini yönetin</p>
      </div>
      <button
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center"
      >
        <span class="mr-2">+</span>
        Yeni Kategori
      </button>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <div
        v-for="category in categories"
        :key="category.id"
        class="bg-white rounded-lg shadow p-4 hover:shadow-md transition-shadow"
      >
        <div class="flex justify-between items-start">
          <div>
            <h3 class="font-bold text-lg text-gray-900">{{ category.name }}</h3>
            <p class="text-sm text-gray-500">{{ category.slug }}</p>
          </div>
          <span class="bg-blue-100 text-blue-800 px-2 py-1 text-xs rounded-full">
            {{ category.posts_count || 0 }} yazı
          </span>
        </div>
        <p v-if="category.description" class="text-sm text-gray-600 mt-2 line-clamp-2">
          {{ category.description }}
        </p>
        <div class="mt-4 pt-3 border-t flex justify-end space-x-2">
          <button @click="editCategory(category)" class="text-blue-600 hover:text-blue-800 text-sm">
            Düzenle
          </button>
          <button @click="deleteCategory(category)" class="text-red-600 hover:text-red-800 text-sm">
            Sil
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="categories.length === 0" class="text-center py-12 bg-white rounded-lg shadow">
      <div class="text-gray-400 text-5xl mb-4">📂</div>
      <h3 class="text-lg font-medium text-gray-900">Henüz kategori yok</h3>
      <p class="text-gray-500 mt-1">İlk kategoriyi oluşturmak için butona tıklayın</p>
      <button
        @click="openCreateModal"
        class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg"
      >
        + Kategori Oluştur
      </button>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold">{{ isEditing ? 'Kategori Düzenle' : 'Yeni Kategori' }}</h2>
        </div>
        <form @submit.prevent="saveCategory" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full border rounded-lg px-4 py-2"
              placeholder="Örn: Seyahat İpuçları"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
            <input
              v-model="form.slug"
              type="text"
              class="w-full border rounded-lg px-4 py-2"
              placeholder="seyahat-ipuclari"
            />
            <p class="text-xs text-gray-500 mt-1">Boş bırakılırsa otomatik oluşturulur</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full border rounded-lg px-4 py-2"
              placeholder="Kategori açıklaması..."
            ></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sıralama</label>
            <input
              v-model.number="form.sort_order"
              type="number"
              min="0"
              class="w-full border rounded-lg px-4 py-2"
            />
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

const categories = ref([])
const showModal = ref(false)
const isEditing = ref(false)

const form = reactive({
  id: null,
  name: '',
  slug: '',
  description: '',
  sort_order: 0
})

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/admin/blog/categories')
    categories.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const openCreateModal = () => {
  isEditing.value = false
  Object.assign(form, {
    id: null,
    name: '',
    slug: '',
    description: '',
    sort_order: 0
  })
  showModal.value = true
}

const editCategory = (category) => {
  isEditing.value = true
  Object.assign(form, category)
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveCategory = async () => {
  try {
    if (isEditing.value) {
      await axios.put(`/api/admin/blog/categories/${form.id}`, form)
    } else {
      await axios.post('/api/admin/blog/categories', form)
    }
    closeModal()
    fetchCategories()
  } catch (error) {
    console.error('Error saving category:', error)
    alert('Kayıt sırasında bir hata oluştu')
  }
}

const deleteCategory = async (category) => {
  if (category.posts_count > 0) {
    alert('Bu kategoride yazılar var. Önce yazıları silmeniz veya taşımanız gerekiyor.')
    return
  }
  
  if (!confirm(`"${category.name}" silinecek. Emin misiniz?`)) return
  
  try {
    await axios.delete(`/api/admin/blog/categories/${category.id}`)
    fetchCategories()
  } catch (error) {
    console.error('Error deleting category:', error)
    alert('Silme sırasında bir hata oluştu')
  }
}

onMounted(() => {
  fetchCategories()
})
</script>
