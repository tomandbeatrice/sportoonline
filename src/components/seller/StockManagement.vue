<template>
  <div class="stock-management p-6">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Stok Yönetimi</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="text-sm text-gray-600">Toplam Ürün</div>
        <div class="text-2xl font-bold text-blue-600">{{ stats.total_products || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="text-sm text-gray-600">Toplam Stok</div>
        <div class="text-2xl font-bold text-green-600">{{ stats.total_stock || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="text-sm text-gray-600">Düşük Stok</div>
        <div class="text-2xl font-bold text-yellow-600">{{ stats.low_stock || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="text-sm text-gray-600">Tükenen</div>
        <div class="text-2xl font-bold text-red-600">{{ stats.out_of_stock || 0 }}</div>
      </div>
    </div>

    <!-- Actions Bar -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
      <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex gap-3 w-full md:w-auto">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Ürün ara..."
            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 flex-1 md:w-64"
          />
          <select
            v-model="stockFilter"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          >
            <option value="all">Tüm Ürünler</option>
            <option value="in_stock">Stokta</option>
            <option value="low_stock">Düşük Stok</option>
            <option value="out_of_stock">Tükendi</option>
          </select>
        </div>
        <div class="flex gap-3">
          <button
            @click="showBulkUpdateModal = true"
            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold"
          >
            Toplu Güncelle
          </button>
          <button
            @click="exportStock"
            class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-semibold"
          >
            Excel İndir
          </button>
        </div>
      </div>
    </div>

    <!-- Stock Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left">
                <input
                  type="checkbox"
                  v-model="selectAll"
                  @change="toggleSelectAll"
                  class="rounded border-gray-300"
                />
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mevcut Stok</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Düşük Stok Eşiği</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="product in filteredProducts" :key="product.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <input
                  type="checkbox"
                  v-model="selectedProducts"
                  :value="product.id"
                  class="rounded border-gray-300"
                />
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <img
                    v-if="product.image_url"
                    :src="product.image_url"
                    :alt="product.name"
                    class="w-12 h-12 rounded object-cover mr-3"
                  />
                  <div>
                    <div class="text-sm font-semibold text-gray-900">{{ product.name }}</div>
                    <div class="text-sm text-gray-500">{{ product.category?.name }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ product.sku || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="text-sm font-bold"
                  :class="getStockColorClass(product.stock)"
                >
                  {{ product.stock }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <input
                  type="number"
                  v-model.number="product.low_stock_threshold"
                  @change="updateThreshold(product)"
                  min="0"
                  class="w-20 px-2 py-1 border border-gray-300 rounded text-sm"
                />
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                  :class="getStockStatusClass(product.stock, product.low_stock_threshold)"
                >
                  {{ getStockStatus(product.stock, product.low_stock_threshold) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="openUpdateModal(product)"
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  Güncelle
                </button>
                <button
                  @click="viewHistory(product)"
                  class="text-green-600 hover:text-green-900"
                >
                  Geçmiş
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

    <!-- Single Product Update Modal -->
    <div v-if="showUpdateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Stok Güncelle</h2>

        <div v-if="selectedProduct" class="mb-4">
          <div class="flex items-center mb-4">
            <img
              v-if="selectedProduct.image_url"
              :src="selectedProduct.image_url"
              :alt="selectedProduct.name"
              class="w-16 h-16 rounded object-cover mr-3"
            />
            <div>
              <div class="font-semibold">{{ selectedProduct.name }}</div>
              <div class="text-sm text-gray-500">Mevcut: {{ selectedProduct.stock }}</div>
            </div>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">İşlem Tipi</label>
              <select
                v-model="stockUpdate.type"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              >
                <option value="add">Ekle (+)</option>
                <option value="remove">Çıkar (-)</option>
                <option value="set">Ayarla (=)</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Miktar *</label>
              <input
                v-model.number="stockUpdate.quantity"
                type="number"
                min="0"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Not</label>
              <textarea
                v-model="stockUpdate.note"
                rows="2"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="Stok güncelleme nedeni..."
              ></textarea>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
              <div class="text-sm font-medium text-blue-900">Yeni Stok Miktarı</div>
              <div class="text-2xl font-bold text-blue-600">{{ calculateNewStock }}</div>
            </div>
          </div>
        </div>

        <div class="flex gap-3 pt-4 border-t mt-4">
          <button
            type="button"
            @click="closeUpdateModal"
            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold"
          >
            İptal
          </button>
          <button
            @click="saveStockUpdate"
            :disabled="saving"
            class="flex-1 px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 text-white rounded-lg font-semibold"
          >
            {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Bulk Update Modal -->
    <div v-if="showBulkUpdateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Toplu Stok Güncelleme</h2>

        <div class="mb-4">
          <div class="text-sm text-gray-600 mb-2">{{ selectedProducts.length }} ürün seçildi</div>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">İşlem Tipi</label>
              <select
                v-model="bulkUpdate.type"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              >
                <option value="add">Tümüne Ekle (+)</option>
                <option value="remove">Tümünden Çıkar (-)</option>
                <option value="set">Tümünü Ayarla (=)</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Miktar *</label>
              <input
                v-model.number="bulkUpdate.quantity"
                type="number"
                min="0"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Not</label>
              <textarea
                v-model="bulkUpdate.note"
                rows="2"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="Toplu güncelleme nedeni..."
              ></textarea>
            </div>
          </div>
        </div>

        <div class="flex gap-3 pt-4 border-t">
          <button
            type="button"
            @click="showBulkUpdateModal = false"
            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold"
          >
            İptal
          </button>
          <button
            @click="saveBulkUpdate"
            :disabled="saving || selectedProducts.length === 0"
            class="flex-1 px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 text-white rounded-lg font-semibold"
          >
            {{ saving ? 'Güncelleniyor...' : 'Güncelle' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Stock History Modal -->
    <div v-if="showHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold text-gray-900">Stok Geçmişi</h2>
          <button @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div v-if="selectedProduct" class="mb-4 pb-4 border-b">
          <div class="flex items-center">
            <img
              v-if="selectedProduct.image_url"
              :src="selectedProduct.image_url"
              :alt="selectedProduct.name"
              class="w-16 h-16 rounded object-cover mr-3"
            />
            <div>
              <div class="font-semibold text-lg">{{ selectedProduct.name }}</div>
              <div class="text-sm text-gray-500">Mevcut Stok: {{ selectedProduct.stock }}</div>
            </div>
          </div>
        </div>

        <div class="space-y-3">
          <div
            v-for="history in stockHistory"
            :key="history.id"
            class="border border-gray-200 rounded-lg p-4"
          >
            <div class="flex justify-between items-start">
              <div>
                <div class="flex items-center gap-2 mb-1">
                  <span
                    class="px-2 py-1 text-xs font-semibold rounded"
                    :class="{
                      'bg-green-100 text-green-800': history.type === 'add',
                      'bg-red-100 text-red-800': history.type === 'remove',
                      'bg-blue-100 text-blue-800': history.type === 'set'
                    }"
                  >
                    {{ getHistoryTypeLabel(history.type) }}
                  </span>
                  <span class="text-sm font-semibold">{{ history.quantity }} adet</span>
                </div>
                <div class="text-sm text-gray-600">{{ history.note || 'Not eklenmedi' }}</div>
                <div class="text-xs text-gray-500 mt-1">
                  {{ formatDate(history.created_at) }}
                </div>
              </div>
              <div class="text-right">
                <div class="text-xs text-gray-500">Önceki</div>
                <div class="font-semibold">{{ history.old_stock }}</div>
                <div class="text-xs text-gray-500 mt-1">Yeni</div>
                <div class="font-semibold text-blue-600">{{ history.new_stock }}</div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="stockHistory.length === 0" class="text-center py-12 text-gray-500">
          Stok geçmişi bulunamadı
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])
const selectedProducts = ref([])
const selectAll = ref(false)
const searchQuery = ref('')
const stockFilter = ref('all')
const showUpdateModal = ref(false)
const showBulkUpdateModal = ref(false)
const showHistoryModal = ref(false)
const selectedProduct = ref(null)
const stockHistory = ref([])
const saving = ref(false)

const stats = ref({
  total_products: 0,
  total_stock: 0,
  low_stock: 0,
  out_of_stock: 0
})

const stockUpdate = ref({
  type: 'add',
  quantity: 0,
  note: ''
})

const bulkUpdate = ref({
  type: 'add',
  quantity: 0,
  note: ''
})

const filteredProducts = computed(() => {
  let result = products.value

  // Search filter
  if (searchQuery.value) {
    result = result.filter(p =>
      p.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      p.sku?.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  // Stock filter
  if (stockFilter.value !== 'all') {
    result = result.filter(p => {
      const threshold = p.low_stock_threshold || 10
      if (stockFilter.value === 'out_of_stock') return p.stock === 0
      if (stockFilter.value === 'low_stock') return p.stock > 0 && p.stock <= threshold
      if (stockFilter.value === 'in_stock') return p.stock > threshold
      return true
    })
  }

  return result
})

const calculateNewStock = computed(() => {
  if (!selectedProduct.value) return 0
  
  const current = selectedProduct.value.stock
  const qty = stockUpdate.value.quantity || 0
  
  switch (stockUpdate.value.type) {
    case 'add':
      return current + qty
    case 'remove':
      return Math.max(0, current - qty)
    case 'set':
      return qty
    default:
      return current
  }
})

const loadProducts = async () => {
  try {
    const { data } = await axios.get('/api/seller/products')
    products.value = data.map(p => ({
      ...p,
      low_stock_threshold: p.low_stock_threshold || 10
    }))
    calculateStats()
  } catch (error) {
    console.error('Failed to load products:', error)
  }
}

const calculateStats = () => {
  stats.value = {
    total_products: products.value.length,
    total_stock: products.value.reduce((sum, p) => sum + (p.stock || 0), 0),
    low_stock: products.value.filter(p => {
      const threshold = p.low_stock_threshold || 10
      return p.stock > 0 && p.stock <= threshold
    }).length,
    out_of_stock: products.value.filter(p => p.stock === 0).length
  }
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedProducts.value = filteredProducts.value.map(p => p.id)
  } else {
    selectedProducts.value = []
  }
}

const openUpdateModal = (product) => {
  selectedProduct.value = product
  stockUpdate.value = {
    type: 'add',
    quantity: 0,
    note: ''
  }
  showUpdateModal.value = true
}

const closeUpdateModal = () => {
  showUpdateModal.value = false
  selectedProduct.value = null
}

const saveStockUpdate = async () => {
  if (!selectedProduct.value || stockUpdate.value.quantity <= 0) return

  saving.value = true
  try {
    const { data } = await axios.post(`/api/products/${selectedProduct.value.id}/stock`, {
      type: stockUpdate.value.type,
      quantity: stockUpdate.value.quantity,
      note: stockUpdate.value.note
    })

    // Update local product
    const index = products.value.findIndex(p => p.id === selectedProduct.value.id)
    if (index !== -1) {
      products.value[index].stock = data.stock || data.new_stock
    }

    calculateStats()
    closeUpdateModal()
  } catch (error) {
    console.error('Failed to update stock:', error)
    alert('Stok güncellenemedi')
  } finally {
    saving.value = false
  }
}

const saveBulkUpdate = async () => {
  if (selectedProducts.value.length === 0 || bulkUpdate.value.quantity <= 0) return

  saving.value = true
  try {
    await axios.post('/api/products/bulk-stock-update', {
      product_ids: selectedProducts.value,
      type: bulkUpdate.value.type,
      quantity: bulkUpdate.value.quantity,
      note: bulkUpdate.value.note
    })

    await loadProducts()
    showBulkUpdateModal.value = false
    selectedProducts.value = []
    selectAll.value = false
    bulkUpdate.value = { type: 'add', quantity: 0, note: '' }
  } catch (error) {
    console.error('Failed to bulk update:', error)
    alert('Toplu güncelleme başarısız')
  } finally {
    saving.value = false
  }
}

const updateThreshold = async (product) => {
  try {
    await axios.put(`/api/products/${product.id}`, {
      low_stock_threshold: product.low_stock_threshold
    })
    calculateStats()
  } catch (error) {
    console.error('Failed to update threshold:', error)
  }
}

const viewHistory = async (product) => {
  selectedProduct.value = product
  showHistoryModal.value = true
  
  try {
    const { data } = await axios.get(`/api/products/${product.id}/stock-history`)
    stockHistory.value = data.history || data
  } catch (error) {
    console.error('Failed to load history:', error)
    stockHistory.value = []
  }
}

const exportStock = () => {
  // Create CSV content
  const headers = ['Ürün Adı', 'SKU', 'Stok', 'Düşük Stok Eşiği', 'Durum']
  const rows = filteredProducts.value.map(p => [
    p.name,
    p.sku || '-',
    p.stock,
    p.low_stock_threshold || 10,
    getStockStatus(p.stock, p.low_stock_threshold)
  ])

  const csv = [headers, ...rows].map(row => row.join(',')).join('\n')
  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `stok-raporu-${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}

const getStockColorClass = (stock) => {
  if (stock === 0) return 'text-red-600'
  if (stock <= 10) return 'text-yellow-600'
  return 'text-green-600'
}

const getStockStatus = (stock, threshold = 10) => {
  if (stock === 0) return 'Tükendi'
  if (stock <= threshold) return 'Düşük Stok'
  return 'Stokta'
}

const getStockStatusClass = (stock, threshold = 10) => {
  if (stock === 0) return 'bg-red-100 text-red-800'
  if (stock <= threshold) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}

const getHistoryTypeLabel = (type) => {
  const labels = {
    add: 'Ekleme',
    remove: 'Çıkarma',
    set: 'Ayarlama'
  }
  return labels[type] || type
}

const formatDate = (date) => {
  return new Date(date).toLocaleString('tr-TR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  loadProducts()
})
</script>
