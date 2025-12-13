<!-- 
  MarketplaceHome.vue - Ana Marketplace Sayfası
  Hiyerarşi:
  1. Header (Logo, Auth, Dil, Sesli Asistan)
  2. Hizmet Sekmeleri (Mağaza, Yemek, Otel, Hizmet, Turbo)
  3. Kampanya Slider
  4. Aktif Siparişler
  5. Bundle Teklifleri
-->
<template>
  <div class="marketplace-home min-h-screen bg-slate-50">
    
    <!-- ═══════════════════════════════════════════════════════════════════
         🔗 HİZMET SEKMELERİ - Mağaza, Yemek, Otel, Hizmet, Turbo
         ═══════════════════════════════════════════════════════════════════ -->
    <MarketplaceNavigation />

    <!-- ═══════════════════════════════════════════════════════════════════
         🦸 HERO SECTION - Sade, Açık Arka Plan
         ═══════════════════════════════════════════════════════════════════ -->
    <MarketplaceHero />

    <!-- ═══════════════════════════════════════════════════════════════════
         🎯 MODÜL GRİD - Aktif Hizmetler
         ═══════════════════════════════════════════════════════════════════ -->
    <MarketplaceServices @toggle-group-order="toggleGroupOrder" />

    <!-- ═══════════════════════════════════════════════════════════════════
         🏷️ KAMPANYA SLIDER
         ═══════════════════════════════════════════════════════════════════ -->
    <MarketplaceCampaigns />

    <!-- ═══════════════════════════════════════════════════════════════════
         🚀 TURBO MOD - Aylık Yarışma Paneli
         ═══════════════════════════════════════════════════════════════════ -->
    <MarketplaceTurbo />

    <MarketplaceCategories />

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

      <!-- ═══════════════════════════════════════════════════════════════════
           2. 📦 AKTİF SİPARİŞLER - Kritik Bilgi
           ═══════════════════════════════════════════════════════════════════ -->
        <LazySection v-if="flags['homepage.activeOrders'] !== false">
          <MarketplaceActiveOrders />
        </LazySection>

      <!-- ═══════════════════════════════════════════════════════════════════
           3. 🎯 ÖZEL TEKLİFLER - Tablı Yapı
           ═══════════════════════════════════════════════════════════════════ -->
      <LazySection v-if="flags['homepage.offers'] !== false">
        <MarketplaceOffers />
      </LazySection>

      <LazySection v-if="flags['homepage.brands'] !== false">
        <MarketplaceBrands />
      </LazySection>

      <!-- ═══════════════════════════════════════════════════════════════════
           🥗 SAĞLIKLI YAŞAM & SPORCU MUTFAĞI - Yeni Özellik
           ═══════════════════════════════════════════════════════════════════ -->
      <LazySection v-if="flags['homepage.healthy'] !== false">
        <MarketplaceHealthy />
      </LazySection>

      <!-- ═══════════════════════════════════════════════════════════════════
           ✅ GÜNLÜK GÖREVLER - Lifestyle Hub
           ═══════════════════════════════════════════════════════════════════ -->
      <LazySection v-if="flags['homepage.tasks'] !== false">
        <MarketplaceTasks />
      </LazySection>

      <LazySection v-if="flags['homepage.bundles'] !== false">
        <MarketplaceBundles />
      </LazySection>

      <LazySection v-if="flags['homepage.recentlyViewed'] !== false">
        <MarketplaceRecentlyViewed />
      </LazySection>

      <LazySection v-if="flags['homepage.trending'] !== false">
        <MarketplaceTrending />
      </LazySection>

      <LazySection v-if="flags['homepage.flashSales'] !== false">
        <MarketplaceFlashSales />
      </LazySection>

      <LazySection v-if="flags['homepage.collections'] !== false">
        <MarketplaceCollections />
      </LazySection>

      <!-- ═══════════════════════════════════════════════════════════════════
           📅 ALIŞVERİŞ ETKİNLİKLERİ - Yeni Özellik
           ═══════════════════════════════════════════════════════════════════ -->
      <LazySection v-if="flags['homepage.events'] !== false">
        <MarketplaceEvents />
      </LazySection>

      <!-- ═══════════════════════════════════════════════════════════════════
           💎 AYRICALIKLI HİZMETLER - Premium Features
           ═══════════════════════════════════════════════════════════════════ -->
      <LazySection v-if="flags['homepage.premium'] !== false">
        <MarketplacePremium />
      </LazySection>

      <LazySection v-if="flags['homepage.blog'] !== false">
        <MarketplaceBlog />
      </LazySection>

    </main>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="fixed bottom-0 left-0 right-0 md:hidden z-50 bg-white border-t border-slate-200 safe-area-bottom">
      <div class="flex items-center justify-around h-16">
        <!-- Home -->
        <router-link 
          to="/"
          class="flex flex-col items-center justify-center flex-1 py-2 transition-colors"
          :class="$route.path === '/' ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          <span class="text-xs mt-1 font-medium">Ana Sayfa</span>
        </router-link>

        <!-- Search -->
        <button 
          @click="showMobileSearch = true"
          class="flex flex-col items-center justify-center flex-1 py-2 text-slate-400 hover:text-slate-600 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <span class="text-xs mt-1 font-medium">Ara</span>
        </button>

        <!-- Cart (Center - Larger) -->
        <router-link 
          to="/cart"
          class="relative flex flex-col items-center justify-center flex-1 py-2"
          :class="$route.path === '/cart' ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'"
        >
          <div class="relative">
            <div class="w-12 h-12 -mt-6 bg-indigo-600 rounded-full flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <span 
              v-if="cartItemCount > 0"
              class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse"
            >
              {{ cartItemCount > 9 ? '9+' : cartItemCount }}
            </span>
          </div>
          <span class="text-xs mt-1 font-medium">Sepet</span>
        </router-link>

        <!-- Orders -->
        <router-link 
          to="/orders"
          class="flex flex-col items-center justify-center flex-1 py-2 transition-colors"
          :class="$route.path.startsWith('/orders') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
          </svg>
          <span class="text-xs mt-1 font-medium">Siparişler</span>
        </router-link>

        <!-- Profile -->
        <router-link 
          to="/user"
          class="flex flex-col items-center justify-center flex-1 py-2 transition-colors"
          :class="$route.path === '/user' ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          <span class="text-xs mt-1 font-medium">Profil</span>
        </router-link>
      </div>
    </nav>

    <!-- Mobile Search Modal -->
    <Transition name="fade">
      <div 
        v-if="showMobileSearch" 
        class="fixed inset-0 z-50 bg-black/50 md:hidden"
        @click="showMobileSearch = false"
      >
        <div 
          class="absolute top-0 left-0 right-0 bg-white p-4 safe-area-top"
          @click.stop
        >
          <div class="flex items-center gap-3">
            <div class="flex-1 relative">
              <input
                ref="mobileSearchInput"
                v-model="searchQuery"
                type="text"
                :placeholder="t('search.placeholder')"
                class="w-full pl-10 pr-4 py-3 bg-slate-100 border-0 rounded-xl text-base focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all"
                @keyup.enter="performSearch; showMobileSearch = false"
                autofocus
              />
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
            <button 
              @click="showMobileSearch = false"
              class="px-4 py-3 text-slate-600 font-medium"
            >
              İptal
            </button>
          </div>
          <!-- Popüler Aramalar -->
          <div v-if="popularSearches.length > 0" class="mt-4">
            <p class="text-xs text-slate-500 mb-2 flex items-center gap-1">
              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
              Popüler Aramalar
            </p>
            <div class="flex flex-wrap gap-2">
              <button 
                v-for="(search, index) in popularSearches.slice(0, 6)" 
                :key="index"
                @click="searchQuery = search; performSearch(); showMobileSearch = false"
                class="px-3 py-1.5 bg-slate-100 hover:bg-indigo-100 text-slate-700 hover:text-indigo-700 text-sm rounded-full transition-colors"
              >
                {{ search }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Group Order Modal -->
    <Transition name="fade">
      <div 
        v-if="showGroupOrder" 
        class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
        @click.self="toggleGroupOrder"
      >
        <div 
          class="max-w-4xl w-full max-h-[90vh] overflow-y-auto"
          @click.stop
        >
          <FoodGroupOrder />
        </div>
      </div>
    </Transition>

    <!-- Spacer for bottom navigation -->
    <div class="h-16 md:hidden"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import MarketplaceNavigation from './MarketplaceNavigation.vue'
import MarketplaceHero from './MarketplaceHero.vue'
import MarketplaceServices from './MarketplaceServices.vue'
import MarketplaceCampaigns from './MarketplaceCampaigns.vue'
import MarketplaceTurbo from './MarketplaceTurbo.vue'
import MarketplaceCategories from './MarketplaceCategories.vue'
import MarketplaceActiveOrders from './MarketplaceActiveOrders.vue'
import MarketplaceOffers from './MarketplaceOffers.vue'
import MarketplaceBundles from './MarketplaceBundles.vue'
import MarketplaceRecentlyViewed from './MarketplaceRecentlyViewed.vue'
import MarketplaceTrending from './MarketplaceTrending.vue'
import MarketplaceFlashSales from './MarketplaceFlashSales.vue'
import MarketplaceBrands from './MarketplaceBrands.vue'
import MarketplaceHealthy from './MarketplaceHealthy.vue'
import MarketplaceCollections from './MarketplaceCollections.vue'
import MarketplaceBlog from './MarketplaceBlog.vue'
import MarketplaceTasks from './MarketplaceTasks.vue'
import MarketplaceEvents from './MarketplaceEvents.vue'
import MarketplacePremium from './MarketplacePremium.vue'
import FoodGroupOrder from './FoodGroupOrder.vue'
import { useTracking } from '@/composables/useTracking'
import { useFeatureFlags } from '@/services/featureFlags'
import { useI18n } from 'vue-i18n'
import LazySection from '@/components/common/LazySection.vue'
import { useCurrencyStore } from '@/stores/currency'

// Tracking system
const { getTrendingProducts: fetchTrendingFromAI, getPopularSearches } = useTracking()
const { flags } = useFeatureFlags()
const { t, locale } = useI18n()  // Extract locale here for reuse
const currencyStore = useCurrencyStore()

// Simple i18n helpers
const formatCurrency = (amount: number) => `₺${amount.toFixed(2)}`
const formatDate = (date: string) => new Date(date).toLocaleDateString('tr-TR')
// Types

// Removed: Review, Product, Restaurant, Hotel interfaces - sections removed from template

// Composables
import { useAuthStore } from '@/stores/auth'
const router = useRouter()
const authStore = useAuthStore()

// ═══════════════════════════════════════════════════════════════════
// State
// ═══════════════════════════════════════════════════════════════════

// Header & Auth
const searchQuery = ref('')
const isAuthenticated = computed(() => authStore.isAuthenticated)
const userInitials = computed(() => {
  const name = authStore.user?.name || ''
  return name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2) || 'U'
})
const voiceAssistantActive = ref(false)
const showLanguageMenu = ref(false)
const currentLanguage = ref('tr')

