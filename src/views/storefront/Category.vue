<template>
  <div class="category-page min-h-screen bg-gray-50">
    <!-- Header / Breadcrumb -->
    <div class="bg-white border-b border-gray-200 py-4">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
          <router-link to="/" class="hover:text-gray-700">Ana Sayfa</router-link>
          <span>/</span>
          <span class="text-gray-900 font-medium">{{ categoryName }}</span>
        </nav>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="lg:w-64 flex-shrink-0">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Filtreler</h3>

            <!-- Price Range Filter -->
            <div class="mb-6">
              <h4 class="font-semibold text-gray-900 mb-3">Fiyat Aralığı</h4>
              <div class="space-y-2">
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="filters.priceRange"
                    value="all"
                    class="mr-2"
                  />
                  <span class="text-sm text-gray-700">Tümü</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="filters.priceRange"
                    value="0-100"
                    class="mr-2"
                  />
                  <span class="text-sm text-gray-700">0 - 100 TL</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="filters.priceRange"
                    value="100-500"
                    class="mr-2"
                  />
                  <span class="text-sm text-gray-700">100 - 500 TL</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="filters.priceRange"
                    value="500-1000"
                    class="mr-2"
                  />
                  <span class="text-sm text-gray-700">500 - 1,000 TL</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="filters.priceRange"
                    value="1000+"
                    class="mr-2"
                  />
                  <span class="text-sm text-gray-700">1,000 TL+</span>
                </label>
              </div>
            </div>

            <!-- Brand Filter -->
            <div class="mb-6">
              <h4 class="font-semibold text-gray-900 mb-3">Marka</h4>
              <div class="space-y-2">
                <label
                  v-for="brand in availableBrands"
                  :key="brand"
                  class="flex items-center"
                >
                  <input
                    type="checkbox"
                    :value="brand"
                    v-model="filters.brands"
                    class="mr-2"
                  />
                  <span class="text-sm text-gray-700">{{ brand }}</span>
                </label>
              </div>
            </div>

            <!-- Color Filter -->
            <div class="mb-6">
              <h4 class="font-semibold text-gray-900 mb-3">Renk</h4>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="color in availableColors"
                  :key="color.name"
                  @click="toggleColor(color.name)"
                  :class="[
                    'w-8 h-8 rounded-full border-2 transition-all',
                    filters.colors.includes(color.name)
                      ? 'border-blue-500 ring-2 ring-blue-200'
                      : 'border-gray-300'
                  ]"
                  :style="{ backgroundColor: color.hex }"
                  :title="color.name"
                />
              </div>
            </div>

            <!-- Clear Filters -->
            <button
              @click="clearFilters"
              class="w-full py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors"
            >
              Filtreleri Temizle
            </button>
          </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1">
          <!-- Header with View Toggle and Sort -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ categoryName }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                  {{ totalProducts }} ürün bulundu
                </p>
              </div>

              <div class="flex items-center gap-4">
                <!-- View Toggle -->
                <div class="flex items-center bg-gray-100 rounded-lg p-1">
                  <button
                    @click="viewMode = 'grid'"
                    :class="[
                      'px-3 py-1.5 rounded transition-colors',
                      viewMode === 'grid'
                        ? 'bg-white text-gray-900 shadow-sm'
                        : 'text-gray-600 hover:text-gray-900'
                    ]"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                  </button>
                  <button
                    @click="viewMode = 'list'"
                    :class="[
                      'px-3 py-1.5 rounded transition-colors',
                      viewMode === 'list'
                        ? 'bg-white text-gray-900 shadow-sm'
                        : 'text-gray-600 hover:text-gray-900'
                    ]"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                  </button>
                </div>

                <!-- Sort Dropdown -->
                <select
                  v-model="sortBy"
                  class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="newest">En Yeni</option>
                  <option value="price-asc">Fiyat: Düşükten Yükseğe</option>
                  <option value="price-desc">Fiyat: Yüksekten Düşüğe</option>
                  <option value="popular">Popüler</option>
                  <option value="rating">En Yüksek Puan</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="flex justify-center items-center py-20">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
            <svg class="w-12 h-12 text-red-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-semibold text-red-900 mb-2">Bir hata oluştu</h3>
            <p class="text-red-700 mb-4">{{ error }}</p>
            <button
              @click="fetchProducts"
              class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
            >
              Tekrar Dene
            </button>
          </div>

          <!-- Products Grid/List -->
          <div v-else-if="products.length > 0">
            <!-- Grid View -->
            <div
              v-if="viewMode === 'grid'"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
            >
              <div
                v-for="product in products"
                :key="product.id"
                class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
                @click="viewProduct(product.id)"
              >
                <div class="aspect-square bg-gray-100 relative">
                  <img
                    v-if="product.image_url"
                    :src="product.image_url"
                    :alt="product.name"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                    <svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
                <div class="p-4">
                  <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">
                    {{ product.name }}
                  </h3>
                  <p class="text-sm text-gray-500 mb-2 line-clamp-1">
                    {{ product.brand || 'Marka belirtilmemiş' }}
                  </p>
                  <div class="flex items-center justify-between">
                    <span class="text-xl font-bold text-blue-600">
                      ₺{{ product.price.toFixed(2) }}
                    </span>
                    <span
                      v-if="product.stock > 0"
                      class="text-xs text-green-600"
                    >
                      Stokta
                    </span>
                    <span v-else class="text-xs text-red-600">Stokta yok</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- List View -->
            <div v-else class="space-y-4">
              <div
                v-for="product in products"
                :key="product.id"
                class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow cursor-pointer flex"
                @click="viewProduct(product.id)"
              >
                <div class="w-48 h-48 bg-gray-100 flex-shrink-0 relative">
                  <img
                    v-if="product.image_url"
                    :src="product.image_url"
                    :alt="product.name"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                    <svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
                <div class="p-6 flex-1">
                  <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ product.name }}
                  </h3>
                  <p class="text-sm text-gray-500 mb-2">
                    {{ product.brand || 'Marka belirtilmemiş' }}
                  </p>
                  <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                    {{ product.description || 'Açıklama yok' }}
                  </p>
                  <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-blue-600">
                      ₺{{ product.price.toFixed(2) }}
                    </span>
                    <span
                      v-if="product.stock > 0"
                      class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm"
                    >
                      Stokta
                    </span>
                    <span
                      v-else
                      class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm"
                    >
                      Stokta yok
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
              <nav class="flex items-center gap-2">
                <button
                  @click="goToPage(currentPage - 1)"
                  :disabled="currentPage === 1"
                  class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Önceki
                </button>
                <button
                  v-for="page in visiblePages"
                  :key="page"
                  @click="goToPage(page)"
                  :class="[
                    'px-4 py-2 border rounded-lg transition-colors',
                    currentPage === page
                      ? 'bg-blue-600 text-white border-blue-600'
                      : 'border-gray-300 hover:bg-gray-50'
                  ]"
                >
                  {{ page }}
                </button>
                <button
                  @click="goToPage(currentPage + 1)"
                  :disabled="currentPage === totalPages"
                  class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Sonraki
                </button>
              </nav>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Ürün bulunamadı</h3>
            <p class="text-gray-600 mb-4">
              Bu kategoride henüz ürün bulunmamaktadır veya arama kriterlerinizle eşleşen ürün yok.
            </p>
            <button
              @click="clearFilters"
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
            >
              Filtreleri Temizle
            </button>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

