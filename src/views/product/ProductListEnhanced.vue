<template>
  <div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb -->
      <nav class="mb-6 flex items-center text-sm text-slate-500">
        <router-link to="/" class="hover:text-blue-600 transition">Ana Sayfa</router-link>
        <svg class="mx-2 h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span v-if="selectedCategory" class="font-medium text-slate-900">{{ selectedCategory.name }}</span>
        <span v-else class="font-medium text-slate-900">Tüm Ürünler</span>
      </nav>

      <div class="lg:grid lg:grid-cols-4 lg:gap-x-8 lg:items-start">
        <!-- Filters Sidebar -->
        <aside class="hidden lg:block">
          <div class="sticky top-24 space-y-6">
            <!-- Category Filter -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h3 class="font-bold text-slate-900 mb-4">Kategoriler</h3>
              <ul class="space-y-2">
                <li v-for="category in categories" :key="category.id">
                  <button 
                    @click="selectCategory(category)"
                    class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50 transition"
                    :class="selectedCategoryId === category.id ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-700'"
                  >
                    {{ category.name }} ({{ category.productCount }})
                  </button>
                  <!-- Sub-categories -->
                  <ul v-if="category.children && selectedCategoryId === category.id" class="ml-4 mt-2 space-y-1">
                    <li v-for="child in category.children" :key="child.id">
                      <button 
                        @click.stop="selectCategory(child)"
                        class="w-full text-left px-3 py-1.5 text-sm rounded-lg hover:bg-slate-50 transition text-slate-600"
                      >
                        {{ child.name }}
                      </button>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>

            <!-- Price Filter -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h3 class="font-bold text-slate-900 mb-4">Fiyat Aralığı</h3>
              <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs text-slate-500 mb-1">Min</label>
                    <input 
                      v-model.number="filters.priceMin"
                      type="number" 
                      placeholder="0"
                      class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                  </div>
                  <div>
                    <label class="block text-xs text-slate-500 mb-1">Max</label>
                    <input 
                      v-model.number="filters.priceMax"
                      type="number" 
                      placeholder="10000"
                      class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                  </div>
                </div>
              </div>
            </div>

            <!-- Stock Filter -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h3 class="font-bold text-slate-900 mb-4">Stok Durumu</h3>
              <div class="space-y-2">
                <label class="flex items-center cursor-pointer">
                  <input 
                    type="checkbox" 
                    v-model="filters.inStock"
                    class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                  >
                  <span class="ml-2 text-sm text-slate-700">Stokta Var</span>
                </label>
              </div>
            </div>

            <!-- Brand Filter -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h3 class="font-bold text-slate-900 mb-4">Markalar</h3>
              <div class="space-y-2 max-h-48 overflow-y-auto">
                <label v-for="brand in brands" :key="brand" class="flex items-center cursor-pointer">
                  <input 
                    type="checkbox" 
                    :value="brand"
                    v-model="filters.brands"
                    class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                  >
                  <span class="ml-2 text-sm text-slate-700">{{ brand }}</span>
                </label>
              </div>
            </div>

            <!-- Clear Filters -->
            <button 
              v-if="hasActiveFilters"
              @click="clearFilters"
              class="w-full py-2 px-4 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition text-sm font-medium"
            >
              Filtreleri Temizle
            </button>
          </div>
        </aside>

        <!-- Product Grid -->
        <div class="lg:col-span-3">
          <!-- Search & Sort Bar -->
          <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm mb-6">
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
              <!-- Search with Suggestions -->
              <div class="relative flex-1 w-full sm:w-auto">
                <input 
                  v-model="searchQuery"
                  @input="handleSearch"
                  @focus="showSuggestions = true"
                  type="text" 
                  placeholder="Ürün ara... (örn: spor ayakkabı, laptop)"
                  class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <svg class="absolute left-3 top-3.5 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                <!-- Search Suggestions Dropdown -->
                <div 
                  v-if="showSuggestions && searchSuggestions.length > 0"
                  class="absolute z-10 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-lg"
                >
                  <ul class="py-2">
                    <li 
                      v-for="(suggestion, index) in searchSuggestions" 
                      :key="index"
                      @click="applySuggestion(suggestion)"
                      class="px-4 py-2 hover:bg-slate-50 cursor-pointer text-sm text-slate-700"
                    >
                      <span class="font-medium">{{ suggestion }}</span>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Sort Dropdown -->
              <select 
                v-model="sortBy"
                class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="relevant">En Uygun</option>
                <option value="price-asc">Fiyat (Düşük-Yüksek)</option>
                <option value="price-desc">Fiyat (Yüksek-Düşük)</option>
                <option value="newest">En Yeni</option>
                <option value="popular">En Popüler</option>
              </select>
            </div>

            <!-- Active Filters Tags -->
            <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
              <span v-if="selectedCategory" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                {{ selectedCategory.name }}
                <button @click="selectedCategoryId = null" class="hover:text-blue-900">×</button>
              </span>
              <span v-if="filters.priceMin || filters.priceMax" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                {{ filters.priceMin || 0 }} - {{ filters.priceMax || '∞' }} TL
                <button @click="filters.priceMin = null; filters.priceMax = null" class="hover:text-blue-900">×</button>
              </span>
              <span v-for="brand in filters.brands" :key="brand" class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                {{ brand }}
                <button @click="filters.brands = filters.brands.filter(b => b !== brand)" class="hover:text-blue-900">×</button>
              </span>
            </div>
          </div>

          <!-- Results Count -->
          <div class="mb-4 text-sm text-slate-500">
            <span class="font-medium text-slate-900">{{ filteredProducts.length }}</span> ürün bulundu
          </div>

          <!-- Products Grid -->
          <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
            <router-link 
              v-for="product in paginatedProducts" 
              :key="product.id"
              :to="`/products/${product.id}`"
              class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-lg transition group"
            >
              <!-- Image -->
              <div class="aspect-square bg-slate-100 overflow-hidden relative">
                <img 
                  :src="product.image || 'https://via.placeholder.com/300'" 
                  :alt="product.name"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                >
                <!-- Badges -->
                <div v-if="product.discount" class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-lg">
                  -%{{ product.discount }}
                </div>
                <div v-if="product.stock === 0" class="absolute inset-0 bg-black/50 flex items-center justify-center">
                  <span class="bg-white text-slate-900 px-4 py-2 rounded-lg font-bold text-sm">Tükendi</span>
                </div>
              </div>

              <!-- Info -->
              <div class="p-4">
                <h3 class="font-medium text-slate-900 text-sm line-clamp-2 mb-2 group-hover:text-blue-600">
                  {{ product.name }}
                </h3>
                
                <!-- Rating -->
                <div class="flex items-center gap-1 mb-2">
                  <span class="text-yellow-500 text-xs">⭐</span>
                  <span class="text-xs text-slate-600">{{ product.rating || 4.5 }}</span>
                  <span class="text-xs text-slate-400">({{ product.reviewCount || 0 }})</span>
                </div>

                <!-- Price -->
                <div class="flex items-center justify-between">
                  <div>
                    <div v-if="product.originalPrice && product.originalPrice > product.price" class="text-xs text-slate-400 line-through">
                      {{ formatPrice(product.originalPrice) }} TL
                    </div>
                    <div class="text-lg font-bold text-slate-900">
                      {{ formatPrice(product.price) }} TL
                    </div>
                  </div>
                  <button 
                    @click.prevent="addToCart(product)"
                    class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                  </button>
                </div>
              </div>
            </router-link>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="flex items-center justify-center gap-2">
            <button 
              @click="currentPage > 1 && currentPage--"
              :disabled="currentPage === 1"
              class="px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Önceki
            </button>
            <span class="px-4 py-2 text-sm text-slate-600">
              Sayfa {{ currentPage }} / {{ totalPages }}
            </span>
            <button 
              @click="currentPage < totalPages && currentPage++"
              :disabled="currentPage === totalPages"
              class="px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Sonraki
            </button>
          </div>

          <!-- Empty State -->
          <div v-if="filteredProducts.length === 0" class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <div class="text-5xl mb-4">🔍</div>
            <h3 class="font-bold text-slate-900 mb-2">Ürün Bulunamadı</h3>
            <p class="text-slate-500 mb-4">Arama kriterlerinize uygun ürün bulunamadı.</p>
            <button @click="clearFilters" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700">
              Filtreleri Temizle
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useCartStore } from '@/stores/cartStore'