// Languages
const languages = [
  { code: 'tr', name: 'Türkçe', flag: '🇹🇷' },
  { code: 'en', name: 'English', flag: '🇬🇧' },
  { code: 'de', name: 'Deutsch', flag: '🇩🇪' },
  { code: 'ar', name: 'العربية', flag: '🇸🇦' }
]

const currentLanguageFlag = computed(() => languages.find(l => l.code === currentLanguage.value)?.flag || '🌐')
const currentLanguageCode = computed(() => currentLanguage.value.toUpperCase())

// Main Services
const activeService = ref('marketplace')
const mainServices = ref<any[]>([])

const fetchServices = async () => {
  try {
    const response = await axios.get('/api/v1/services')
    mainServices.value = response.data.map((service: any) => ({
      id: service.slug,
      name: service.name,
      icon: service.icon,
      badge: service.badge,
      badgeClass: service.badge_class
    }))
  } catch (error) {
    console.error('Error fetching services:', error)
    // Fallback to default if API fails
    mainServices.value = [
      { id: 'marketplace', name: 'Mağaza', icon: '🛒', badge: null, badgeClass: '' },
      { id: 'food', name: 'Yemek', icon: '🍔', badge: 'Yeni', badgeClass: 'bg-green-100 text-green-600' },
      { id: 'hotel', name: 'Otel', icon: '🏨', badge: null, badgeClass: '' },
      { id: 'services', name: 'Hizmet', icon: '🔧', badge: null, badgeClass: '' },
      { id: 'turbo', name: 'Turbo', icon: '🚀', badge: 'CANLI', badgeClass: 'bg-violet-100 text-violet-600' }
    ]
  }
}

