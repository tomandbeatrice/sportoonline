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
  </div>
</template>
                <option value="price_desc">Fiyat (Azalan)</option>
                <option value="popular">En Çok Satanlar</option>
              </select>
            </div>

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
const productStore = useProductStore()
const { products: storeProducts } = storeToRefs(productStore)
const cartStore = useCartStore()
const orderStore = useOrderStore()
const badgeClasses = badgeToneClasses
const badgeDebug = isBadgeDebugEnabled()

// State
const products = ref<any[]>([])
const categories = ref<any[]>([])
const availableColors = ref<string[]>([])
const availableSizes = ref<string[]>([])
const availableBrands = ref<string[]>([])
const loading = ref(false)
const totalProducts = ref(0)
const currentPage = ref(1)
const totalPages = ref(1)
const favoriteSet = reactive(new Set<number | string>())

const filters = reactive({
  search: '',
  category: null as number | null,
  minPrice: null as number | null,
  maxPrice: null as number | null,
  colors: [] as string[],
  sizes: [] as string[],
  brands: [] as string[],
  sort: 'newest'
})

const heroContent = {
  badge: 'Sportoon Express',
  title: 'Spor alışverişini 120 dakikanın altına indiren tek pazar yeri',
  subtitle: 'Gerçek stok görünürlüğü, mikro fulfillment ve express partner ağıyla siparişi verdiğin anda hazırlayıp 2 saatte kapına ulaştırıyoruz.',
  primaryCta: { label: 'Şimdi alışveriş yap', to: '/login' },
  secondaryCta: { label: 'Satıcı paneli', to: '/seller/dashboard' },
  stats: [
    { label: 'Aktif Satıcı', value: '12.4K' },
    { label: 'Günlük Sipariş', value: '58K' },
    { label: 'Canlı Kampanya', value: '142' }
  ],
  dailyDeal: 'Express teslimatta %30 sepet indirimi',
  backgroundImage: 'https://images.unsplash.com/photo-1521412644187-c49fa049e84d?auto=format&fit=crop&w=1500&q=80'
}

const promoCards = [
  {
    badge: 'Müşteri Fırsatı',
    title: 'Süper hafta sonu +%15 bonus',
    copy: 'Cart kampanyaları ile 750 ₺ üzeri alışverişte ekstra bonus kazan.',
    cta: 'Bonus detayları',
    to: '/campaigns',
    bg: 'bg-gradient-to-br from-fuchsia-600 to-pink-500'
  },
  {
    badge: 'Yeni Satıcı',
    title: 'İlk 90 gün komisyon indirimi',
    copy: 'Mağaza açılışında esnek kargo ve reklam kredisi hediyesi.',
    cta: 'Satıcı rehberi',
    to: '/seller/guide',
    bg: 'bg-gradient-to-br from-blue-600 to-indigo-600'
  },
  {
    badge: 'Express Teslimat',
    title: '2 saat içinde kapında',
    copy: 'Metro şehirlerinde hızlı teslimat partnerleriyle tanış.',
    cta: 'Bölgemi kontrol et',
    to: '/orders',
    bg: 'bg-gradient-to-br from-emerald-500 to-lime-500'
  }
]

const campaigns = [
  {
    badge: 'FLASH',
    title: 'Koşu ekipmanlarında +%25',
    copy: 'Nike, Adidas, Hoka mağazalarında sepette ekstra indirim.',
    window: '17-20 Kasım',
    discount: '%25 + Kargo',
    status: 'Canlı',
    statusTone: 'success',
    reach: '1.3M kullanıcı',
    lift: '+18% dönüşüm',
    channel: 'Sportoonline · Ana sayfa',
    phase: 'live',
    cta: { label: 'Flash kampanyaya katıl', to: '/campaigns/flash-run' },
    metrics: [
      { label: 'Adım hedefi', value: '750K', trend: '+65K bugün' },
      { label: 'Sepet artışı', value: '+32%', trend: '+6% haftalık' }
    ]
  },
  {
    badge: 'EXPRESS',
    title: 'Şehir içi express +%40 boost',
    copy: 'Mikro fulfillment ağında anlık sipariş sıçramasını yönetiyoruz.',
    window: 'Bugün · 12:00-18:00',
    discount: 'Teslimat ücreti hediye',
    status: 'Canlı uyarı',
    statusTone: 'alert',
    reach: '820K kullanıcı',
    lift: '-5% SLA',
    channel: 'Express ağ · İstanbul',
    phase: 'live',
    metrics: [
      { label: 'Kurye kapasitesi', value: '92%', trend: '-3% son saat' },
      { label: 'Sipariş yükü', value: '+40%', trend: '+12% pik' }
    ]
  },
  {
    badge: 'YENİ',
    title: 'Outdoor festival koleksiyonu',
    copy: 'Çadır ve aksesuarlarda set kombin indirimi.',
    window: 'Kasım boyunca',
    discount: '%35’e varan',
    status: 'Planlandı',
    statusTone: 'neutral',
    progress: 54,
    channel: 'Sportoonline · Mağaza',
    phase: 'planned'
  },
  {
    badge: 'VIP',
    title: 'Sadakat kulübü erken erişim',
    copy: 'Gold üyeler için sepet sınırı olmadan hediye çekleri.',
    window: 'Her Cuma',
    discount: '+%10 Bonus',
    status: 'Planlanan',
    statusTone: 'neutral',
    progress: 82,
    channel: 'Sadakat · Mobil',
    phase: 'planned'
  }
]

