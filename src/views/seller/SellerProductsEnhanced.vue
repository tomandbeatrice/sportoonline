<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
      <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-4">
          <h1 class="text-2xl font-bold text-slate-900">Ürün Yönetimi</h1>
          <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">SATICI</span>
        </div>
        <div class="flex items-center gap-3">
          <button @click="showBulkActions = !showBulkActions" class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">
            📦 Toplu İşlemler
          </button>
          <button @click="openProductModal()" class="px-6 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700">
            ➕ Yeni Ürün Ekle
          </button>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      
      <!-- Stats -->
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm">
          <p class="text-2xl font-bold text-slate-900">{{ stats.total }}</p>
          <p class="text-xs text-slate-500">Toplam Ürün</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 shadow-sm border border-green-100">
          <p class="text-2xl font-bold text-green-700">{{ stats.active }}</p>
          <p class="text-xs text-green-600">Yayında</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4 shadow-sm border border-yellow-100">
          <p class="text-2xl font-bold text-yellow-700">{{ stats.draft }}</p>
          <p class="text-xs text-yellow-600">Taslak</p>
        </div>
        <div class="bg-red-50 rounded-xl p-4 shadow-sm border border-red-100">
          <p class="text-2xl font-bold text-red-700">{{ stats.outOfStock }}</p>
          <p class="text-xs text-red-600">Stokta Yok</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 shadow-sm border border-blue-100">
          <p class="text-2xl font-bold text-blue-700">{{ stats.lowStock }}</p>
          <p class="text-xs text-blue-600">Düşük Stok</p>
        </div>
      </div>

      <!-- Bulk Actions Panel -->
      <transition name="slide-down">
        <div v-if="showBulkActions && selectedProducts.length > 0" class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6">
          <div class="flex items-center justify-between">
            <span class="font-medium text-orange-900">{{ selectedProducts.length }} ürün seçildi</span>
            <div class="flex gap-2">
              <button @click="bulkUpdateStock" class="px-4 py-2 bg-white border border-orange-300 rounded-lg text-sm text-orange-700 hover:bg-orange-100">
                Stok Güncelle
              </button>
              <button @click="bulkUpdatePrice" class="px-4 py-2 bg-white border border-orange-300 rounded-lg text-sm text-orange-700 hover:bg-orange-100">
                Fiyat Güncelle
              </button>
              <button @click="bulkPublish" class="px-4 py-2 bg-white border border-orange-300 rounded-lg text-sm text-orange-700 hover:bg-orange-100">
                Yayınla
              </button>
              <button @click="bulkUnpublish" class="px-4 py-2 bg-white border border-orange-300 rounded-lg text-sm text-orange-700 hover:bg-orange-100">
                Yayından Kaldır
              </button>
              <button @click="selectedProducts = []" class="px-4 py-2 bg-white border border-red-300 rounded-lg text-sm text-red-700 hover:bg-red-100">
                İptal
              </button>
            </div>
          </div>
        </div>
      </transition>

      <!-- Filters -->
      <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Ürün adı veya SKU ara..."
              class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500"
            />
          </div>
          <select v-model="statusFilter" class="px-4 py-2 border border-slate-200 rounded-lg">
            <option value="">Tüm Durumlar</option>
            <option value="active">Yayında</option>
            <option value="draft">Taslak</option>
            <option value="archived">Arşiv</option>
          </select>
          <select v-model="categoryFilter" class="px-4 py-2 border border-slate-200 rounded-lg">
            <option value="">Tüm Kategoriler</option>
            <option value="electronics">Elektronik</option>
            <option value="clothing">Giyim</option>
            <option value="sports">Spor</option>
          </select>
          <select v-model="stockFilter" class="px-4 py-2 border border-slate-200 rounded-lg">
            <option value="">Tüm Stoklar</option>
            <option value="in_stock">Stokta</option>
            <option value="low_stock">Düşük Stok (< 10)</option>
            <option value="out_of_stock">Tükendi</option>
          </select>
        </div>
      </div>

      <!-- Products Table -->
      <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
          <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
              <th class="px-6 py-4 text-left">
                <input 
                  type="checkbox" 
                  @change="toggleSelectAll"
                  :checked="selectedProducts.length === filteredProducts.length && filteredProducts.length > 0"
                  class="w-4 h-4 text-orange-600 border-slate-300 rounded focus:ring-orange-500"
                />
              </th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Ürün</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">SKU</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Kategori</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Fiyat</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Stok</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Durum</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Satış</th>
              <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase">İşlem</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="product in filteredProducts" :key="product.id" class="hover:bg-slate-50">
              <td class="px-6 py-4">
                <input 
                  type="checkbox" 
                  :value="product.id"
                  v-model="selectedProducts"
                  class="w-4 h-4 text-orange-600 border-slate-300 rounded focus:ring-orange-500"
                />
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <img :src="product.image" class="w-12 h-12 rounded-lg object-cover border border-slate-200" />
                  <div>
                    <p class="font-medium text-slate-900">{{ product.name }}</p>
                    <p v-if="product.variants > 0" class="text-xs text-slate-500">{{ product.variants }} varyant</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="font-mono text-xs text-slate-600">{{ product.sku }}</span>
              </td>
              <td class="px-6 py-4">
                <span class="text-sm text-slate-600">{{ product.category }}</span>
              </td>
              <td class="px-6 py-4">
                <div>
                  <p class="font-bold text-slate-900">{{ formatPrice(product.price) }} TL</p>
                  <p v-if="product.comparePrice" class="text-xs text-slate-500 line-through">{{ formatPrice(product.comparePrice) }} TL</p>
                </div>
              </td>
              <td class="px-6 py-4">
                <div>
                  <p :class="getStockClass(product.stock)" class="font-medium">{{ product.stock }}</p>
                  <p v-if="product.lowStockAlert && product.stock < 10" class="text-xs text-amber-600">⚠️ Düşük</p>
                </div>
              </td>
              <td class="px-6 py-4">
                <span :class="getStatusBadge(product.status)" class="px-3 py-1 rounded-full text-xs font-medium">
                  {{ getStatusText(product.status) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <p class="text-sm font-medium text-slate-900">{{ product.totalSales || 0 }} adet</p>
              </td>
              <td class="px-6 py-4">
                <div class="flex justify-end gap-2">
                  <button @click="openProductModal(product)" class="p-2 hover:bg-slate-100 rounded-lg" title="Düzenle">✏️</button>
                  <button @click="duplicateProduct(product)" class="p-2 hover:bg-blue-100 rounded-lg" title="Kopyala">📄</button>
                  <button v-if="product.status === 'draft'" @click="publishProduct(product)" class="p-2 hover:bg-green-100 rounded-lg" title="Yayınla">🚀</button>
                  <button v-else @click="unpublishProduct(product)" class="p-2 hover:bg-yellow-100 rounded-lg" title="Taslağa Al">📝</button>
                  <button @click="deleteProduct(product)" class="p-2 hover:bg-red-100 rounded-lg" title="Sil">🗑️</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between mt-6">
        <p class="text-sm text-slate-500">{{ filteredProducts.length }} ürün gösteriliyor</p>
        <div class="flex gap-2">
          <button class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">← Önceki</button>
          <button class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm">1</button>
          <button class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">2</button>
          <button class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">Sonraki →</button>
        </div>
      </div>
    </div>

    <!-- Product Modal -->
    <transition name="modal">
      <div v-if="showProductModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeProductModal">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900">{{ editingProduct ? 'Ürün Düzenle' : 'Yeni Ürün Ekle' }}</h2>
            <button @click="closeProductModal" class="p-2 hover:bg-slate-100 rounded-lg">✕</button>
          </div>

          <form @submit.prevent="saveProduct" class="p-6 space-y-6">
            <!-- Basic Info -->
            <div>
              <h3 class="font-bold text-slate-900 mb-4">Temel Bilgiler</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-slate-700 mb-2">Ürün Adı *</label>
                  <input v-model="productForm.name" type="text" required class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="iPhone 14 Pro 256GB" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">SKU *</label>
                  <input v-model="productForm.sku" type="text" required class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="IPH14-PRO-256" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Kategori *</label>
                  <select v-model="productForm.category" required class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                    <option value="">Seçiniz</option>
                    <option value="electronics">Elektronik</option>
                    <option value="clothing">Giyim</option>
                    <option value="sports">Spor</option>
                  </select>
                </div>
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-slate-700 mb-2">Açıklama</label>
                  <textarea v-model="productForm.description" rows="4" class="w-full px-4 py-2 border border-slate-200 rounded-lg resize-none" placeholder="Ürün detaylarını buraya yazın..."></textarea>
                </div>
              </div>
            </div>

            <!-- Pricing -->
            <div>
              <h3 class="font-bold text-slate-900 mb-4">Fiyatlandırma</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Fiyat (TL) *</label>
                  <input v-model.number="productForm.price" type="number" step="0.01" required class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="999.99" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Karşılaştırma Fiyatı (TL)</label>
                  <input v-model.number="productForm.comparePrice" type="number" step="0.01" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="1299.99" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Maliyet (TL)</label>
                  <input v-model.number="productForm.cost" type="number" step="0.01" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="750.00" />
                </div>
              </div>
            </div>

            <!-- Stock -->
            <div>
              <h3 class="font-bold text-slate-900 mb-4">Stok Yönetimi</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Stok Adedi *</label>
                  <input v-model.number="productForm.stock" type="number" required class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="100" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-2">Düşük Stok Eşiği</label>
                  <input v-model.number="productForm.lowStockThreshold" type="number" class="w-full px-4 py-2 border border-slate-200 rounded-lg" placeholder="10" />
                </div>
              </div>
              <label class="flex items-center mt-3 cursor-pointer">
                <input type="checkbox" v-model="productForm.trackInventory" class="w-4 h-4 text-orange-600 border-slate-300 rounded" />
                <span class="ml-2 text-sm text-slate-700">Stok takibi yap</span>
              </label>
            </div>

            <!-- Variants -->
            <div>
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-900">Varyantlar</h3>
                <button type="button" @click="addVariant" class="px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-sm hover:bg-orange-200">
                  ➕ Varyant Ekle
                </button>
              </div>
              <div v-if="productForm.variants.length > 0" class="space-y-3">
                <div v-for="(variant, index) in productForm.variants" :key="index" class="p-4 border border-slate-200 rounded-lg">
                  <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input v-model="variant.name" type="text" placeholder="Renk: Siyah" class="px-3 py-2 border border-slate-200 rounded-lg text-sm" />
                    <input v-model="variant.sku" type="text" placeholder="IPH14-BLK" class="px-3 py-2 border border-slate-200 rounded-lg text-sm" />
                    <input v-model.number="variant.price" type="number" step="0.01" placeholder="Fiyat" class="px-3 py-2 border border-slate-200 rounded-lg text-sm" />
                    <div class="flex gap-2">
                      <input v-model.number="variant.stock" type="number" placeholder="Stok" class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm" />
                      <button type="button" @click="removeVariant(index)" class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">🗑️</button>
                    </div>
                  </div>
                </div>
              </div>
              <p v-else class="text-sm text-slate-500 text-center py-4 bg-slate-50 rounded-lg">Henüz varyant eklenmedi</p>
            </div>

            <!-- Images -->
            <div>
              <h3 class="font-bold text-slate-900 mb-4">Görseller</h3>
              <div class="grid grid-cols-4 gap-4">
                <div v-for="(img, index) in productForm.images" :key="index" class="relative aspect-square bg-slate-100 rounded-lg overflow-hidden">
                  <img :src="img" class="w-full h-full object-cover" />
                  <button type="button" @click="removeImage(index)" class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600">×</button>
                </div>
                <label class="aspect-square border-2 border-dashed border-slate-300 rounded-lg flex items-center justify-center cursor-pointer hover:border-orange-500">
                  <input type="file" accept="image/*" multiple class="hidden" @change="uploadImages" />
                  <span class="text-slate-400">📷</span>
                </label>
              </div>
            </div>

            <!-- Submit -->
            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
              <button type="button" @click="closeProductModal" class="px-6 py-2 border border-slate-200 rounded-lg hover:bg-slate-50">İptal</button>
              <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">{{ editingProduct ? 'Güncelle' : 'Kaydet' }}</button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

// State
const searchQuery = ref('')
const statusFilter = ref('')
const categoryFilter = ref('')
const stockFilter = ref('')
const showBulkActions = ref(false)
const selectedProducts = ref<number[]>([])
const showProductModal = ref(false)
const editingProduct = ref<any>(null)

const stats = ref({
  total: 47,
  active: 32,
  draft: 8,
  outOfStock: 3,
  lowStock: 7
})

const products = ref([
  {
    id: 1,
    name: 'iPhone 14 Pro 256GB',
    sku: 'IPH14-PRO-256',
    category: 'Elektronik',
    price: 42999,
    comparePrice: 49999,
    stock: 15,
    status: 'active',
    image: 'https://via.placeholder.com/100',
    variants: 3,
    totalSales: 42,
    lowStockAlert: false
  },
  {
    id: 2,
    name: 'Nike Air Max 2024',
    sku: 'NIKE-AM-2024',
    category: 'Spor',
    price: 2499,
    stock: 5,
    status: 'active',
    image: 'https://via.placeholder.com/100',
    variants: 5,
    totalSales: 128,
    lowStockAlert: true
  },
  {
    id: 3,
    name: 'Samsung Galaxy S24',
    sku: 'SAM-S24-128',
    category: 'Elektronik',
    price: 28999,
    comparePrice: 32999,
    stock: 0,
    status: 'active',
    image: 'https://via.placeholder.com/100',
    variants: 2,
    totalSales: 67,
    lowStockAlert: false
  }
])

const productForm = ref({
  name: '',
  sku: '',
  category: '',
  description: '',
  price: 0,
  comparePrice: 0,
  cost: 0,
  stock: 0,
  lowStockThreshold: 10,
  trackInventory: true,
  variants: [] as any[],
  images: [] as string[]
})

// Computed
const filteredProducts = computed(() => {
  return products.value.filter(p => {
    const matchSearch = !searchQuery.value || 
      p.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      p.sku.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchStatus = !statusFilter.value || p.status === statusFilter.value
    const matchCategory = !categoryFilter.value || p.category === categoryFilter.value
    const matchStock = !stockFilter.value || 
      (stockFilter.value === 'in_stock' && p.stock > 10) ||
      (stockFilter.value === 'low_stock' && p.stock > 0 && p.stock <= 10) ||
      (stockFilter.value === 'out_of_stock' && p.stock === 0)
    
    return matchSearch && matchStatus && matchCategory && matchStock
  })
})

// Methods
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price)
}

