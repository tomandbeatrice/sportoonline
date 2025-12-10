<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center gap-4 mb-4">
          <div class="p-4 bg-white/20 backdrop-blur-sm rounded-2xl">
            <span class="text-5xl">🤖</span>
          </div>
          <div>
            <h1 class="text-3xl font-bold">AI Öneri Motoru</h1>
            <p class="text-indigo-100">Sizin için kişiselleştirilmiş ürün önerileri</p>
          </div>
        </div>

        <!-- Profile Summary -->
        <div v-if="profileSummary" class="mt-6 flex flex-wrap gap-3">
          <div class="px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm">
            <span class="opacity-70">Favori Kategoriler:</span>
            <span class="font-semibold ml-1">{{ profileSummary.favorite_categories?.join(', ') || 'Henüz yok' }}</span>
          </div>
          <div class="px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm">
            <span class="opacity-70">Segment:</span>
            <span class="font-semibold ml-1">{{ profileSummary.price_preference }}</span>
          </div>
          <div class="px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm">
            <span class="opacity-70">Durum:</span>
            <span class="font-semibold ml-1">{{ profileSummary.activity_level }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-20">
        <div class="relative">
          <div class="w-20 h-20 border-4 border-indigo-200 rounded-full animate-spin border-t-indigo-600"></div>
          <span class="absolute inset-0 flex items-center justify-center text-3xl">🧠</span>
        </div>
        <p class="mt-4 text-slate-600 font-medium">AI önerileri hesaplanıyor...</p>
        <p class="text-sm text-slate-400">Satın alma geçmişiniz analiz ediliyor</p>
      </div>

      <!-- Recommendations -->
      <div v-else>
        <!-- Action Bar -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-xl font-bold text-slate-900">Size Özel Öneriler</h2>
            <p class="text-sm text-slate-500">{{ recommendations.length }} ürün önerisi bulundu</p>
          </div>
          <button 
            @click="refreshRecommendations"
            :disabled="refreshing"
            class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition"
          >
            <span :class="{ 'animate-spin': refreshing }">🔄</span>
            Yenile
          </button>
        </div>

        <!-- Guest Message -->
        <div v-if="isGuest" class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
          <div class="flex items-center gap-3">
            <span class="text-2xl">👤</span>
            <div>
              <p class="font-medium text-amber-800">Giriş yaparak daha kişisel öneriler alın!</p>
              <p class="text-sm text-amber-600">Şu an popüler ürünleri gösteriyoruz</p>
            </div>
            <router-link to="/login" class="ml-auto px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition">
              Giriş Yap
            </router-link>
          </div>
        </div>

        <!-- Recommendations Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div
            v-for="item in recommendations"
            :key="item.product_id"
            class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all group"
          >
            <!-- Product Image -->
            <div class="relative aspect-square bg-slate-100">
              <img 
                :src="item.product?.image || '/images/placeholder.jpg'" 
                :alt="item.product?.name"
                class="w-full h-full object-cover"
              />
              <!-- AI Score Badge -->
              <div class="absolute top-3 right-3 px-2 py-1 bg-indigo-600 text-white text-xs font-bold rounded-full">
                AI Skor: {{ item.score }}
              </div>
              <!-- Reason Badge -->
              <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/60 to-transparent p-3">
                <p class="text-white text-xs">{{ item.reason }}</p>
              </div>
            </div>

            <!-- Product Info -->
            <div class="p-4">
              <h3 class="font-semibold text-slate-900 mb-1 line-clamp-2 group-hover:text-indigo-600 transition">
                {{ item.product?.name }}
              </h3>
              
              <div class="flex items-center gap-1 mb-2">
                <span class="text-yellow-500">⭐</span>
                <span class="text-sm text-slate-600">{{ item.product?.rating || '4.5' }}</span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-lg font-bold text-indigo-600">
                  {{ formatPrice(item.product?.price) }}
                </span>
                <button 
                  @click="addToCart(item.product)"
                  class="p-2 bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition"
                >
                  🛒
                </button>
              </div>

              <!-- Source Tags -->
              <div class="flex flex-wrap gap-1 mt-3">
                <span 
                  v-for="source in item.sources" 
                  :key="source"
                  class="px-2 py-0.5 text-xs rounded-full"
                  :class="getSourceClass(source)"
                >
                  {{ getSourceLabel(source) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="recommendations.length === 0" class="text-center py-20">
          <span class="text-6xl mb-4 block">🔍</span>
          <h3 class="text-xl font-semibold text-slate-900 mb-2">Henüz öneri bulunamadı</h3>
          <p class="text-slate-500 mb-6">Alışveriş yaptıkça size özel öneriler oluşturulacak</p>
          <router-link to="/products" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            Ürünlere Göz At
          </router-link>
        </div>
      </div>

      <!-- How It Works Section -->
      <div class="mt-16">
        <h2 class="text-xl font-bold text-slate-900 mb-6 text-center">AI Öneriler Nasıl Çalışır?</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="text-center p-6 bg-white rounded-2xl shadow-sm">
            <span class="text-4xl mb-3 block">📊</span>
            <h3 class="font-semibold text-slate-900 mb-2">Analiz</h3>
            <p class="text-sm text-slate-500">Satın alma geçmişinizi ve tercihlerinizi analiz ederiz</p>
          </div>
          <div class="text-center p-6 bg-white rounded-2xl shadow-sm">
            <span class="text-4xl mb-3 block">👥</span>
            <h3 class="font-semibold text-slate-900 mb-2">Benzer Kullanıcılar</h3>
            <p class="text-sm text-slate-500">Sizin gibi alıcıların tercihlerini inceleriz</p>
          </div>
          <div class="text-center p-6 bg-white rounded-2xl shadow-sm">
            <span class="text-4xl mb-3 block">📈</span>
            <h3 class="font-semibold text-slate-900 mb-2">Trend Takibi</h3>
            <p class="text-sm text-slate-500">Popüler ürünleri takip ederiz</p>
          </div>
          <div class="text-center p-6 bg-white rounded-2xl shadow-sm">
            <span class="text-4xl mb-3 block">🎯</span>
            <h3 class="font-semibold text-slate-900 mb-2">Kişiselleştirme</h3>
            <p class="text-sm text-slate-500">Size en uygun ürünleri sunarız</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const loading = ref(true)
const refreshing = ref(false)
const isGuest = ref(false)
const recommendations = ref<any[]>([])
const profileSummary = ref<any>(null)

const fetchRecommendations = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/v1/ai/recommendations')
    
    if (response.data.is_guest) {
      isGuest.value = true
      recommendations.value = response.data.guest_recommendations?.recommendations || []
    } else {
      recommendations.value = response.data.recommendations || []
      profileSummary.value = response.data.profile_summary
    }
  } catch (error) {
    console.error('AI önerileri yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const refreshRecommendations = async () => {
  refreshing.value = true
  try {
    await axios.get('/api/v1/ai/recommendations/refresh')
    await fetchRecommendations()
  } catch (error) {
    console.error('Öneriler yenilenemedi:', error)
  } finally {
    refreshing.value = false
  }
}

const addToCart = async (product: any) => {
  try {
    await axios.post('/api/v1/cart', { product_id: product.id, quantity: 1 })
    // Toast notification göster
  } catch (error) {
    console.error('Sepete eklenemedi:', error)
  }
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(price || 0)
}

const getSourceClass = (source: string) => {
  const classes: Record<string, string> = {
    'purchase': 'bg-green-100 text-green-700',
    'collaborative': 'bg-blue-100 text-blue-700',
    'view_history': 'bg-purple-100 text-purple-700',
    'trending': 'bg-orange-100 text-orange-700',
  }
  return classes[source] || 'bg-slate-100 text-slate-700'
}

const getSourceLabel = (source: string) => {
  const labels: Record<string, string> = {
    'purchase': 'Geçmiş Alışveriş',
    'collaborative': 'Benzer Alıcılar',
    'view_history': 'Görüntüleme',
    'trending': 'Trend',
  }
  return labels[source] || source
}

onMounted(() => {
  fetchRecommendations()
})
</script>
