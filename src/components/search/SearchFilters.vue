<template>
  <div class="bg-white rounded-2xl border border-slate-200 p-4">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-bold text-slate-900">Filtreler</h3>
      <button 
        v-if="hasActiveFilters"
        @click="clearAllFilters"
        class="text-sm text-red-500 hover:underline"
      >
        Temizle
      </button>
    </div>

    <!-- Price Range -->
    <div class="mb-6">
      <button 
        @click="toggleSection('price')"
        class="flex items-center justify-between w-full text-left mb-3"
      >
        <span class="font-medium text-slate-700">💰 Fiyat Aralığı</span>
        <span class="text-slate-400">{{ expandedSections.price ? '−' : '+' }}</span>
      </button>
      
      <Transition name="collapse">
        <div v-if="expandedSections.price" class="space-y-3">
          <div class="flex gap-2">
            <div class="flex-1">
              <input
                v-model.number="filters.priceMin"
                type="number"
                placeholder="Min"
                class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500"
              />
            </div>
            <span class="text-slate-400 self-center">−</span>
            <div class="flex-1">
              <input
                v-model.number="filters.priceMax"
                type="number"
                placeholder="Max"
                class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500"
              />
            </div>
          </div>
          
          <!-- Quick price ranges -->
          <div class="flex flex-wrap gap-2">
            <button 
              v-for="range in priceRanges"
              :key="range.label"
              @click="setPriceRange(range.min, range.max)"
              class="px-3 py-1 text-xs border border-slate-200 rounded-full hover:border-indigo-500 hover:text-indigo-600 transition"
              :class="{ 'bg-indigo-50 border-indigo-500 text-indigo-600': isPriceRangeActive(range) }"
            >
              {{ range.label }}
            </button>
          </div>
        </div>
      </Transition>
    </div>

    <!-- Categories -->
    <div class="mb-6">
      <button 
        @click="toggleSection('categories')"
        class="flex items-center justify-between w-full text-left mb-3"
      >
        <span class="font-medium text-slate-700">📁 Kategoriler</span>
        <span class="text-slate-400">{{ expandedSections.categories ? '−' : '+' }}</span>
      </button>
      
      <Transition name="collapse">
        <div v-if="expandedSections.categories" class="space-y-2 max-h-48 overflow-y-auto">
          <label 
            v-for="cat in categories"
            :key="cat.id"
            class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-2 rounded-lg transition"
          >
            <input 
              type="checkbox"
              :value="cat.id"
              v-model="filters.categories"
              class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
            />
            <span class="text-sm text-slate-700 flex-1">{{ cat.name }}</span>
            <span class="text-xs text-slate-400">({{ cat.count }})</span>
          </label>
        </div>
      </Transition>
    </div>

    <!-- Brands -->
    <div class="mb-6">
      <button 
        @click="toggleSection('brands')"
        class="flex items-center justify-between w-full text-left mb-3"
      >
        <span class="font-medium text-slate-700">🏷️ Markalar</span>
        <span class="text-slate-400">{{ expandedSections.brands ? '−' : '+' }}</span>
      </button>
      
      <Transition name="collapse">
        <div v-if="expandedSections.brands">
          <input
            v-model="brandSearch"
            type="text"
            placeholder="Marka ara..."
            class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm mb-3 focus:ring-2 focus:ring-indigo-500"
          />
          <div class="space-y-2 max-h-48 overflow-y-auto">
            <label 
              v-for="brand in filteredBrands"
              :key="brand.id"
              class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-2 rounded-lg transition"
            >
              <input 
                type="checkbox"
                :value="brand.id"
                v-model="filters.brands"
                class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
              />
              <span class="text-sm text-slate-700 flex-1">{{ brand.name }}</span>
              <span class="text-xs text-slate-400">({{ brand.count }})</span>
            </label>
          </div>
        </div>
      </Transition>
    </div>

    <!-- Ratings -->
    <div class="mb-6">
      <button 
        @click="toggleSection('rating')"
        class="flex items-center justify-between w-full text-left mb-3"
      >
        <span class="font-medium text-slate-700">⭐ Değerlendirme</span>
        <span class="text-slate-400">{{ expandedSections.rating ? '−' : '+' }}</span>
      </button>
      
      <Transition name="collapse">
        <div v-if="expandedSections.rating" class="space-y-2">
          <label 
            v-for="rating in [4, 3, 2, 1]"
            :key="rating"
            class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-2 rounded-lg transition"
          >
            <input 
              type="radio"
              :value="rating"
              v-model="filters.minRating"
              name="rating"
              class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500"
            />
            <div class="flex items-center gap-1">
              <span v-for="n in 5" :key="n" class="text-sm">
                {{ n <= rating ? '⭐' : '☆' }}
              </span>
            </div>
            <span class="text-sm text-slate-500">ve üzeri</span>
          </label>
        </div>
      </Transition>
    </div>

    <!-- Shipping & Stock -->
    <div class="mb-6">
      <button 
        @click="toggleSection('shipping')"
        class="flex items-center justify-between w-full text-left mb-3"
      >
        <span class="font-medium text-slate-700">🚚 Kargo & Stok</span>
        <span class="text-slate-400">{{ expandedSections.shipping ? '−' : '+' }}</span>
      </button>
      
      <Transition name="collapse">
        <div v-if="expandedSections.shipping" class="space-y-3">
          <label class="flex items-center gap-3 cursor-pointer">
            <input 
              type="checkbox"
              v-model="filters.freeShipping"
              class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
            />
            <span class="text-sm text-slate-700">Ücretsiz Kargo</span>
          </label>
          <label class="flex items-center gap-3 cursor-pointer">
            <input 
              type="checkbox"
              v-model="filters.inStock"
              class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
            />
            <span class="text-sm text-slate-700">Stokta Var</span>
          </label>
          <label class="flex items-center gap-3 cursor-pointer">
            <input 
              type="checkbox"
              v-model="filters.fastDelivery"
              class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
            />
            <span class="text-sm text-slate-700">Hızlı Teslimat (1-3 gün)</span>
          </label>
        </div>
      </Transition>
    </div>

    <!-- Seller Rating -->
    <div class="mb-6">
      <button 
        @click="toggleSection('seller')"
        class="flex items-center justify-between w-full text-left mb-3"
      >
        <span class="font-medium text-slate-700">🏪 Satıcı Puanı</span>
        <span class="text-slate-400">{{ expandedSections.seller ? '−' : '+' }}</span>
      </button>
      
      <Transition name="collapse">
        <div v-if="expandedSections.seller" class="space-y-2">
          <label 
            v-for="score in [4.5, 4.0, 3.5]"
            :key="score"
            class="flex items-center gap-3 cursor-pointer hover:bg-slate-50 p-2 rounded-lg transition"
          >
            <input 
              type="radio"
              :value="score"
              v-model="filters.minSellerRating"
              name="sellerRating"
              class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500"
            />
            <span class="text-sm text-slate-700">⭐ {{ score }}+ puan</span>
          </label>
        </div>
      </Transition>
    </div>

    <!-- Apply Button -->
    <button 
      @click="applyFilters"
      class="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition"
    >
      Filtreleri Uygula
      <span v-if="activeFilterCount > 0" class="ml-2 px-2 py-0.5 bg-white/20 rounded-full text-sm">
        {{ activeFilterCount }}
      </span>
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, reactive } from 'vue'