// State
const loading = ref(false)
const error = ref<string | null>(null)
const products = ref<any[]>([])
const totalProducts = ref(0)
const currentPage = ref(1)
const perPage = ref(12)
const viewMode = ref<'grid' | 'list'>('grid')
const sortBy = ref('newest')

const filters = ref({
  priceRange: 'all',
  brands: [] as string[],
  colors: [] as string[]
})

const availableBrands = ref<string[]>([])
const availableColors = ref<Array<{ name: string; hex: string }>>([
  { name: 'Siyah', hex: '#000000' },
  { name: 'Beyaz', hex: '#FFFFFF' },
  { name: 'Kırmızı', hex: '#EF4444' },
  { name: 'Mavi', hex: '#3B82F6' },
  { name: 'Yeşil', hex: '#10B981' },
  { name: 'Sarı', hex: '#F59E0B' },
  { name: 'Turuncu', hex: '#F97316' },
  { name: 'Mor', hex: '#8B5CF6' }
])

// Computed
const categoryId = computed(() => route.params.id as string)
const categoryName = computed(() => route.query.name as string || 'Kategori')

const totalPages = computed(() => Math.ceil(totalProducts.value / perPage.value))

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start < maxVisible - 1) {
    start = Math.max(1, end - maxVisible + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

// Methods
const fetchProducts = async () => {
  loading.value = true
  error.value = null

  try {
    const params: any = {
      page: currentPage.value,
      per_page: perPage.value,
      sort_by: sortBy.value
    }

    if (categoryId.value) {
      params.category_id = categoryId.value
    }

    if (filters.value.priceRange !== 'all') {
      params.price_range = filters.value.priceRange
    }

    if (filters.value.brands.length > 0) {
      params.brands = filters.value.brands.join(',')
    }

    if (filters.value.colors.length > 0) {
      params.colors = filters.value.colors.join(',')
    }

    const response = await axios.get('/api/v1/products', { params })

    products.value = response.data.data || response.data.products || []
    totalProducts.value = response.data.total || products.value.length
    
    // Extract available brands from products
    const brands = new Set<string>()
    products.value.forEach(product => {
      if (product.brand) {
        brands.add(product.brand)
      }
    })
    availableBrands.value = Array.from(brands).sort()

  } catch (err: any) {
    console.error('Error fetching products:', err)
    error.value = err.response?.data?.message || 'Ürünler yüklenirken bir hata oluştu.'
  } finally {
    loading.value = false
  }
}

const toggleColor = (colorName: string) => {
  const index = filters.value.colors.indexOf(colorName)
  if (index > -1) {
    filters.value.colors.splice(index, 1)
  } else {
    filters.value.colors.push(colorName)
  }
}

const clearFilters = () => {
  filters.value = {
    priceRange: 'all',
    brands: [],
    colors: []
  }
  currentPage.value = 1
}

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const viewProduct = (productId: number) => {
  router.push(`/products/${productId}`)
}

// Watchers
watch([() => filters.value, sortBy], () => {
  currentPage.value = 1
  fetchProducts()
}, { deep: true })

watch(currentPage, () => {
  fetchProducts()
})

watch(() => route.params.id, () => {
  if (route.name === 'Category') {
    currentPage.value = 1
    clearFilters()
    fetchProducts()
  }
})

// Lifecycle
onMounted(() => {
  fetchProducts()
})
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>