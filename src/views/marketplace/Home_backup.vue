<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section bg-gradient-to-br from-blue-600 via-purple-600 to-pink-500 text-white py-20">
      <div class="container mx-auto px-6">
        <div class="max-w-3xl">
          <h1 class="text-5xl font-bold mb-4">Spor Dünyasının Dijital Pazarı</h1>
          <p class="text-xl mb-8 opacity-90">Binlerce ürün, güvenli alışveriş, hızlı teslimat</p>
          <div class="flex gap-4">
            <button @click="scrollToProducts" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
              Ürünleri İncele
            </button>
            <router-link to="/apply-seller" class="border-2 border-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
              Satıcı Ol
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Turbo Mod Panel -->
    <TurboMode />

    <!-- Stats Section -->
    <section class="py-12 bg-white">
      <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="text-4xl font-bold text-blue-600 mb-2">{{ stats.total_products || '0' }}</div>
            <div class="text-gray-600">Aktif Ürün</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-green-600 mb-2">{{ stats.total_sellers || '0' }}</div>
            <div class="text-gray-600">Satıcı</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-purple-600 mb-2">{{ stats.total_orders || '0' }}</div>
            <div class="text-gray-600">Sipariş</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-orange-600 mb-2">{{ stats.total_customers || '0' }}</div>
            <div class="text-gray-600">Müşteri</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories -->
    <section class="py-16 bg-gray-50">
      <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12">Popüler Kategoriler</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
          <div 
            v-for="category in categories" 
            :key="category.id"
            @click="filterByCategory(category.id)"
            class="bg-white rounded-2xl p-6 text-center cursor-pointer hover:shadow-xl transition-all hover:-translate-y-1"
          >
            <div class="text-4xl mb-3">{{ category.icon || '📦' }}</div>
            <div class="font-semibold text-gray-900">{{ category.name }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ category.product_count || 0 }} ürün</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-white" id="products">
      <div class="container mx-auto px-6">
        <div class="flex justify-between items-center mb-12">
          <h2 class="text-3xl font-bold">Öne Çıkan Ürünler</h2>
          <div class="flex gap-4">
            <select 
              v-model="filters.sort" 
              @change="fetchProducts"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="newest">En Yeniler</option>
              <option value="price_asc">Fiyat (Artan)</option>
              <option value="price_desc">Fiyat (Azalan)</option>
              <option value="popular">En Çok Satanlar</option>
            </select>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="i in 8" :key="i" class="bg-gray-100 rounded-2xl animate-pulse">
            <div class="aspect-square bg-gray-200"></div>
            <div class="p-4 space-y-3">
              <div class="h-4 bg-gray-200 rounded w-3/4"></div>
              <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
          </div>
        </div>

        <!-- Products Grid -->
        <div v-else-if="products.length > 0" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div
            v-for="product in products"
            :key="product.id"
            class="bg-white rounded-2xl overflow-hidden border border-gray-200 hover:shadow-2xl transition-all hover:-translate-y-1 cursor-pointer group"
          >
            <div 
              class="aspect-square overflow-hidden bg-gray-100 relative"
              @click="router.push(`/products/${product.id}`)"
            >
              <img
                v-if="product.image_url"
                :src="product.image_url"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>

              <!-- Stock Badge -->
              <div v-if="product.stock <= 5 && product.stock > 0" class="absolute top-3 right-3 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                Son {{ product.stock }} adet
              </div>
              <div v-else-if="product.stock === 0" class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                Tükendi
              </div>
            </div>

            <div class="p-4">
              <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ product.name }}</h3>
              <p class="text-sm text-gray-500 mb-3 line-clamp-1">{{ product.seller?.name || 'Satıcı' }}</p>
              
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-2xl font-bold text-blue-600">₺{{ formatPrice(product.price) }}</div>
                  <div v-if="product.original_price && product.original_price > product.price" class="text-sm text-gray-400 line-through">
                    ₺{{ formatPrice(product.original_price) }}
                  </div>
                </div>
                
                <button 
                  @click.stop="addToCart(product)"
                  :disabled="product.stock === 0"
                  class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
                >
                  <span v-if="product.stock === 0">Tükendi</span>
                  <span v-else>Sepete Ekle</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16">
          <div class="text-6xl mb-4">🔍</div>
          <h3 class="text-2xl font-semibold text-gray-900 mb-2">Ürün Bulunamadı</h3>
          <p class="text-gray-600 mb-6">Filtreleri değiştirerek tekrar deneyin</p>
          <button @click="clearFilters" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
            Filtreleri Temizle
          </button>
        </div>

        <!-- Load More -->
        <div v-if="hasMore && !loading" class="text-center mt-12">
          <button @click="loadMore" class="bg-gray-900 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors">
            Daha Fazla Yükle
          </button>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
      <div class="container mx-auto px-6 text-center">
        <h2 class="text-4xl font-bold mb-4">Satıcı Olmak İster Misiniz?</h2>
        <p class="text-xl mb-8 opacity-90">Ürünlerinizi binlerce müşteriye ulaştırın</p>
        <router-link to="/apply-seller" class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition-colors">
          Hemen Başvur
        </router-link>
      </div>
    </section>

    <OnboardingTour />
  </div>