interface FilterState {
  priceMin: number | null
  priceMax: number | null
  categories: string[]
  brands: string[]
  minRating: number | null
  minSellerRating: number | null
  freeShipping: boolean
  inStock: boolean
  fastDelivery: boolean
}

const emit = defineEmits<{
  filter: [filters: FilterState]
  clear: []
}>()

const filters = reactive<FilterState>({
  priceMin: null,
  priceMax: null,
  categories: [],
  brands: [],
  minRating: null,
  minSellerRating: null,
  freeShipping: false,
  inStock: false,
  fastDelivery: false
})

const expandedSections = reactive({
  price: true,
  categories: true,
  brands: true,
  rating: false,
  shipping: false,
  seller: false
})

const brandSearch = ref('')

// Categories and brands from API
const categories = ref<any[]>([])
const brands = ref<any[]>([])

// Fetch filter options from API
const fetchFilterOptions = async () => {
  try {
    const [catRes, brandRes] = await Promise.all([
      axios.get('/api/categories'),
      axios.get('/api/brands')
    ])
    categories.value = (catRes.data.categories || catRes.data || []).map((c: any) => ({
      id: c.id || c.slug,
      name: c.name,
      count: c.products_count || c.count || 0
    }))
    brands.value = (brandRes.data.brands || brandRes.data || []).map((b: any) => ({
      id: b.id || b.slug,
      name: b.name,
      count: b.products_count || b.count || 0
    }))
  } catch (error) {
    // Fallback demo data
    categories.value = [
      { id: 'shoes', name: 'Spor Ayakkabılar', count: 1234 },
      { id: 'clothing', name: 'Spor Giyim', count: 856 },
      { id: 'equipment', name: 'Spor Ekipmanları', count: 432 },
    ]
    brands.value = [
      { id: 'nike', name: 'Nike', count: 456 },
      { id: 'adidas', name: 'Adidas', count: 389 },
      { id: 'puma', name: 'Puma', count: 234 },
    ]
  }
}

