<template>
  <div class="min-h-screen bg-slate-50 pb-20">
    <!-- Hero Section with Search -->
    <div class="mx-auto max-w-6xl px-4 py-8">
      <div class="mb-8 text-center">
        <h1 class="mb-3 text-4xl font-black text-slate-800 md:text-5xl">
          Merhaba, bugün neye ihtiyacın var?
        </h1>
        <p class="text-slate-600">Milyonlarca ürün ve hizmet bir arada</p>
      </div>

      <!-- Large Search Bar -->
      <div class="mx-auto mb-8 max-w-3xl">
        <div class="group relative">
          <div class="absolute inset-y-0 left-5 flex items-center text-slate-400">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
          </div>
          <input
            v-model="searchQuery"
            type="search"
            placeholder="Ürün, kategori veya marka ara..."
            class="w-full rounded-2xl border-2 border-slate-200 bg-white px-16 py-5 text-lg text-slate-700 shadow-lg transition-all focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            @keyup.enter="handleSearch"
          />
          <div class="absolute inset-y-0 right-3 flex items-center gap-2">
            <button 
              class="rounded-xl p-2 text-slate-400 transition-colors hover:bg-slate-100 hover:text-blue-600"
              title="Sesli arama"
            >
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
              </svg>
            </button>
            <button
              @click="handleSearch"
              class="rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition-all hover:shadow-xl hover:shadow-blue-500/40"
            >
              Ara
            </button>
          </div>
        </div>
        
        <!-- Popular Searches -->
        <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
          <span class="text-slate-500">Popüler aramalar:</span>
          <button 
            v-for="term in popularSearches" 
            :key="term"
            @click="searchQuery = term; handleSearch()"
            class="rounded-full bg-white px-4 py-1.5 text-slate-600 shadow-sm transition-all hover:bg-blue-50 hover:text-blue-600 hover:shadow-md"
          >
            {{ term }}
          </button>
        </div>
      </div>

      <!-- Campaign Banner Slider -->
      <div class="relative mb-8 overflow-hidden rounded-3xl">
        <div class="flex transition-transform duration-500" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
          <div v-for="(campaign, index) in campaigns" :key="index" class="min-w-full">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r p-8 text-white" :class="campaign.bgClass">
              <div class="relative z-10">
                <span class="mb-2 inline-block rounded-full bg-white/20 px-4 py-1 text-sm font-bold backdrop-blur-md">
                  {{ campaign.badge }}
                </span>
                <h2 class="mb-2 text-3xl font-black md:text-4xl">{{ campaign.title }}</h2>
                <p class="mb-4 text-lg opacity-90">{{ campaign.description }}</p>
                <router-link :to="campaign.link" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 font-bold text-slate-900 shadow-lg transition-transform hover:scale-105">
                  {{ campaign.cta }}
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                  </svg>
                </router-link>
              </div>
              <div class="absolute right-0 top-0 h-full w-1/3 opacity-20">
                <div class="text-9xl">{{ campaign.emoji }}</div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Slider Controls -->
        <div class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2">
          <button 
            v-for="(_, index) in campaigns" 
            :key="index"
            @click="currentSlide = index"
            :class="['h-2 w-2 rounded-full transition-all', currentSlide === index ? 'w-8 bg-white' : 'bg-white/50']"
          ></button>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4 md:grid-cols-4 md:grid-rows-3 md:gap-6 h-[600px]">
        
        <!-- Food Module (Large Square) -->
        <router-link 
          to="/food"
          class="group relative col-span-2 row-span-2 overflow-hidden rounded-3xl bg-orange-500 p-6 text-white shadow-lg transition-transform hover:scale-[1.02] hover:shadow-orange-500/30"
        >
          <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-white/10 blur-3xl transition-all group-hover:bg-white/20"></div>
          <div class="relative z-10 flex h-full flex-col justify-between">
            <div>
              <span class="mb-2 inline-block rounded-full bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur-md">
                30-45 dk Teslimat
              </span>
              <h2 class="text-3xl font-black leading-tight md:text-4xl">
                Acıktın mı?<br />
                <span class="text-orange-100">Sıcak yemek kapında.</span>
              </h2>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium opacity-90">Restoranları Keşfet &rarr;</span>
              <span class="text-6xl">🍔</span>
            </div>
          </div>
          <!-- Decorative Image/Icon -->
          <img 
            src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&q=80" 
            alt="Burger" 
            class="absolute bottom-0 right-0 w-48 translate-x-10 translate-y-10 rotate-12 object-cover opacity-90 transition-transform group-hover:rotate-6 group-hover:scale-110"
          />
        </router-link>

        <!-- Marketplace Module (Wide Rectangle) -->
        <router-link 
          to="/market"
          class="group relative col-span-2 row-span-1 overflow-hidden rounded-3xl bg-blue-600 p-6 text-white shadow-lg transition-transform hover:scale-[1.02] hover:shadow-blue-600/30"
        >
          <div class="absolute -left-10 -bottom-10 h-48 w-48 rounded-full bg-white/10 blur-3xl transition-all group-hover:bg-white/20"></div>
          <div class="relative z-10 flex h-full items-center justify-between">
            <div>
              <span class="mb-2 inline-block rounded-full bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur-md">
                %50'ye varan indirimler
              </span>
              <h2 class="text-2xl font-black md:text-3xl">
                Süper Market<br />
                <span class="text-blue-200">Haftanın Fırsatları</span>
              </h2>
            </div>
            <span class="text-5xl">🛍️</span>
          </div>
        </router-link>

        <!-- Transport Module (Small Square) -->
        <router-link 
          to="/transport"
          class="group relative col-span-1 row-span-1 overflow-hidden rounded-3xl bg-emerald-600 p-5 text-white shadow-lg transition-transform hover:scale-[1.02] hover:shadow-emerald-600/30"
        >
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-emerald-700 opacity-100"></div>
          <div class="relative z-10 flex h-full flex-col justify-between">
            <h2 class="text-xl font-bold">Ulaşım</h2>
            <div class="flex items-end justify-between">
              <span class="text-xs opacity-80">Araç Çağır</span>
              <span class="text-4xl">🚖</span>
            </div>
          </div>
        </router-link>

        <!-- Hotel Module (Small Square) -->
        <router-link 
          to="/hotel"
          class="group relative col-span-1 row-span-1 overflow-hidden rounded-3xl bg-purple-600 p-5 text-white shadow-lg transition-transform hover:scale-[1.02] hover:shadow-purple-600/30"
        >
          <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-purple-700 opacity-100"></div>
          <div class="relative z-10 flex h-full flex-col justify-between">
            <h2 class="text-xl font-bold">Seyahat</h2>
            <div class="flex items-end justify-between">
              <span class="text-xs opacity-80">Otel & Tatil</span>
              <span class="text-4xl">🏨</span>
            </div>
          </div>
        </router-link>

        <!-- Career Module (Wide Rectangle at Bottom) -->
        <router-link 
          to="/career"
          class="group relative col-span-2 row-span-1 overflow-hidden rounded-3xl bg-cyan-600 p-6 text-white shadow-lg transition-transform hover:scale-[1.02] hover:shadow-cyan-600/30"
        >
          <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-black/10 to-transparent"></div>
          <div class="relative z-10 flex items-center justify-between">
            <div>
              <h2 class="text-xl font-bold">Kariyer Fırsatları</h2>
              <p class="text-sm text-cyan-100">Hayalindeki işi veya çalışanı bul.</p>
            </div>
            <span class="text-4xl">💼</span>
          </div>
        </router-link>

        <!-- Services Module (Remaining Space) -->
        <router-link 
          to="/services"
          class="group relative col-span-2 row-span-1 overflow-hidden rounded-3xl bg-slate-800 p-6 text-white shadow-lg transition-transform hover:scale-[1.02] hover:shadow-slate-800/30"
        >
           <div class="relative z-10 flex items-center justify-between">
            <div>
              <h2 class="text-xl font-bold">Hizmetler</h2>
              <p class="text-sm text-slate-300">Temizlik, Tamirat ve daha fazlası.</p>
            </div>
            <span class="text-4xl">🔧</span>
          </div>
        </router-link>

      </div>
    </div>

    <!-- Product Recommendations -->
    <div class="mx-auto max-w-6xl px-4 py-6">
      <div class="mb-4 flex items-center justify-between">
        <h3 class="text-xl font-bold text-slate-800">Sizin İçin Önerilenler</h3>
        <router-link to="/market" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
          Tümünü Gör →
        </router-link>
      </div>
      
      <div v-if="loading" class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
        <div v-for="i in 5" :key="i" class="min-w-[200px] flex-shrink-0 animate-pulse">
          <div class="rounded-xl bg-white p-4 shadow-sm border border-slate-100">
            <div class="mb-3 h-32 w-full rounded-lg bg-slate-200"></div>
            <div class="h-4 w-3/4 rounded bg-slate-200 mb-2"></div>
            <div class="h-3 w-1/2 rounded bg-slate-200 mb-3"></div>
            <div class="h-9 w-full rounded-lg bg-slate-200"></div>
          </div>
        </div>
      </div>
      
      <div v-else class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
        <div 
          v-for="product in recommendedProducts" 
          :key="product.id"
          class="group cursor-pointer overflow-hidden rounded-xl bg-white shadow-sm border border-slate-100 transition-all hover:shadow-lg hover:-translate-y-1"
        >
          <div class="relative aspect-square overflow-hidden bg-slate-100">
            <img 
              :src="product.image" 
              :alt="product.name"
              class="h-full w-full object-cover transition-transform group-hover:scale-110"
              loading="lazy"
            />
            <button class="absolute right-2 top-2 rounded-full bg-white/90 p-2 opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
              <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            </button>
          </div>
          <div class="p-3">
            <h4 class="mb-1 text-sm font-semibold text-slate-800 line-clamp-2">{{ product.name }}</h4>
            <div class="mb-2 flex items-center gap-1">
              <div class="flex text-yellow-400">
                <svg v-for="i in 5" :key="i" class="h-3 w-3" :class="i <= product.rating ? 'fill-current' : 'fill-slate-200'" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              </div>
              <span class="text-xs text-slate-500">({{ product.reviews }})</span>
            </div>
            <p class="mb-3 text-lg font-bold text-slate-900">₺{{ product.price }}</p>
            <button class="w-full rounded-lg bg-blue-600 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-700">
              Sepete Ekle
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const searchQuery = ref('')
const currentSlide = ref(0)
const loading = ref(true)