const getStockClass = (stock: number) => {
  if (stock === 0) return 'text-red-600'
  if (stock < 10) return 'text-amber-600'
  return 'text-green-600'
}

const getStatusBadge = (status: string) => {
  const classes: Record<string, string> = {
    active: 'bg-green-100 text-green-700',
    draft: 'bg-yellow-100 text-yellow-700',
    archived: 'bg-slate-100 text-slate-700'
  }
  return classes[status] || classes.draft
}

const getStatusText = (status: string) => {
  const texts: Record<string, string> = {
    active: 'Yayında',
    draft: 'Taslak',
    archived: 'Arşiv'
  }
  return texts[status] || status
}

const toggleSelectAll = () => {
  if (selectedProducts.value.length === filteredProducts.value.length) {
    selectedProducts.value = []
  } else {
    selectedProducts.value = filteredProducts.value.map(p => p.id)
  }
}

const openProductModal = (product?: any) => {
  if (product) {
    editingProduct.value = product
    productForm.value = { ...product, variants: product.variants || [] }
  } else {
    editingProduct.value = null
    productForm.value = {
      name: '',
      sku: '',
      category: '',
      description: '',
      price: 0,
      comparePrice: 0,
      cost: 0,
      stock: 0,
      lowStockThreshold: 10,
      trackInventory: true,
      variants: [],
      images: []
    }
  }
  showProductModal.value = true
}