const announcements = [
  'Canlı: Express kargo yoğunluk uyarısı',
  'Duyuru: Satıcı paneli v3 yayında',
  'Yeni kampanya: Koşu ekipmanlarında +%25',
  'Finans: Haftalık ödeme raporu gönderildi'
]

const orderTrackingHighlights = [
  { icon: '📦', title: 'Anlık sipariş akışı', description: 'Sipariş başına SLA ve teslimat kilometre taşı' },
  { icon: '🚚', title: 'Kurye rota görünürlüğü', description: 'Gerçek zamanlı kurye lokasyonu' },
  { icon: '🏪', title: 'Mağaza hazırlık', description: 'Hazırlanma süresi 14 dk ortalama' },
  { icon: '⚠️', title: 'Yoğunluk alarmı', description: 'Pik saatlerde otomatik kapasite uyarıları' }
]

const liveMetrics = [
  { label: 'Aktif Satıcı', value: '12.4K', delta: 12, caption: 'Son 24 saat', icon: '🛍️', tone: 'orange' },
  { label: 'Günlük Sipariş', value: '58.2K', delta: 8, caption: 'Express ağ', icon: '⚡', tone: 'emerald' },
  { label: 'Canlı Kampanya', value: '142', delta: 5, caption: 'Kampanya stüdyosu', icon: '🎯', tone: 'sky' }
]

const brandPartners = [
  { name: 'JetLog', tagline: '2s teslimat', emoji: '⚡', sla: '1.2g SLA' },
  { name: 'FitWorld', tagline: 'Spor giyim', emoji: '🏃', sla: '1.8g SLA' },
  { name: 'PeakTech', tagline: 'Wearable', emoji: '⌚', sla: '1.4g SLA' },
  { name: 'MetroCargo', tagline: 'Soğuk zincir', emoji: '🚚', sla: '2.0g SLA' },
  { name: 'TrailX', tagline: 'Outdoor', emoji: '⛰️', sla: '1.6g SLA' },
  { name: 'UrbanRide', tagline: 'Mikro mobilite', emoji: '🛴', sla: '1.3g SLA' }
]

const brandStats = [
  { label: 'Ağ kapsaması', value: '48 şehir', delta: '+6 şehir' },
  { label: 'Express partner', value: '320', delta: '+35 partner' },
  { label: 'SLA ortalaması', value: '1.6 gün', delta: '+0.2 iyileşme' }
]

const brandBadges = [
  { label: 'ISO 27001', description: 'Tüm hub’larda sertifikalı' },
  { label: 'Same-day ready', description: '16 şehirde T+0 teslimat' },
  { label: 'Pharma cold chain', description: 'GDP compliance' },
  { label: 'AI Forecast', description: 'Talep tahmin motoru' }
]

const brandCta = {
  eyebrow: 'Partner onboarding',
  title: 'Sportoon Express ağına 12 günde katıl',
  description: 'Tek entegrasyonla stok görünürlüğü, SLA izleme ve mikro-fullfillment kapasitesine eriş.',
  button: 'Programa başvur',
  meta: 'Ücretsiz hızlandırma paketi'
}

// Methods
async function fetchProducts() {
  loading.value = true
  try {
    // Mock filtering logic using storeProducts
    let filtered = [...storeProducts.value]

    // Search
    if (filters.search) {
      const q = filters.search.toLowerCase()
      filtered = filtered.filter(p => p.name.toLowerCase().includes(q) || p.description?.toLowerCase().includes(q))
    }

    // Category
    if (filters.category) {
      filtered = filtered.filter(p => p.category?.id === filters.category)
    }

    // Price
    if (filters.minPrice) filtered = filtered.filter(p => p.price >= filters.minPrice!)
    if (filters.maxPrice) filtered = filtered.filter(p => p.price <= filters.maxPrice!)

    // Sort
    if (filters.sort === 'price_asc') filtered.sort((a, b) => a.price - b.price)
    else if (filters.sort === 'price_desc') filtered.sort((a, b) => b.price - a.price)
    else if (filters.sort === 'newest') filtered.sort((a, b) => b.id - a.id)

    // Pagination (mock)
    const perPage = 12
    totalProducts.value = filtered.length
    totalPages.value = Math.ceil(filtered.length / perPage)
    const start = (currentPage.value - 1) * perPage
    const end = start + perPage
    
    const list = filtered.slice(start, end)

    products.value = list.map((item: any) => ({
      ...item,
      badgeTokens: deriveBadgesFromProduct(item)
    }))
    
    // Mock filters (could be derived from products)
    availableColors.value = ['Kırmızı', 'Mavi', 'Siyah', 'Beyaz']
    availableSizes.value = ['S', 'M', 'L', 'XL', '36', '37', '38', '42', '43']
    availableBrands.value = ['Nike', 'Adidas', 'Puma', 'Under Armour']

  } catch (e) {
    console.error('Error fetching products:', e)
  } finally {
    loading.value = false
  }
}