</template>

            <div v-if="loading" class="flex h-64 items-center justify-center">
              <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-blue-600"></div>
            </div>

            <div v-else-if="products.length === 0" class="flex h-64 flex-col items-center justify-center text-center">
              <div class="mb-4 rounded-full bg-gray-100 p-4 text-4xl">🔍</div>
              <h3 class="text-lg font-semibold text-gray-900">Sonuç bulunamadı</h3>
              <p class="text-gray-500">Filtreleri değiştirerek tekrar deneyin.</p>
              <button @click="clearFilters" class="mt-4 text-blue-600 hover:underline">Filtreleri Temizle</button>
            </div>

            <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" id="tour-product-grid">
              <div
                v-for="product in products"
                :key="product.id"
                class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white transition-all hover:shadow-xl hover:-translate-y-1"
              >
                <div class="aspect-square overflow-hidden bg-gray-100 relative cursor-pointer" @click="router.push(`/products/${product.id}`)">
                  <img
                    v-if="product.image_url"
                    :src="product.image_url"
                    :alt="product.name"
                    class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                  />
                  <div v-else class="flex h-full items-center justify-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                  
                  <!-- Badges -->
                  <div v-if="product.badgeTokens?.length" class="absolute top-3 left-3 flex flex-col gap-1">
                    <span
                      v-for="badge in product.badgeTokens.slice(0, 2)"
                      :key="`${product.id}-${badge.code}`"
                      class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium shadow-sm backdrop-blur-md"
                      :class="badgeClasses[badge.tone]"
                    >
                      {{ badge.label }}
                    </span>
                  </div>

                  <!-- Quick Actions -->
                  <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-3 opacity-0 transform translate-y-4 transition-all duration-300 group-hover:opacity-100 group-hover:translate-y-0">
                    <button 
                      @click.stop="addToCart(product)" 
                      class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-blue-600 shadow-lg hover:bg-blue-600 hover:text-white transition-colors"
                      title="Sepete Ekle"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                    </button>
                    <button 
                      @click.stop="toggleFavorite(product.id)" 
                      class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-lg hover:bg-red-50 transition-colors"
                      :class="isFavorite(product.id) ? 'text-red-500' : 'text-gray-400 hover:text-red-500'"
                      title="Favorilere Ekle"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :fill="isFavorite(product.id) ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                      </svg>
                    </button>
                  </div>
                </div>

                <div class="flex flex-1 flex-col p-4">
                  <div class="mb-2">
                    <p class="text-xs font-medium text-blue-600 mb-1">{{ product.category?.name }}</p>
                    <h3 
                      class="font-bold text-gray-900 line-clamp-1 group-hover:text-blue-600 transition-colors cursor-pointer"
                      @click="router.push(`/products/${product.id}`)"
                    >
                      {{ product.name }}
                    </h3>
                  </div>
                  
                  <p class="mb-4 line-clamp-2 text-sm text-gray-500 min-h-[2.5rem]">{{ product.description }}</p>
                  
                  <div class="mt-auto flex items-end justify-between border-t border-gray-50 pt-3">
                    <div>
                      <span class="block text-lg font-black text-gray-900">{{ formatCurrency(product.price) }}</span>
                      <div class="flex items-center gap-1 mt-1">
                        <div class="flex text-yellow-400 text-xs">
                          <span v-for="i in 5" :key="i">{{ i <= (product.rating || 5) ? '★' : '☆' }}</span>
                        </div>
                        <span class="text-xs text-gray-400">({{ product.reviews_count || 0 }})</span>
                      </div>
                    </div>
                    
                    <span v-if="product.stock < 5" class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">
                      Son {{ product.stock }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="totalPages > 1" class="flex justify-center gap-2 pt-8">
              <button 
                v-for="page in totalPages" 
                :key="page"
                @click="changePage(page)"
                class="h-8 w-8 rounded-lg text-sm font-medium transition-colors"
                :class="currentPage === page ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
              >
                {{ page }}
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>
    <OnboardingTour />
  </div>
</template>


<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useCartStore } from '@/stores/cartStore'
import { useToast } from 'vue-toastification'
import { useSEO } from '@/composables'
import TurboMode from '@/components/home/TurboMode.vue'
import OnboardingTour from '@/components/marketplace/OnboardingTour.vue'

