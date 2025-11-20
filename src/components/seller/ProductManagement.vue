<template>
  <div class="product-management p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Ürün Yönetimi</h1>
      <button
        @click="openProductModal()"
        class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Yeni Ürün Ekle
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Ürün ara..."
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        />
        <select
          v-model="filters.category"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tüm Kategoriler</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
        <select
          v-model="filters.status"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tüm Durumlar</option>
          <option value="active">Aktif</option>
          <option value="inactive">Pasif</option>
        </select>
        <select
          v-model="filters.stock"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Stok Durumu</option>
          <option value="in_stock">Stokta Var</option>
          <option value="low_stock">Düşük Stok</option>
          <option value="out_of_stock">Tükendi</option>
        </select>
      </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ürün
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Kategori
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Fiyat
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Stok
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Durum
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                İşlemler
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="product in filteredProducts" :key="product.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <img
                    :src="product.image_url || '/placeholder.jpg'"
                    :alt="product.name"
                    class="w-12 h-12 rounded object-cover"
                  />
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                    <div class="text-sm text-gray-500">SKU: {{ product.sku || 'N/A' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-gray-900">{{ product.category?.name || 'N/A' }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-semibold text-gray-900">₺{{ formatMoney(product.price) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                  :class="getStockClass(product.stock)"
                >
                  {{ product.stock || 0 }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button
                  @click="toggleStatus(product)"
                  class="px-3 py-1 text-xs font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': product.is_active,
                    'bg-gray-100 text-gray-800': !product.is_active
                  }"
                >
                  {{ product.is_active ? 'Aktif' : 'Pasif' }}
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="openProductModal(product)"
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  Düzenle
                </button>
                <button
                  @click="deleteProduct(product)"
                  class="text-red-600 hover:text-red-900"
                >
                  Sil
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="filteredProducts.length === 0" class="text-center py-12 text-gray-500">
        Ürün bulunamadı
      </div>
    </div>

    <!-- Product Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-900">
            {{ editingProduct ? 'Ürün Düzenle' : 'Yeni Ürün Ekle' }}
          </h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="saveProduct" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı *</label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
              <select
                v-model="form.category_id"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Seçiniz</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
              <input
                v-model="form.sku"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fiyat (₺) *</label>
              <input
                v-model.number="form.price"
                type="number"
                step="0.01"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Stok *</label>
              <input
                v-model.number="form.stock"
                type="number"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Görsel URL</label>
              <input
                v-model="form.image_url"
                type="url"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div class="flex items-center">
              <input
                v-model="form.is_active"
                type="checkbox"
                id="is_active"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              />
              <label for="is_active" class="ml-2 text-sm text-gray-700">Aktif</label>
            </div>
          </div>

          <div class="flex gap-3 pt-4 border-t">
            <button
              type="button"
              @click="closeModal"
              class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold"
            >
              İptal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="flex-1 px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 text-white rounded-lg font-semibold"
            >
              {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])
const categories = ref([])
const showModal = ref(false)
const editingProduct = ref(null)
const saving = ref(false)

const filters = ref({
  search: '',
  category: '',
  status: '',
  stock: ''
})

const form = ref({
  name: '',
  category_id: '',
  sku: '',
  price: 0,
  stock: 0,
  description: '',
  image_url: '',
  is_active: true
})

const filteredProducts = computed(() => {
  let result = products.value

  if (filters.value.search) {
    result = result.filter(p =>
      p.name.toLowerCase().includes(filters.value.search.toLowerCase())
    )
  }

  if (filters.value.category) {
    result = result.filter(p => p.category_id == filters.value.category)
  }

  if (filters.value.status === 'active') {
    result = result.filter(p => p.is_active)
  } else if (filters.value.status === 'inactive') {
    result = result.filter(p => !p.is_active)
  }

  if (filters.value.stock === 'in_stock') {
    result = result.filter(p => p.stock > 10)
  } else if (filters.value.stock === 'low_stock') {
    result = result.filter(p => p.stock > 0 && p.stock <= 10)
  } else if (filters.value.stock === 'out_of_stock') {
    result = result.filter(p => p.stock === 0)
  }

  return result
})

const loadProducts = async () => {
  try {
    const { data } = await axios.get('/api/seller/products')
    products.value = data.products || data
  } catch (error) {
    console.error('Failed to load products:', error)
  }
}

const loadCategories = async () => {
  try {
    const { data } = await axios.get('/api/categories')
    categories.value = data.categories || data
  } catch (error) {
    console.error('Failed to load categories:', error)
  }
}

const openProductModal = (product = null) => {
  if (product) {
    editingProduct.value = product
    form.value = { ...product }
  } else {
    editingProduct.value = null
    form.value = {
      name: '',
      category_id: '',
      sku: '',
      price: 0,
      stock: 0,
      description: '',
      image_url: '',
      is_active: true
    }
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingProduct.value = null
}

const saveProduct = async () => {
  saving.value = true
  try {
    if (editingProduct.value) {
      await axios.put(`/api/products/${editingProduct.value.id}`, form.value)
    } else {
      await axios.post('/api/products', form.value)
    }
    await loadProducts()
    closeModal()
  } catch (error) {
    console.error('Failed to save product:', error)
    alert('Ürün kaydedilemedi')
  } finally {
    saving.value = false
  }
}

const toggleStatus = async (product) => {
  try {
    await axios.put(`/api/products/${product.id}`, {
      is_active: !product.is_active
    })
    product.is_active = !product.is_active
  } catch (error) {
    console.error('Failed to toggle status:', error)
  }
}

const deleteProduct = async (product) => {
  if (!confirm(`${product.name} ürününü silmek istediğinizden emin misiniz?`)) return

  try {
    await axios.delete(`/api/products/${product.id}`)
    await loadProducts()
  } catch (error) {
    console.error('Failed to delete product:', error)
    alert('Ürün silinemedi')
  }
}

const getStockClass = (stock) => {
  if (stock === 0) return 'bg-red-100 text-red-800'
  if (stock <= 10) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}

const formatMoney = (amount) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

onMounted(() => {
  loadProducts()
  loadCategories()
})
</script>
