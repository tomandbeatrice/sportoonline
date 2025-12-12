<template>
  <div class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Favorilerim</h1>
        <p class="text-slate-500">{{ favorites.length }} ürün listenizde</p>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
          <div class="flex-1 min-w-[200px]">
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Ürün ara..." 
                class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
              >
            </div>
          </div>
          <select 
            v-model="stockFilter"
            class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
          >
            <option value="">Tüm Ürünler</option>
            <option value="in-stock">Stokta Var</option>
            <option value="out-of-stock">Stokta Yok</option>
            <option value="low-stock">Az Stok</option>
          </select>
          <select 
            v-model="sortBy"
            class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
          >
            <option value="date">Eklenme Tarihi</option>
            <option value="price-low">Fiyat (Düşük-Yüksek)</option>
            <option value="price-high">Fiyat (Yüksek-Düşük)</option>
            <option value="discount">İndirim Oranı</option>
          </select>
        </div>
      </div>

      <!-- Favorites Grid -->
      <div v-if="filteredFavorites.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <div
          v-for="product in filteredFavorites"
          :key="product.id"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-lg transition relative group"
        >
          <!-- Discount Badge -->
          <div v-if="product.discount > 0" class="absolute top-2 left-2 z-10 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-lg">
            -%{{ product.discount }}
          </div>

          <!-- Remove Button -->
          <button 
            @click="removeFromFavorites(product.id)"
            class="absolute top-2 right-2 z-10 w-8 h-8 bg-white/90 backdrop-blur rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition hover:bg-red-50"
            title="Favorilerden Çıkar"
          >
            🗑️
          </button>

          <!-- Stock Badge -->
          <div 
            v-if="product.stock === 0"
            class="absolute top-12 left-2 z-10 bg-slate-900 text-white text-xs font-medium px-2 py-1 rounded-lg"
          >
            Tükendi
          </div>
          <div 
            v-else-if="product.stock < 5"
            class="absolute top-12 left-2 z-10 bg-orange-500 text-white text-xs font-medium px-2 py-1 rounded-lg"
          >
            Son {{ product.stock }} adet
          </div>

          <!-- Product Image -->
          <router-link :to="`/products/${product.id}`" class="block">
            <div class="aspect-square bg-slate-100 overflow-hidden relative">
              <img 
                :src="product.image" 
                :alt="product.name" 
                class="w-full h-full object-cover"
                :class="{ 'opacity-50': product.stock === 0 }"
              >
            </div>
          </router-link>

          <!-- Product Info -->
          <div class="p-4">
            <router-link :to="`/products/${product.id}`">
              <h3 class="font-medium text-slate-900 text-sm mb-1 line-clamp-2 hover:text-green-600">
                {{ product.name }}
              </h3>
            </router-link>
            
            <!-- Rating -->
            <div class="flex items-center gap-1 text-xs text-slate-500 mb-2">
              <span class="text-yellow-500">⭐</span>
              <span>{{ product.rating }}</span>
              <span>({{ product.reviewCount }})</span>
            </div>

            <!-- Price -->
            <div class="mb-3">
              <div v-if="product.discount > 0" class="flex items-center gap-2">
                <span class="text-xs text-slate-400 line-through">{{ formatPrice(product.originalPrice) }}</span>
                <span class="text-lg font-bold text-green-600">{{ formatPrice(product.price) }}</span>
              </div>
              <div v-else>
                <span class="text-lg font-bold text-slate-900">{{ formatPrice(product.price) }}</span>
              </div>
            </div>

            <!-- Price Alert Toggle -->
            <div class="flex items-center gap-2 mb-3 text-xs">
              <label class="flex items-center gap-1 cursor-pointer">
                <input 
                  type="checkbox" 
                  v-model="product.priceAlert"
                  @change="togglePriceAlert(product.id, product.priceAlert)"
                  class="w-4 h-4 text-green-600 border-slate-300 rounded focus:ring-green-500"
                >
                <span class="text-slate-600">Fiyat düşünce bildir</span>
                <span class="text-slate-400">🔔</span>
              </label>
            </div>

            <!-- Actions -->
            <button 
              @click="addToCart(product)"
              :disabled="product.stock === 0"
              class="w-full py-2.5 text-sm font-medium rounded-lg transition"
              :class="product.stock === 0 
                ? 'bg-slate-200 text-slate-400 cursor-not-allowed' 
                : 'bg-green-600 text-white hover:bg-green-700'"
            >
              {{ product.stock === 0 ? 'Stokta Yok' : '🛒 Sepete Ekle' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <div class="text-5xl mb-4">❤️</div>
        <h3 class="font-bold text-slate-900 mb-2">Favori Ürününüz Yok</h3>
        <p class="text-slate-500 mb-4">Beğendiğiniz ürünleri favorilerinize ekleyin ve kolayca takip edin.</p>
        <router-link to="/products" class="inline-flex px-6 py-2.5 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700">
          Ürünleri Keşfet
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'

const router = useRouter()
const toast = useToast()

const searchQuery = ref('')
const stockFilter = ref('')
const sortBy = ref('date')

const favorites = ref([
  { 
    id: 1, 
    name: 'Nike Air Max 270 Erkek Spor Ayakkabı', 
    price: 2199, 
    originalPrice: 2999,
    discount: 27,
    image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400',
    stock: 15,
    rating: 4.7,
    reviewCount: 234,
    priceAlert: false,
    addedDate: '2025-12-01'
  },
  { 
    id: 2, 
    name: 'Adidas Ultraboost 22 Koşu Ayakkabısı', 
    price: 2899, 
    originalPrice: 2899,
    discount: 0,
    image: 'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=400',
    stock: 3,
    rating: 4.8,
    reviewCount: 567,
    priceAlert: true,
    addedDate: '2025-12-03'
  },
  { 
    id: 3, 
    name: 'Puma RS-X Retro Sneaker', 
    price: 1599, 
    originalPrice: 2199,
    discount: 27,
    image: 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=400',
    stock: 0,
    rating: 4.5,
    reviewCount: 189,
    priceAlert: false,
    addedDate: '2025-12-05'
  },
  { 
    id: 4, 
    name: 'New Balance 574 Classic Sneaker', 
    price: 1799, 
    originalPrice: 1799,
    discount: 0,
    image: 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=400',
    stock: 22,
    rating: 4.6,
    reviewCount: 412,
    priceAlert: true,
    addedDate: '2025-11-28'
  },
])

const filteredFavorites = computed(() => {
  let result = [...favorites.value]
  
  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(p => p.name.toLowerCase().includes(query))
  }
  
  // Stock filter
  if (stockFilter.value === 'in-stock') {
    result = result.filter(p => p.stock > 5)
  } else if (stockFilter.value === 'out-of-stock') {
    result = result.filter(p => p.stock === 0)
  } else if (stockFilter.value === 'low-stock') {
    result = result.filter(p => p.stock > 0 && p.stock < 5)
  }
  
  // Sort
  if (sortBy.value === 'price-low') {
    result.sort((a, b) => a.price - b.price)
  } else if (sortBy.value === 'price-high') {
    result.sort((a, b) => b.price - a.price)
  } else if (sortBy.value === 'discount') {
    result.sort((a, b) => b.discount - a.discount)
  } else if (sortBy.value === 'date') {
    result.sort((a, b) => new Date(b.addedDate).getTime() - new Date(a.addedDate).getTime())
  }
  
  return result
})

const formatPrice = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { 
    style: 'currency', 
    currency: 'TRY', 
    maximumFractionDigits: 0 
  }).format(value)
}

const removeFromFavorites = (id: number) => {
  favorites.value = favorites.value.filter(f => f.id !== id)
  toast.success('Ürün favorilerden çıkarıldı')
}

const togglePriceAlert = (id: number, enabled: boolean) => {
  if (enabled) {
    toast.success('Fiyat düştüğünde bilgilendirileceksiniz')
  } else {
    toast.info('Fiyat bildirimi kapatıldı')
  }
}

const addToCart = (product: any) => {
  toast.success(`${product.name} sepete eklendi`)
  // TODO: API call to add to cart
}
</script>
