<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Search Header -->
    <div class="bg-white border-b border-slate-200 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 py-4">
        <GlobalSearch 
          :large="true"
          :autofocus="!searchQuery"
          placeholder="Ürün, marka, kategori veya satıcı ara..."
          @search="handleSearch"
        />
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-6">
      <!-- Breadcrumb & Results Info -->
      <div class="flex items-center justify-between mb-6">
        <div>
          <div class="text-sm text-slate-500 mb-1">
            <router-link to="/" class="hover:text-indigo-600">Ana Sayfa</router-link>
            <span class="mx-2">›</span>
            <span>Arama Sonuçları</span>
          </div>
          <h1 class="text-2xl font-bold text-slate-900">
            <span v-if="searchQuery">"{{ searchQuery }}" için</span>
            {{ totalResults }} sonuç bulundu
          </h1>
        </div>

        <!-- Sort -->
        <div class="flex items-center gap-4">
          <select 
            v-model="sortBy"
            class="px-4 py-2 border border-slate-200 rounded-xl bg-white text-sm focus:ring-2 focus:ring-indigo-500"
          >
            <option value="relevance">En İlgili</option>
            <option value="price-asc">Fiyat: Düşük → Yüksek</option>
            <option value="price-desc">Fiyat: Yüksek → Düşük</option>
            <option value="newest">En Yeni</option>
            <option value="popular">En Popüler</option>
            <option value="rating">En Çok Değerlendirilen</option>
          </select>

          <!-- View Toggle -->
          <div class="flex bg-slate-100 rounded-lg p-1">
            <button 
              @click="viewMode = 'grid'"
              class="p-2 rounded-md transition"
              :class="viewMode === 'grid' ? 'bg-white shadow' : ''"
            >
              ▦
            </button>
            <button 
              @click="viewMode = 'list'"
              class="p-2 rounded-md transition"
              :class="viewMode === 'list' ? 'bg-white shadow' : ''"
            >
              ☰
            </button>
          </div>
        </div>
      </div>

      <!-- Active Filters -->
      <div v-if="activeFilters.length > 0" class="flex flex-wrap gap-2 mb-6">
        <div 
          v-for="filter in activeFilters"
          :key="filter.key"
          class="flex items-center gap-2 px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm"
        >
          <span>{{ filter.label }}</span>
          <button @click="removeFilter(filter.key)" class="hover:text-red-500">✕</button>
        </div>
        <button 
          @click="clearAllFilters"
          class="px-3 py-1.5 text-red-500 text-sm hover:underline"
        >
          Tümünü Temizle
        </button>
      </div>

      <div class="flex gap-6">
        <!-- Filters Sidebar -->
        <div class="w-72 flex-shrink-0 hidden lg:block">
          <SearchFilters 
            @filter="handleFilter"
            @clear="clearAllFilters"
          />
        </div>

        <!-- Mobile Filter Button -->
        <button 
          @click="showMobileFilters = true"
          class="lg:hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-40 px-6 py-3 bg-indigo-600 text-white rounded-full shadow-lg flex items-center gap-2"
        >
          <span>⚙️</span>
          Filtreler
          <span v-if="activeFilters.length" class="px-2 py-0.5 bg-white/20 rounded-full text-xs">
            {{ activeFilters.length }}
          </span>
        </button>

        <!-- Results -->
        <div class="flex-1">
          <!-- Loading -->
          <div v-if="isLoading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div 
              v-for="i in 8"
              :key="i"
              class="bg-white rounded-2xl p-4 animate-pulse"
            >
              <div class="aspect-square bg-slate-200 rounded-xl mb-4"></div>
              <div class="h-4 bg-slate-200 rounded w-3/4 mb-2"></div>
              <div class="h-4 bg-slate-200 rounded w-1/2"></div>
            </div>
          </div>

          <!-- Grid View -->
          <div 
            v-else-if="viewMode === 'grid'"
            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
          >
            <div 
              v-for="product in products"
              :key="product.id"
              class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl transition-all group"
            >
              <router-link :to="`/products/${product.id}`">
                <div class="relative aspect-square overflow-hidden">
                  <img 
                    :src="product.image" 
                    :alt="product.name"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  />
                  <div v-if="product.discount" class="absolute top-3 left-3 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-lg">
                    %{{ product.discount }} İndirim
                  </div>
                  <button class="absolute top-3 right-3 w-8 h-8 bg-white/80 backdrop-blur rounded-full flex items-center justify-center hover:bg-red-50 transition">
                    ♡
                  </button>
                </div>
                <div class="p-4">
                  <div class="text-xs text-slate-500 mb-1">{{ product.brand }}</div>
                  <h3 class="font-medium text-slate-900 mb-2 line-clamp-2">{{ product.name }}</h3>
                  <div class="flex items-center gap-2 mb-2">
                    <span class="text-yellow-500 text-sm">⭐ {{ product.rating }}</span>
                    <span class="text-slate-400 text-xs">({{ product.reviewCount }})</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="font-bold text-lg text-indigo-600">{{ formatPrice(product.price) }}</span>
                    <span v-if="product.originalPrice" class="text-sm text-slate-400 line-through">
                      {{ formatPrice(product.originalPrice) }}
                    </span>
                  </div>
                  <div v-if="product.freeShipping" class="mt-2 text-xs text-green-600 flex items-center gap-1">
                    🚚 Ücretsiz Kargo
                  </div>
                </div>
              </router-link>
            </div>
          </div>

          <!-- List View -->
          <div v-else class="space-y-4">
            <div 
              v-for="product in products"
              :key="product.id"
              class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl transition-all flex"
            >
              <router-link :to="`/products/${product.id}`" class="flex w-full">
                <div class="relative w-48 h-48 flex-shrink-0">
                  <img 
                    :src="product.image" 
                    :alt="product.name"
                    class="w-full h-full object-cover"
                  />
                  <div v-if="product.discount" class="absolute top-3 left-3 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-lg">
                    %{{ product.discount }}
                  </div>
                </div>
                <div class="flex-1 p-5">
                  <div class="flex items-start justify-between">
                    <div>
                      <div class="text-sm text-slate-500 mb-1">{{ product.brand }}</div>
                      <h3 class="font-semibold text-lg text-slate-900 mb-2">{{ product.name }}</h3>
                      <p class="text-sm text-slate-600 line-clamp-2 mb-3">{{ product.description }}</p>
                      <div class="flex items-center gap-3">
                        <span class="text-yellow-500">⭐ {{ product.rating }}</span>
                        <span class="text-slate-400 text-sm">({{ product.reviewCount }} değerlendirme)</span>
                        <span v-if="product.freeShipping" class="text-green-600 text-sm">🚚 Ücretsiz Kargo</span>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="font-bold text-2xl text-indigo-600">{{ formatPrice(product.price) }}</div>
                      <div v-if="product.originalPrice" class="text-sm text-slate-400 line-through">
                        {{ formatPrice(product.originalPrice) }}
                      </div>
                      <button class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition">
                        Sepete Ekle
                      </button>
                    </div>
                  </div>
                </div>
              </router-link>
            </div>
          </div>

          <!-- No Results -->
          <div v-if="!isLoading && products.length === 0" class="text-center py-16">
            <div class="text-6xl mb-4">🔍</div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Sonuç bulunamadı</h3>
            <p class="text-slate-500 mb-6">Aramanızla eşleşen ürün bulamadık. Farklı anahtar kelimeler deneyin.</p>
            <button 
              @click="clearAllFilters"
              class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition"
            >
              Filtreleri Temizle
            </button>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="flex justify-center gap-2 mt-8">
            <button 
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-4 py-2 border border-slate-200 rounded-xl hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              ← Önceki
            </button>
            
            <button 
              v-for="page in visiblePages"
              :key="page"
              @click="goToPage(page)"
              class="w-10 h-10 rounded-xl transition"
              :class="page === currentPage 
                ? 'bg-indigo-600 text-white' 
                : 'border border-slate-200 hover:bg-slate-50'"
            >
              {{ page }}
            </button>
            
            <button 
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-4 py-2 border border-slate-200 rounded-xl hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Sonraki →
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Filters Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showMobileFilters" class="fixed inset-0 z-50 lg:hidden">
          <div class="absolute inset-0 bg-black/50" @click="showMobileFilters = false"></div>
          <div class="absolute right-0 top-0 bottom-0 w-80 bg-white overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between">
              <h3 class="font-bold">Filtreler</h3>
              <button @click="showMobileFilters = false" class="p-2">✕</button>
            </div>
            <div class="p-4">
              <SearchFilters 
                @filter="handleFilter"
                @clear="clearAllFilters"
              />
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import GlobalSearch from '@/components/search/GlobalSearch.vue'
import SearchFilters from '@/components/search/SearchFilters.vue'