const router = useRouter()
const toast = useToast()
const cartStore = useCartStore()

// State
const searchQuery = ref('')
const showSuggestions = ref(false)
const selectedCategoryId = ref<number | null>(null)
const sortBy = ref('relevant')
const currentPage = ref(1)
const itemsPerPage = 12

const filters = ref({
  priceMin: null as number | null,
  priceMax: null as number | null,
  inStock: false,
  brands: [] as string[]
})

// Mock Data
const categories = ref([
  { id: 1, name: 'Spor Ayakkabı', productCount: 145, children: [
    { id: 11, name: 'Koşu Ayakkabıları', productCount: 45 },
    { id: 12, name: 'Basketbol Ayakkabıları', productCount: 32 }
  ]},
  { id: 2, name: 'Elektronik', productCount: 234 },
  { id: 3, name: 'Giyim', productCount: 456 },
  { id: 4, name: 'Ev & Yaşam', productCount: 189 }
])

const brands = ref(['Nike', 'Adidas', 'Puma', 'New Balance', 'Reebok'])

const products = ref([
  { id: 1, name: 'Nike Air Max 270 Erkek Spor Ayakkabı', price: 2499, originalPrice: 3499, discount: 29, image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=300', stock: 15, rating: 4.8, reviewCount: 234, brand: 'Nike', categoryId: 1 },
  { id: 2, name: 'Adidas Ultraboost 22 Koşu Ayakkabısı', price: 2899, originalPrice: null, discount: 0, image: 'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=300', stock: 8, rating: 4.9, reviewCount: 567, brand: 'Adidas', categoryId: 1 },
  { id: 3, name: 'Puma RS-X Retro Sneaker', price: 1799, originalPrice: 2199, discount: 18, image: 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=300', stock: 0, rating: 4.5, reviewCount: 189, brand: 'Puma', categoryId: 1 },
  { id: 4, name: 'Apple MacBook Pro 14" M3', price: 45999, originalPrice: null, discount: 0, image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=300', stock: 5, rating: 4.9, reviewCount: 892, brand: 'Apple', categoryId: 2 },
  { id: 5, name: 'Sony WH-1000XM5 Kulaklık', price: 8999, originalPrice: 10999, discount: 18, image: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=300', stock: 12, rating: 4.7, reviewCount: 445, brand: 'Sony', categoryId: 2 },
  // Add more mock products as needed
])

// Computed
const selectedCategory = computed(() => 
  categories.value.find(c => c.id === selectedCategoryId.value)
)

const hasActiveFilters = computed(() => 
  selectedCategoryId.value || 
  filters.value.priceMin || 
  filters.value.priceMax || 
  filters.value.inStock ||
  filters.value.brands.length > 0
)

const searchSuggestions = computed(() => {
  if (!searchQuery.value || searchQuery.value.length < 2) return []
  
  const query = searchQuery.value.toLowerCase()
  const suggestions = new Set<string>()
  
  products.value.forEach(p => {
    if (p.name.toLowerCase().includes(query)) {
      suggestions.add(p.name)
    }
  })
  
  return Array.from(suggestions).slice(0, 5)
})

const filteredProducts = computed(() => {
  let result = [...products.value]
  
  // Search filter (typo tolerant)
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(p => 
      p.name.toLowerCase().includes(query) ||
      p.brand?.toLowerCase().includes(query)
    )
  }
  
  // Category filter
  if (selectedCategoryId.value) {
    result = result.filter(p => p.categoryId === selectedCategoryId.value)
  }
  
  // Price filter
  if (filters.value.priceMin) {
    result = result.filter(p => p.price >= filters.value.priceMin!)
  }
  if (filters.value.priceMax) {
    result = result.filter(p => p.price <= filters.value.priceMax!)
  }
  
  // Stock filter
  if (filters.value.inStock) {
    result = result.filter(p => p.stock > 0)
  }
  
  // Brand filter
  if (filters.value.brands.length > 0) {
    result = result.filter(p => filters.value.brands.includes(p.brand))
  }
  
  // Sort
  if (sortBy.value === 'price-asc') {
    result.sort((a, b) => a.price - b.price)
  } else if (sortBy.value === 'price-desc') {
    result.sort((a, b) => b.price - a.price)
  } else if (sortBy.value === 'popular') {
    result.sort((a, b) => (b.reviewCount || 0) - (a.reviewCount || 0))
  }
  
  return result
})

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / itemsPerPage))

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredProducts.value.slice(start, end)
})

// Methods
const selectCategory = (category: any) => {
  selectedCategoryId.value = category.id
  currentPage.value = 1
}

const handleSearch = () => {
  showSuggestions.value = true
  currentPage.value = 1
}

const applySuggestion = (suggestion: string) => {
  searchQuery.value = suggestion
  showSuggestions.value = false
  currentPage.value = 1
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategoryId.value = null
  filters.value = {
    priceMin: null,
    priceMax: null,
    inStock: false,
    brands: []
  }
  currentPage.value = 1
}

const addToCart = (product: any) => {
  if (product.stock === 0) {
    toast.error('Ürün stokta yok')
    return
  }
  
  cartStore.addToCart({
    id: product.id,
    name: product.name,
    price: product.price,
    image: product.image,
    quantity: 1,
    type: 'product'
  })
  
  toast.success('Ürün sepete eklendi')
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 0 }).format(price)
}

// Watch for filter changes to reset page
watch(() => filters.value, () => {
  currentPage.value = 1
}, { deep: true })

// Close suggestions on click outside
const handleClickOutside = () => {
  showSuggestions.value = false
}
</script>