// Popular Categories removed (moved to MarketplaceCategories.vue)

// Featured Brands removed (moved to MarketplaceBrands.vue)

// Trending Products removed (moved to MarketplaceTrending.vue)

// Recently Viewed Products removed (moved to MarketplaceRecentlyViewed.vue)

// Popular Searches (AI Tracking)
const popularSearches = ref<string[]>([])

const fetchPopularSearches = async () => {
  try {
    const searches = await getPopularSearches(8)
    popularSearches.value = searches.map((s: any) => s.query || s)
  } catch (error) {
    console.error('Popüler aramalar alınamadı:', error)
    // Fallback
    popularSearches.value = ['Spor ayakkabı', 'Fitness', 'Yoga', 'Koşu bandı', 'Dumbell']
  }
}

// Flash Sales removed (moved to MarketplaceFlashSales.vue)

// Collections removed (moved to MarketplaceCollections.vue)

// Blog Posts removed (moved to MarketplaceBlog.vue)

// Premium Features removed (moved to MarketplacePremium.vue)

// Announcements (Duyurular)
const announcements = ref([
  '🎉 Yeni Yıl Kampanyası: Tüm ürünlerde %70\'e varan indirimler başladı!',
  '⚡ Flash Sale: Elektronik kategorisinde bugün sınırlı sayıda ürünlerde ekstra %30 indirim',
  '🚚 Ücretsiz Kargo: 500 TL ve üzeri tüm siparişlerde kargo bedava',
  '💳 Taksit Fırsatı: Kredi kartına 12 taksit imkanı',
  '🎁 Hediye Çeki: Her 1000 TL\'lik alışverişe 100 TL hediye çeki kazanın'
])