const popularSearches = [
  'Laptop', 'Telefon', 'Ayakkabı', 'Kıyafet', 'Ev Eşyası'
]

const campaigns = [
  {
    title: 'Yılbaşı Kampanyası',
    description: 'Tüm kategorilerde %50\'ye varan indirimler',
    badge: 'SON GÜNLER',
    cta: 'Fırsatları Gör',
    link: '/campaigns',
    bgClass: 'from-red-600 to-rose-700',
    emoji: '🎁'
  },
  {
    title: 'Ücretsiz Kargo',
    description: '150 TL ve üzeri alışverişlerde kargo bedava',
    badge: 'KARGO BİZDEN',
    cta: 'Alışverişe Başla',
    link: '/market',
    bgClass: 'from-emerald-600 to-teal-700',
    emoji: '🚚'
  },
  {
    title: 'Teknoloji Günleri',
    description: 'Elektronik ürünlerde özel indirim fırsatları',
    badge: 'TEKNOLOJİ',
    cta: 'Keşfet',
    link: '/market?category=electronics',
    bgClass: 'from-blue-600 to-indigo-700',
    emoji: '💻'
  }
]

const recommendedProducts = ref([
  { id: 1, name: 'Kablosuz Bluetooth Kulaklık', price: '299.99', rating: 4, reviews: 128, image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop' },
  { id: 2, name: 'Smart Watch Pro Series 8', price: '2499.99', rating: 5, reviews: 342, image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop' },
  { id: 3, name: 'Ergonomik Ofis Sandalyesi', price: '1899.99', rating: 4, reviews: 89, image: 'https://images.unsplash.com/photo-1506097425191-7ad538b29cef?w=400&h=400&fit=crop' },
  { id: 4, name: 'Taşınabilir SSD 1TB', price: '899.99', rating: 5, reviews: 256, image: 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?w=400&h=400&fit=crop' },
  { id: 5, name: 'Mekanik Gaming Klavye RGB', price: '599.99', rating: 4, reviews: 178, image: 'https://images.unsplash.com/photo-1511385348-c6a7676eaeb4?w=400&h=400&fit=crop' }
])

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'Search', query: { q: searchQuery.value } })
  }
}

let slideInterval: number | null = null

onMounted(() => {
  // Simulate loading
  setTimeout(() => {
    loading.value = false
  }, 1000)
  
  // Auto-play campaign slider
  slideInterval = window.setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % campaigns.length
  }, 5000)
})

onUnmounted(() => {
  if (slideInterval) {
    clearInterval(slideInterval)
  }
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
