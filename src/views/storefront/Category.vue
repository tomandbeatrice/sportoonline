<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Category Header -->
    <div class="bg-white border-b border-slate-200">
      <div class="mx-auto max-w-7xl px-4 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ categoryName }}</h1>
            <p class="text-sm text-slate-600">{{ totalProducts }} ürün bulundu</p>
          </div>
          
          <!-- View Toggle (Desktop) -->
          <div class="hidden md:flex items-center gap-2">
            <button 
              @click="viewMode = 'grid'"
              :class="['rounded-lg p-2 transition-colors', viewMode === 'grid' ? 'bg-blue-100 text-blue-600' : 'text-slate-600 hover:bg-slate-100']"
              title="Grid görünümü"
            >
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
            </button>
            <button 
              @click="viewMode = 'list'"
              :class="['rounded-lg p-2 transition-colors', viewMode === 'list' ? 'bg-blue-100 text-blue-600' : 'text-slate-600 hover:bg-slate-100']"
              title="Liste görünümü"
            >
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Active Filters Chips -->
        <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap items-center gap-2">
          <span class="text-sm text-slate-600">Aktif filtreler:</span>
          <button 
            v-for="filter in activeFilters" 
            :key="filter.id"
            @click="removeFilter(filter.id)"
            class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-700 transition-colors hover:bg-blue-200"
          >
            {{ filter.label }}
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <button @click="clearAllFilters" class="text-sm text-red-600 hover:text-red-700 font-medium">
            Tümünü Temizle
          </button>
        </div>
      </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-6">
      <div class="flex gap-6">
        <!-- Sidebar Filters (Desktop) -->
        <aside class="hidden md:block w-64 flex-shrink-0">
          <div class="sticky top-20 space-y-6">
            <!-- Price Range Filter -->
            <div class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
              <h3 class="mb-4 font-bold text-slate-900">Fiyat Aralığı</h3>
              <div class="space-y-3">
                <div>
                  <input 
                    v-model="filters.priceMin" 
                    type="number" 
                    placeholder="Min ₺" 
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                  />
                </div>
                <div>
                  <input 
                    v-model="filters.priceMax" 
                    type="number" 
                    placeholder="Max ₺" 
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                  />
                </div>
                <button 
                  @click="applyPriceFilter"
                  class="w-full rounded-lg bg-blue-600 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                >
                  Uygula
                </button>
              </div>
            </div>

            <!-- Brand Filter -->
            <div class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
              <h3 class="mb-4 font-bold text-slate-900">Markalar</h3>
              <div class="max-h-60 space-y-2 overflow-y-auto">
                <label 
                  v-for="brand in brands" 
                  :key="brand.id"
                  class="flex items-center gap-2 cursor-pointer hover:bg-slate-50 rounded px-2 py-1"
                >
                  <input 
                    type="checkbox" 
                    v-model="filters.brands" 
                    :value="brand.id"
                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                  />
                  <span class="text-sm text-slate-700">{{ brand.name }}</span>
                  <span class="ml-auto text-xs text-slate-500">({{ brand.count }})</span>
                </label>
              </div>
            </div>

            <!-- Rating Filter -->
            <div class="rounded-xl bg-white p-4 shadow-sm border border-slate-200">
              <h3 class="mb-4 font-bold text-slate-900">Değerlendirme</h3>
              <div class="space-y-2">
                <label 
                  v-for="rating in [5, 4, 3, 2, 1]" 
                  :key="rating"
                  class="flex items-center gap-2 cursor-pointer hover:bg-slate-50 rounded px-2 py-1"
                >
                  <input 
                    type="radio" 
                    v-model="filters.minRating" 
                    :value="rating"
                    name="rating"
                    class="border-slate-300 text-blue-600 focus:ring-blue-500"
                  />
                  <div class="flex items-center gap-1">
                    <div class="flex text-yellow-400">
                      <svg v-for="i in rating" :key="i" class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    </div>
                    <span class="text-sm text-slate-700">ve üzeri</span>
                  </div>
                </label>
              </div>
            </div>
          </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1">
          <!-- Toolbar -->
          <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <button 
                @click="showMobileFilters = true"
                class="md:hidden inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700"
              >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filtrele
              </button>
            </div>
            
            <select 
              v-model="sortBy"
              class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 focus:border-blue-500 focus:outline-none"
            >
              <option value="popularity">En Popüler</option>
              <option value="price-asc">Fiyat: Düşükten Yükseğe</option>
              <option value="price-desc">Fiyat: Yüksekten Düşüğe</option>
              <option value="newest">En Yeni</option>
              <option value="rating">En Çok Değerlendirilenler</option>
            </select>
          </div>

          <!-- Products Grid/List -->
          <div v-if="loading" class="grid gap-4" :class="viewMode === 'grid' ? 'grid-cols-2 lg:grid-cols-3' : 'grid-cols-1'">
            <div v-for="i in 6" :key="i" class="animate-pulse rounded-xl bg-white p-4 shadow-sm">
              <div class="mb-4 aspect-square w-full rounded-lg bg-slate-200"></div>
              <div class="h-4 w-3/4 rounded bg-slate-200 mb-2"></div>
              <div class="h-4 w-1/2 rounded bg-slate-200 mb-3"></div>
              <div class="h-10 w-full rounded-lg bg-slate-200"></div>
            </div>
          </div>

          <div v-else>
            <!-- Grid View -->
            <div v-if="viewMode === 'grid'" class="grid gap-4 grid-cols-2 lg:grid-cols-3">
              <div 
                v-for="product in products" 
                :key="product.id"
                class="group cursor-pointer overflow-hidden rounded-xl bg-white shadow-sm border border-slate-200 transition-all hover:shadow-lg hover:-translate-y-1"
              >
                <div class="relative aspect-square overflow-hidden bg-slate-100">
                  <img 
                    :src="product.image" 
                    :alt="product.name"
                    class="h-full w-full object-cover transition-transform group-hover:scale-110"
                    loading="lazy"
                  />
                  <div class="absolute right-2 top-2 flex flex-col gap-2">
                    <button class="rounded-full bg-white/90 p-2 opacity-0 shadow-lg transition-opacity group-hover:opacity-100 hover:bg-white">
                      <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                      </svg>
                    </button>
                    <button class="rounded-full bg-white/90 p-2 opacity-0 shadow-lg transition-opacity group-hover:opacity-100 hover:bg-white">
                      <svg class="h-5 w-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                  </div>
                  <div v-if="product.discount" class="absolute left-2 top-2 rounded-lg bg-red-600 px-2 py-1 text-xs font-bold text-white">
                    -{{ product.discount }}%
                  </div>
                </div>
                <div class="p-4">
                  <p class="mb-1 text-xs text-slate-500">{{ product.brand }}</p>
                  <h3 class="mb-2 text-sm font-semibold text-slate-900 line-clamp-2">{{ product.name }}</h3>
                  <div class="mb-2 flex items-center gap-1">
                    <div class="flex text-yellow-400">
                      <svg v-for="i in 5" :key="i" class="h-3 w-3" :class="i <= product.rating ? 'fill-current' : 'fill-slate-200'" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    </div>
                    <span class="text-xs text-slate-500">({{ product.reviews }})</span>
                  </div>
                  <div class="mb-3 flex items-baseline gap-2">
                    <p class="text-lg font-bold text-slate-900">₺{{ product.price }}</p>
                    <p v-if="product.oldPrice" class="text-sm text-slate-400 line-through">₺{{ product.oldPrice }}</p>
                  </div>
                  <button class="w-full rounded-lg bg-blue-600 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
                    Sepete Ekle
                  </button>
                </div>
              </div>
            </div>

            <!-- List View -->
            <div v-else class="space-y-4">
              <div 
                v-for="product in products" 
                :key="product.id"
                class="group flex gap-4 overflow-hidden rounded-xl bg-white p-4 shadow-sm border border-slate-200 transition-all hover:shadow-lg"
              >
                <div class="relative h-40 w-40 flex-shrink-0 overflow-hidden rounded-lg bg-slate-100">
                  <img 
                    :src="product.image" 
                    :alt="product.name"
                    class="h-full w-full object-cover"
                    loading="lazy"
                  />
                  <div v-if="product.discount" class="absolute left-2 top-2 rounded-lg bg-red-600 px-2 py-1 text-xs font-bold text-white">
                    -{{ product.discount }}%
                  </div>
                </div>
                <div class="flex flex-1 flex-col">
                  <p class="mb-1 text-xs text-slate-500">{{ product.brand }}</p>
                  <h3 class="mb-2 text-base font-semibold text-slate-900">{{ product.name }}</h3>
                  <div class="mb-2 flex items-center gap-1">
                    <div class="flex text-yellow-400">
                      <svg v-for="i in 5" :key="i" class="h-4 w-4" :class="i <= product.rating ? 'fill-current' : 'fill-slate-200'" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    </div>
                    <span class="text-sm text-slate-600">({{ product.reviews }} değerlendirme)</span>
                  </div>
                  <p class="mb-4 text-sm text-slate-600 line-clamp-2">{{ product.description }}</p>
                  <div class="mt-auto flex items-center justify-between">
                    <div class="flex items-baseline gap-2">
                      <p class="text-2xl font-bold text-slate-900">₺{{ product.price }}</p>
                      <p v-if="product.oldPrice" class="text-sm text-slate-400 line-through">₺{{ product.oldPrice }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                      <button class="rounded-lg border border-slate-300 p-2 text-slate-700 hover:bg-slate-50">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                      </button>
                      <button class="rounded-lg bg-blue-600 px-6 py-2 font-semibold text-white hover:bg-blue-700">
                        Sepete Ekle
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div class="mt-8 flex items-center justify-center gap-2">
            <button 
              @click="currentPage--"
              :disabled="currentPage === 1"
              :class="['rounded-lg px-4 py-2 font-medium transition-colors', currentPage === 1 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-300']"
            >
              Önceki
            </button>
            <button 
              v-for="page in visiblePages" 
              :key="page"
              @click="currentPage = page"
              :class="['rounded-lg px-4 py-2 font-medium transition-colors', page === currentPage ? 'bg-blue-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-300']"
            >
              {{ page }}
            </button>
            <button 
              @click="currentPage++"
              :disabled="currentPage === totalPages"
              :class="['rounded-lg px-4 py-2 font-medium transition-colors', currentPage === totalPages ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-300']"
            >
              Sonraki
            </button>
          </div>
        </main>
      </div>
    </div>

    <!-- Mobile Filter Modal -->
    <Teleport to="body">
      <div v-if="showMobileFilters" class="fixed inset-0 z-50 md:hidden">
        <div class="absolute inset-0 bg-black/50" @click="showMobileFilters = false"></div>
        <div class="absolute bottom-0 left-0 right-0 max-h-[80vh] overflow-y-auto rounded-t-3xl bg-white p-6">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900">Filtreler</h2>
            <button @click="showMobileFilters = false" class="rounded-full p-2 hover:bg-slate-100">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          <!-- Same filters as desktop -->
          <div class="space-y-6">
            <!-- Price Filter -->
            <div>
              <h3 class="mb-3 font-bold text-slate-900">Fiyat Aralığı</h3>
              <div class="flex gap-2">
                <input v-model="filters.priceMin" type="number" placeholder="Min ₺" class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm" />
                <input v-model="filters.priceMax" type="number" placeholder="Max ₺" class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm" />
              </div>
            </div>
            
            <!-- Brands -->
            <div>
              <h3 class="mb-3 font-bold text-slate-900">Markalar</h3>
              <div class="max-h-40 space-y-2 overflow-y-auto">
                <label v-for="brand in brands" :key="brand.id" class="flex items-center gap-2">
                  <input type="checkbox" v-model="filters.brands" :value="brand.id" class="rounded" />
                  <span class="text-sm">{{ brand.name }}</span>
                </label>
              </div>
            </div>
            
            <!-- Rating -->
            <div>
              <h3 class="mb-3 font-bold text-slate-900">Minimum Değerlendirme</h3>
              <div class="space-y-2">
                <label v-for="rating in [5, 4, 3, 2, 1]" :key="rating" class="flex items-center gap-2">
                  <input type="radio" v-model="filters.minRating" :value="rating" name="mobile-rating" />
                  <div class="flex items-center gap-1">
                    <div class="flex text-yellow-400">
                      <svg v-for="i in rating" :key="i" class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    </div>
                    <span class="text-sm">ve üzeri</span>
                  </div>
                </label>
              </div>
            </div>
          </div>
          
          <div class="mt-6 flex gap-3">
            <button @click="clearAllFilters(); showMobileFilters = false" class="flex-1 rounded-lg border border-slate-300 py-3 font-semibold text-slate-700">
              Temizle
            </button>
            <button @click="applyFilters(); showMobileFilters = false" class="flex-1 rounded-lg bg-blue-600 py-3 font-semibold text-white">
              Uygula
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const viewMode = ref<'grid' | 'list'>('grid')
const sortBy = ref('popularity')
const loading = ref(true)
const showMobileFilters = ref(false)
const currentPage = ref(1)
const totalPages = ref(10)

const categoryName = ref('Elektronik')
const totalProducts = ref(156)

const filters = ref({
  priceMin: null as number | null,
  priceMax: null as number | null,
  brands: [] as number[],
  minRating: null as number | null
})

const brands = ref([
  { id: 1, name: 'Apple', count: 45 },
  { id: 2, name: 'Samsung', count: 38 },
  { id: 3, name: 'Xiaomi', count: 27 },
  { id: 4, name: 'Huawei', count: 19 },
  { id: 5, name: 'LG', count: 15 }
])

const products = ref([
  { 
    id: 1, 
    name: 'iPhone 15 Pro Max 256GB', 
    brand: 'Apple',
    price: '54999.99', 
    oldPrice: '59999.99',
    discount: 8,
    rating: 5, 
    reviews: 234,
    description: 'A17 Pro çip, Titanyum kasa, 48MP kamera sistemi',
    image: 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=400&h=400&fit=crop' 
  },
  { 
    id: 2, 
    name: 'Samsung Galaxy S24 Ultra', 
    brand: 'Samsung',
    price: '49999.99', 
    rating: 5, 
    reviews: 187,
    description: 'Snapdragon 8 Gen 3, 200MP kamera, S Pen desteği',
    image: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=400&fit=crop' 
  },
  { 
    id: 3, 
    name: 'MacBook Air M3 13"', 
    brand: 'Apple',
    price: '44999.99', 
    oldPrice: '47999.99',
    discount: 6,
    rating: 5, 
    reviews: 156,
    description: 'M3 çip, 8GB RAM, 256GB SSD, Liquid Retina ekran',
    image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&h=400&fit=crop' 
  },
  { 
    id: 4, 
    name: 'Sony WH-1000XM5', 
    brand: 'Sony',
    price: '12999.99', 
    rating: 5, 
    reviews: 421,
    description: 'Aktif gürültü engelleme, 30 saat pil ömrü',
    image: 'https://images.unsplash.com/photo-1545127398-14699f92334b?w=400&h=400&fit=crop' 
  },
  { 
    id: 5, 
    name: 'iPad Air 11" M2', 
    brand: 'Apple',
    price: '24999.99', 
    rating: 4, 
    reviews: 98,
    description: 'M2 çip, 128GB, WiFi + Cellular, Magic Keyboard uyumlu',
    image: 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=400&fit=crop' 
  },
  { 
    id: 6, 
    name: 'Xiaomi 14 Pro', 
    brand: 'Xiaomi',
    price: '34999.99', 
    oldPrice: '37999.99',
    discount: 8,
    rating: 4, 
    reviews: 145,
    description: 'Snapdragon 8 Gen 3, Leica optik, 120W hızlı şarj',
    image: 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400&h=400&fit=crop' 
  }
])

const activeFilters = computed(() => {
  const result = []
  if (filters.value.priceMin || filters.value.priceMax) {
    const min = filters.value.priceMin || 0
    const max = filters.value.priceMax || '∞'
    result.push({ id: 'price', label: `₺${min} - ₺${max}` })
  }
  filters.value.brands.forEach(brandId => {
    const brand = brands.value.find(b => b.id === brandId)
    if (brand) result.push({ id: `brand-${brandId}`, label: brand.name })
  })
  if (filters.value.minRating) {
    result.push({ id: 'rating', label: `${filters.value.minRating}+ yıldız` })
  }
  return result
})

const hasActiveFilters = computed(() => activeFilters.value.length > 0)

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, currentPage.value + 2)
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

const removeFilter = (filterId: string) => {
  if (filterId === 'price') {
    filters.value.priceMin = null
    filters.value.priceMax = null
  } else if (filterId === 'rating') {
    filters.value.minRating = null
  } else if (filterId.startsWith('brand-')) {
    const brandId = parseInt(filterId.replace('brand-', ''))
    filters.value.brands = filters.value.brands.filter(id => id !== brandId)
  }
}

const clearAllFilters = () => {
  filters.value.priceMin = null
  filters.value.priceMax = null
  filters.value.brands = []
  filters.value.minRating = null
}

const applyPriceFilter = () => {
  // Apply filter logic
  console.log('Applying price filter:', filters.value.priceMin, filters.value.priceMax)
}

const applyFilters = () => {
  // Apply all filters
  console.log('Applying filters:', filters.value)
}

onMounted(() => {
  // Simulate loading
  setTimeout(() => {
    loading.value = false
  }, 1000)
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