// SEO Configuration
useSEO({
  title: 'SportoOnline - Türkiye\'nin En Büyük Spor Ürünleri Pazaryeri',
  description: 'Spor malzemeleri, ekipmanlar ve aksesuarlar için Türkiye\'nin lider C2C pazaryeri. Güvenli alışveriş, hızlı teslimat.',
  keywords: 'spor, spor malzemeleri, spor ekipmanları, fitness, outdoor, satış, alışveriş, marketplace',
  image: '/og-image.jpg',
  type: 'website'
})

const router = useRouter()
const toast = useToast()
const cartStore = useCartStore()

// State
const loading = ref(false)
const stats = ref({
  total_products: 0,
  total_sellers: 0,
  total_orders: 0,
  total_customers: 0
})

const categories = ref<any[]>([
  { id: 1, name: 'Ayakkabı', icon: '👟', products_count: 0 },
  { id: 2, name: 'Giyim', icon: '👕', products_count: 0 },
  { id: 3, name: 'Ekipman', icon: '🎒', products_count: 0 },
  { id: 4, name: 'Aksesuar', icon: '🧢', products_count: 0 },
  { id: 5, name: 'Beslenme', icon: '🥤', products_count: 0 },
  { id: 6, name: 'Teknoloji', icon: '⌚', products_count: 0 }
])

const products = ref<any[]>([])
const currentPage = ref(1)
const totalPages = ref(1)
const selectedCategory = ref<number | null>(null)

// Methods
function scrollToProducts() {
  document.getElementById('products-section')?.scrollIntoView({ 
    behavior: 'smooth' 
  })
}

async function fetchStats() {
  try {
    const { data } = await axios.get('/api/marketplace/stats')
    stats.value = data
  } catch (error) {
    console.error('İstatistikler yüklenemedi:', error)
  }
}

async function fetchCategories() {
  try {
    const { data } = await axios.get('/api/categories')
    if (data.data) {
      categories.value = data.data.map((cat: any) => ({
        id: cat.id,
        name: cat.name,
        icon: cat.icon || '🏷️',
        products_count: cat.products_count || 0
      }))
    }
  } catch (error) {
    console.error('Kategoriler yüklenemedi:', error)
  }
}

async function fetchProducts(page = 1) {
  loading.value = true
  try {
    const params: any = { page }
    if (selectedCategory.value) {
      params.category_id = selectedCategory.value
    }
    
    const { data } = await axios.get('/api/products', { params })
    
    products.value = data.data || []
    currentPage.value = data.current_page || 1
    totalPages.value = data.last_page || 1
  } catch (error) {
    console.error('Ürünler yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

function filterByCategory(categoryId: number) {
  if (selectedCategory.value === categoryId) {
    selectedCategory.value = null
  } else {
    selectedCategory.value = categoryId
  }
  fetchProducts(1)
}

function clearFilters() {
  selectedCategory.value = null
  fetchProducts(1)
}

function changePage(page: number) {
  currentPage.value = page
  fetchProducts(page)
  scrollToProducts()
}

function addToCart(product: any) {
  cartStore.addToCart(product)
  toast.success(\\ sepete eklendi\, {
    timeout: 2000,
    hideProgressBar: true
  })
}

function formatPrice(value: number) {
  return new Intl.NumberFormat('tr-TR', { 
    style: 'currency', 
    currency: 'TRY' 
  }).format(value || 0)
}

onMounted(() => {
  fetchStats()
  fetchCategories()
  fetchProducts()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