import { onMounted } from 'vue'
import axios from 'axios'

onMounted(() => {
  fetchFilterOptions()
})

const priceRanges = [
  { label: '0-100₺', min: 0, max: 100 },
  { label: '100-500₺', min: 100, max: 500 },
  { label: '500-1000₺', min: 500, max: 1000 },
  { label: '1000₺+', min: 1000, max: null },
]

const filteredBrands = computed(() => {
  if (!brandSearch.value) return brands.value
  return brands.value.filter(b => 
    b.name.toLowerCase().includes(brandSearch.value.toLowerCase())
  )
})

const hasActiveFilters = computed(() => {
  return filters.priceMin !== null ||
         filters.priceMax !== null ||
         filters.categories.length > 0 ||
         filters.brands.length > 0 ||
         filters.minRating !== null ||
         filters.minSellerRating !== null ||
         filters.freeShipping ||
         filters.inStock ||
         filters.fastDelivery
})

const activeFilterCount = computed(() => {
  let count = 0
  if (filters.priceMin !== null || filters.priceMax !== null) count++
  count += filters.categories.length
  count += filters.brands.length
  if (filters.minRating !== null) count++
  if (filters.minSellerRating !== null) count++
  if (filters.freeShipping) count++
  if (filters.inStock) count++
  if (filters.fastDelivery) count++
  return count
})

const toggleSection = (section: keyof typeof expandedSections) => {
  expandedSections[section] = !expandedSections[section]
}

const setPriceRange = (min: number, max: number | null) => {
  filters.priceMin = min
  filters.priceMax = max
}

const isPriceRangeActive = (range: { min: number; max: number | null }) => {
  return filters.priceMin === range.min && filters.priceMax === range.max
}

const applyFilters = () => {
  emit('filter', { ...filters })
}

const clearAllFilters = () => {
  filters.priceMin = null
  filters.priceMax = null
  filters.categories = []
  filters.brands = []
  filters.minRating = null
  filters.minSellerRating = null
  filters.freeShipping = false
  filters.inStock = false
  filters.fastDelivery = false
  emit('clear')
}
</script>

<style scoped>
.collapse-enter-active,
.collapse-leave-active {
  transition: all 0.2s ease;
  overflow: hidden;
}
.collapse-enter-from,
.collapse-leave-to {
  opacity: 0;
  max-height: 0;
}
.collapse-enter-to,
.collapse-leave-from {
  max-height: 300px;
}
</style>