interface Product {
  id: string
  name: string
  brand: string
  description: string
  price: number
  originalPrice?: number
  discount?: number
  image: string
  rating: number
  reviewCount: number
  freeShipping: boolean
}

interface ActiveFilter {
  key: string
  label: string
}

const route = useRoute()
const router = useRouter()

const searchQuery = ref('')
const isLoading = ref(false)
const viewMode = ref<'grid' | 'list'>('grid')
const sortBy = ref('relevance')
const currentPage = ref(1)
const totalPages = ref(5)
const totalResults = ref(0)
const showMobileFilters = ref(false)

const products = ref<Product[]>([])
const activeFilters = ref<ActiveFilter[]>([])

const visiblePages = computed(() => {
  const pages: number[] = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, currentPage.value + 2)
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(price)
}

const handleSearch = (query: string) => {
  searchQuery.value = query
  currentPage.value = 1
  fetchProducts()
}

const handleFilter = (filters: any) => {
  activeFilters.value = []
  
  if (filters.priceMin || filters.priceMax) {
    activeFilters.value.push({ 
      key: 'price', 
      label: `${filters.priceMin || 0}₺ - ${filters.priceMax || '∞'}₺` 
    })
  }
  
  filters.categories?.forEach((cat: string) => {
    activeFilters.value.push({ key: `cat-${cat}`, label: cat })
  })
  
  filters.brands?.forEach((brand: string) => {
    activeFilters.value.push({ key: `brand-${brand}`, label: brand })
  })
  
  if (filters.freeShipping) {
    activeFilters.value.push({ key: 'freeShipping', label: 'Ücretsiz Kargo' })
  }
  
  showMobileFilters.value = false
  currentPage.value = 1
  fetchProducts()
}