// Hero Content removed (moved to MarketplaceHero.vue)

// Live Metrics
const liveMetrics = ref([
  { icon: '👥', label: 'Aktif Kullanıcı', value: '12,847', trend: '+8.2%', trendUp: true },
  { icon: '🛒', label: 'Sepetteki Ürün', value: '4,231', trend: '+12%', trendUp: true },
  { icon: '📦', label: 'Günlük Sipariş', value: '892', trend: '+5.1%', trendUp: true },
  { icon: '⭐', label: 'Ortalama Puan', value: '4.8/5', trend: '+0.2', trendUp: true }
])

// Shopping Events (Now Active Campaigns) removed (moved to MarketplaceEvents.vue)

// Daily Tasks removed (moved to MarketplaceTasks.vue)

// Campaigns removed (moved to MarketplaceCampaigns.vue)

// Turbo Competition removed (moved to MarketplaceTurbo.vue)

// Orders removed (moved to MarketplaceActiveOrders.vue)

// Bundles removed (moved to MarketplaceBundles.vue)

// Mobile Bottom Navigation
const showMobileSearch = ref(false)
const mobileSearchInput = ref<HTMLInputElement | null>(null)

// Group Order
const showGroupOrder = ref(false)

// Cart
const cartItemCount = ref(0)

// Special Offers removed (moved to MarketplaceOffers.vue)

// ═══════════════════════════════════════════════════════════════════
// Computed
// ═══════════════════════════════════════════════════════════════════



// ═══════════════════════════════════════════════════════════════════
// Methods
// ═══════════════════════════════════════════════════════════════════

// Header Methods
const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ path: '/search', query: { q: searchQuery.value } })
  }
}

const toggleVoiceAssistant = () => {
  voiceAssistantActive.value = !voiceAssistantActive.value
  if (voiceAssistantActive.value) {
    startVoiceRecognition()
  } else {
    stopVoiceRecognition()
  }
}

// Voice Recognition using Web Speech API
let recognition: any = null

const startVoiceRecognition = () => {
  // Check if browser supports Web Speech API
  const SpeechRecognition = (window as any).SpeechRecognition || (window as any).webkitSpeechRecognition
  
  if (!SpeechRecognition) {
    alert('Tarayıcınız ses tanıma özelliğini desteklemiyor. Lütfen Chrome veya Edge kullanın.')
    voiceAssistantActive.value = false
    return
  }
  
  try {
    recognition = new SpeechRecognition()
    recognition.lang = currentLanguage.value === 'tr' ? 'tr-TR' : 'en-US'
    recognition.continuous = false
    recognition.interimResults = false
    recognition.maxAlternatives = 1
    
    recognition.onresult = (event: any) => {
      const transcript = event.results[0][0].transcript
      searchQuery.value = transcript
      console.log('Voice search:', transcript)
      
      // Automatically perform search
      setTimeout(() => {
        performSearch()
        voiceAssistantActive.value = false
      }, 500)
    }
    
    recognition.onerror = (event: any) => {
      console.error('Voice recognition error:', event.error)
      voiceAssistantActive.value = false
      
      if (event.error === 'no-speech') {
        alert('Ses algılanamadı. Lütfen tekrar deneyin.')
      } else if (event.error === 'not-allowed') {
        alert('Mikrofon erişimi reddedildi. Lütfen tarayıcı ayarlarından mikrofon iznini açın.')
      }
    }
    
    recognition.onend = () => {
      voiceAssistantActive.value = false
    }
    
    recognition.start()
  } catch (error) {
    console.error('Failed to start voice recognition:', error)
    voiceAssistantActive.value = false
  }
}

const stopVoiceRecognition = () => {
  if (recognition) {
    recognition.stop()
    recognition = null
  }
}