const closeProductModal = () => {
  showProductModal.value = false
  editingProduct.value = null
}

const saveProduct = () => {
  toast.success(editingProduct.value ? 'Ürün güncellendi' : 'Ürün eklendi')
  closeProductModal()
}

const duplicateProduct = (product: any) => {
  toast.success(`${product.name} kopyalandı`)
}

const publishProduct = (product: any) => {
  product.status = 'active'
  toast.success('Ürün yayınlandı')
}

const unpublishProduct = (product: any) => {
  product.status = 'draft'
  toast.success('Ürün taslağa alındı')
}

const deleteProduct = (product: any) => {
  if (confirm(`${product.name} silinecek. Emin misiniz?`)) {
    products.value = products.value.filter(p => p.id !== product.id)
    toast.success('Ürün silindi')
  }
}

const addVariant = () => {
  productForm.value.variants.push({
    name: '',
    sku: '',
    price: productForm.value.price,
    stock: 0
  })
}

const removeVariant = (index: number) => {
  productForm.value.variants.splice(index, 1)
}

const removeImage = (index: number) => {
  productForm.value.images.splice(index, 1)
}

const uploadImages = (event: Event) => {
  const files = (event.target as HTMLInputElement).files
  if (files) {
    Array.from(files).forEach(file => {
      const reader = new FileReader()
      reader.onload = (e) => {
        productForm.value.images.push(e.target?.result as string)
      }
      reader.readAsDataURL(file)
    })
  }
}

const bulkUpdateStock = () => {
  const newStock = prompt('Yeni stok adedi:')
  if (newStock) {
    toast.success(`${selectedProducts.value.length} ürünün stoku güncellendi`)
    selectedProducts.value = []
  }
}

const bulkUpdatePrice = () => {
  const newPrice = prompt('Yeni fiyat:')
  if (newPrice) {
    toast.success(`${selectedProducts.value.length} ürünün fiyatı güncellendi`)
    selectedProducts.value = []
  }
}

const bulkPublish = () => {
  toast.success(`${selectedProducts.value.length} ürün yayınlandı`)
  selectedProducts.value = []
}

const bulkUnpublish = () => {
  toast.success(`${selectedProducts.value.length} ürün taslağa alındı`)
  selectedProducts.value = []
}
</script>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.3s ease;
}
.slide-down-enter-from, .slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>