const removeFilter = (key: string) => {
  activeFilters.value = activeFilters.value.filter(f => f.key !== key)
  fetchProducts()
}

const clearAllFilters = () => {
  activeFilters.value = []
  fetchProducts()
}

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    fetchProducts()
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const fetchProducts = async () => {
  isLoading.value = true
  
  try {
    const params: Record<string, any> = {
      search: searchQuery.value,
      page: currentPage.value,
    }

    // Apply sorting
    switch (sortBy.value) {
      case 'price-asc':
        params.sort = 'price_asc'
        break
      case 'price-desc':
        params.sort = 'price_desc'
        break
      case 'newest':
        params.sort = 'newest'
        break
      case 'popular':
        params.sort = 'popular'
        break
    }

    // Apply active filters
    activeFilters.value.forEach(filter => {
      if (filter.key === 'price') {
        const match = filter.label.match(/(\d+).*-.*(\d+|∞)/)
        if (match) {
          params.min_price = match[1]
          if (match[2] !== '∞') params.max_price = match[2]
        }
      } else if (filter.key.startsWith('cat-')) {
        params.category = filter.key.replace('cat-', '')
      } else if (filter.key.startsWith('brand-')) {
        params.brand = filter.key.replace('brand-', '')
      } else if (filter.key === 'freeShipping') {
        params.free_shipping = true
      }
    })

    const response = await axios.get('/api/products', { params })
    
    products.value = response.data.products?.data || response.data.products || []
    totalResults.value = response.data.products?.total || products.value.length
    totalPages.value = response.data.products?.last_page || 1
  } catch (error) {
    console.error('Failed to fetch products:', error)
    // Fallback mock data for demo
    products.value = [
      { id: '1', name: 'Nike Air Max 270 React', brand: 'Nike', description: 'Erkek Koşu Ayakkabısı', price: 2499, originalPrice: 3199, discount: 22, image: 'https://via.placeholder.com/300', rating: 4.8, reviewCount: 1234, freeShipping: true },
      { id: '2', name: 'Adidas Ultraboost 22', brand: 'Adidas', description: 'Unisex Koşu Ayakkabısı', price: 3199, image: 'https://via.placeholder.com/300', rating: 4.7, reviewCount: 856, freeShipping: true },
    ]
    totalResults.value = 2
    totalPages.value = 1
  } finally {
    isLoading.value = false
  }
}

watch(() => route.query.q, (newQuery) => {
  if (newQuery && typeof newQuery === 'string') {
    searchQuery.value = newQuery
    fetchProducts()
  }
}, { immediate: true })

watch(sortBy, () => {
  currentPage.value = 1
  fetchProducts()
})

onMounted(() => {
  if (route.query.q) {
    searchQuery.value = route.query.q as string
  }
  fetchProducts()
})
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-from .absolute.right-0,
.modal-leave-to .absolute.right-0 {
  transform: translateX(100%);
}
</style>