async function loadCategories() {
  try {
    // Mock categories
    const mockCategories = [
      { id: 1, name: 'Ayakkabı', icon: '👟', products_count: 150 },
      { id: 2, name: 'Giyim', icon: '👕', products_count: 320 },
      { id: 3, name: 'Ekipman', icon: '🎒', products_count: 85 },
      { id: 4, name: 'Aksesuar', icon: '🧢', products_count: 210 },
    ]
    categories.value = normalizeCategories(mockCategories)
  } catch (error) {
    console.error('Kategoriler yüklenemedi:', error)
  }
}

function normalizeCategories(payload: any[]) {
  return (payload || []).map((cat: any) => ({
    ...cat,
    icon: cat.icon || '🏷️',
    count: cat.products_count || 0
  }))
}

function handleSearch(payload: any) {
  if (typeof payload === 'object' && payload.type === 'category') {
    filters.category = payload.value
  } else if (typeof payload === 'string') {
    filters.search = payload
  }
  
  currentPage.value = 1
  fetchProducts()
  document.getElementById('discovery-lab')?.scrollIntoView({ behavior: 'smooth' })
}

function updateFilters(newFilters: any) {
  if (newFilters.category !== undefined) filters.category = newFilters.category
  if (newFilters.minPrice !== undefined) filters.minPrice = newFilters.minPrice
  if (newFilters.maxPrice !== undefined) filters.maxPrice = newFilters.maxPrice
  if (newFilters.colors !== undefined) filters.colors = newFilters.colors
  if (newFilters.sizes !== undefined) filters.sizes = newFilters.sizes
  if (newFilters.brands !== undefined) filters.brands = newFilters.brands
  
  currentPage.value = 1
  fetchProducts()
}

function clearFilters() {
  filters.search = ''
  filters.category = null
  filters.minPrice = null
  filters.maxPrice = null
  filters.colors = []
  filters.sizes = []
  filters.brands = []
  filters.sort = 'newest'
  fetchProducts()
}

function changePage(page: number) {
  currentPage.value = page
  fetchProducts()
  document.getElementById('discovery-lab')?.scrollIntoView({ behavior: 'smooth' })
}

function filterByCategory(categoryId: number) {
  filters.category = categoryId
  document.getElementById('discovery-lab')?.scrollIntoView({ behavior: 'smooth' })
  fetchProducts()
}

function addToCart(product: any) {
  cartStore.addToCart(product)
  toast.success(`${product.name} sepete eklendi`, {
    timeout: 2000,
    hideProgressBar: true
  })
}

function toggleFavorite(productId: number) {
  const product = products.value.find(p => p.id === productId)
  if (product) {
    const wasFavorite = orderStore.isFavorite(productId)
    orderStore.toggleFavorite(product)
    if (wasFavorite) {
      toast.info('Favorilerden çıkarıldı', { timeout: 1500 })
    } else {
      toast.success('Favorilere eklendi', { timeout: 1500 })
    }
  }
}

function isFavorite(productId: number) {
  return orderStore.isFavorite(productId)
}

function addHighlightToCart(productId: number | string) {
    const product = products.value.find((item: any) => item.id === productId) || 
                    featuredProducts.value.find((item: any) => item.id === productId)
    if (product) {
        addToCart(product)
    }
}

function formatCurrency(value: number) {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value || 0)
}

function formatBadgeDebugTitle(badge: NormalizedBadge) {
  return `${badge.code} | ${badge.tone}${badge.label ? ` | ${badge.label}` : ''}`
}

const featuredProducts = computed(() => {
  return products.value.slice(0, 6).map((product: any) => ({
    id: product.id,
    name: product.name,
    description: product.description || 'Çok satan ürün',
    priceLabel: formatCurrency(product.price),
    seller: product.seller_name || 'Sportoonline Satıcısı',
    category: product.category?.name || 'Genel',
    image: product.image_url || product.image,
    badges: product.badgeTokens ?? deriveBadgesFromProduct(product)
  }))
})

const isDev = computed(() => import.meta.env.DEV)

onMounted(() => {
  loadCategories()
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