const changeLanguage = (code: string) => {
  currentLanguage.value = code
  showLanguageMenu.value = false
  
  // Update i18n locale
  locale.value = code
  
  // Store preference in localStorage
  localStorage.setItem('language', code)
  
  // Update HTML lang attribute
  document.documentElement.lang = code
  
  // If voice recognition is active, restart with new language
  if (voiceAssistantActive.value && recognition) {
    stopVoiceRecognition()
    setTimeout(() => startVoiceRecognition(), 100)
  }
  
  console.log('Language changed to:', code)
}

const selectService = (serviceId: string) => {
  activeService.value = serviceId
  
  // Navigate to service page
  const serviceRoutes: Record<string, string> = {
    marketplace: '/',
    food: '/food',
    hotel: '/hotels',
    rides: '/rides',
    services: '/services',
    career: '/career'
  }
  
  if (serviceRoutes[serviceId] && serviceId !== 'marketplace') {
    router.push(serviceRoutes[serviceId])
  }
}

const openFeature = (featureId: string) => {
  const featureRoutes: Record<string, string> = {
    'kitchen': '/sport-kitchen',
    'virtual-try': '/virtual-fitting',
    'ai-suggest': '/ai-recommendations',
    'nutrition': '/nutrition-consultant',
    'trainer': '/personal-trainer',
    'live-support': '/support'
  }
  
  if (featureRoutes[featureId]) {
    router.push(featureRoutes[featureId])
  }
}

// Admin navigation
const goToAdmin = () => {
  const userRole = localStorage.getItem('role')
  if (userRole === 'admin') {
    router.push('/admin/marketplace')
  } else {
    alert('Admin erişimi gereklidir')
  }
}

// Campaign Methods removed (moved to MarketplaceCampaigns.vue)

// Order Methods removed (moved to MarketplaceActiveOrders.vue)

// Bundle Methods removed (moved to MarketplaceBundles.vue)

// Group Order Methods
const toggleGroupOrder = () => {
  showGroupOrder.value = !showGroupOrder.value
}

// ═══════════════════════════════════════════════════════════════════
// Data Fetching
// ═══════════════════════════════════════════════════════════════════





const loadCartCount = async () => {
  try {
    const response = await fetch('/api/cart/count')
    const data = response.ok ? await response.json() : null
    cartItemCount.value = data?.count || 0
  } catch (error) {
    console.error('Sepet sayısı alınamadı:', error)
  }
}

// ═══════════════════════════════════════════════════════════════════
// Lifecycle
// ═══════════════════════════════════════════════════════════════════

onMounted(async () => {
  // Restore language preference
  const savedLanguage = localStorage.getItem('language')
  if (savedLanguage && ['tr', 'en', 'ar', 'de'].includes(savedLanguage)) {
    currentLanguage.value = savedLanguage
    locale.value = savedLanguage
    document.documentElement.lang = savedLanguage
  }
  
  // Load all data in parallel
  await Promise.all([
    fetchServices(),
    fetchPopularSearches(),
    currencyStore.fetchExchangeRates(currencyStore.selectedCurrency)
  ])
  

})

onUnmounted(() => {
  // Cleanup voice recognition
  stopVoiceRecognition()
})
</script>

<style scoped>
/* Smooth scrolling for orders container */
.orders-container {
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}

.orders-container::-webkit-scrollbar {
  display: none;
}

.order-card {
  scroll-snap-align: start;
}

/* Hide scrollbar for bundle carousel */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Animation delays */
.delay-300 {
  animation-delay: 300ms;
}

/* Gradient text */
.gradient-text {
  background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Line clamp for reviews */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Dropdown animation */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* Fade animation for mobile search */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Safe area for iPhone notch */
.safe-area-top {
  padding-top: env(safe-area-inset-top);
}

.safe-area-bottom {
  padding-bottom: env(safe-area-inset-bottom);
}

/* Touch scroll optimization */
.touch-pan-x {
  touch-action: pan-x;
}

/* Skeleton animation */
@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

/* Marquee animation for announcements */
@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.animate-marquee {
  animation: marquee 30s linear infinite;
}

.animate-marquee:hover {
  animation-play-state: paused;
}

/* Smooth image loading */
img {
  opacity: 1;
  transition: opacity 0.3s ease;
}

img[loading="lazy"] {
  opacity: 0;
}

img[loading="lazy"].loaded {
  opacity: 1;
}

/* Bottom navigation shadow */
nav.fixed.bottom-0 {
  box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
}

/* Main content padding for bottom nav */
main {
  padding-bottom: 1rem;
}

@media (max-width: 768px) {
  main {
    padding-bottom: 5rem;
  }
}
</style>
