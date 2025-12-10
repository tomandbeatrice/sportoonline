<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import BannerAIAnalyzer from '@/components/admin/banner/BannerAIAnalyzer.vue'

// --- Types ---
interface Banner {
  id: number
  title: string
  image_url: string
  position: string
  status: 'active' | 'scheduled' | 'expired' | 'inactive'
  start_date: string | null
  end_date: string | null
  views: number
  clicks: number
  ctr: number
  order: number
  link: string
}

// --- State ---
const loading = ref(false)
const banners = ref<Banner[]>([])
const selectedBanner = ref<Banner | null>(null)
const showCreateModal = ref(false)

const filters = ref({
  search: '',
  position: '',
  status: ''
})

const stats = ref({
  total: 0,
  active: 0,
  scheduled: 0,
  totalViews: 0
})

// --- Computed ---
const filteredBanners = computed(() => {
  return banners.value.filter(b => {
    const matchSearch = b.title.toLowerCase().includes(filters.value.search.toLowerCase())
    const matchPos = filters.value.position ? b.position === filters.value.position : true
    const matchStatus = filters.value.status ? b.status === filters.value.status : true
    return matchSearch && matchPos && matchStatus
  })
})

// --- Methods ---
const loadBanners = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/admin/banners')
    banners.value = response.data.data || response.data
    
    // Fetch stats if available
    try {
        const statsResponse = await axios.get('/api/admin/banners/stats')
        stats.value = statsResponse.data
    } catch (e) {
        // Fallback stats calculation
        stats.value.total = banners.value.length
        stats.value.active = banners.value.filter(b => b.status === 'active').length
        stats.value.scheduled = banners.value.filter(b => b.status === 'scheduled').length
        stats.value.totalViews = banners.value.reduce((sum, b) => sum + (b.views || 0), 0)
    }

    if (banners.value.length > 0 && !selectedBanner.value) {
      selectedBanner.value = banners.value[0]
    }
  } catch (error) {
    console.error('Failed to fetch banners:', error)
  } finally {
    loading.value = false
  }
}

const selectBanner = (banner: Banner) => {
  selectedBanner.value = banner
}

const getStatusBadgeClass = (status: string) => {
  switch (status) {
    case 'active': return 'bg-emerald-100 text-emerald-700 border-emerald-200'
    case 'scheduled': return 'bg-blue-100 text-blue-700 border-blue-200'
    case 'expired': return 'bg-slate-100 text-slate-600 border-slate-200'
    case 'inactive': return 'bg-red-100 text-red-700 border-red-200'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const getStatusLabel = (status: string) => {
  const map: Record<string, string> = {
    active: 'Yayında',
    scheduled: 'Zamanlandı',
    expired: 'Süresi Doldu',
    inactive: 'Pasif'
  }
  return map[status] || status
}

const getPositionLabel = (pos: string) => {
  const map: Record<string, string> = {
    home_top: 'Ana Sayfa Üst',
    home_middle: 'Ana Sayfa Orta',
    sidebar: 'Kenar Çubuğu',
    category: 'Kategori',
    product: 'Ürün Detay'
  }
  return map[pos] || pos
}

onMounted(() => {
  loadBanners()
})
</script>

<template>
  <div class="h-[calc(100vh-4rem)] flex flex-col bg-slate-50 overflow-hidden">
    <!-- Top Stats Bar -->
    <div class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
          🎨 Banner Yönetimi
          <span class="text-xs font-normal bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full border border-indigo-200">AI Destekli</span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">Görsel kampanyalarınızı optimize edin ve yönetin</p>
      </div>
      
      <div class="flex gap-4">
        <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-lg border border-slate-200">
          <div class="text-center">
            <div class="text-xs text-slate-500 font-medium">Toplam</div>
            <div class="text-lg font-bold text-slate-700">{{ stats.total }}</div>
          </div>
          <div class="w-px h-8 bg-slate-200"></div>
          <div class="text-center">
            <div class="text-xs text-slate-500 font-medium">Aktif</div>
            <div class="text-lg font-bold text-emerald-600">{{ stats.active }}</div>
          </div>
          <div class="w-px h-8 bg-slate-200"></div>
          <div class="text-center">
            <div class="text-xs text-slate-500 font-medium">Görüntüleme</div>
            <div class="text-lg font-bold text-blue-600">{{ (stats.totalViews / 1000).toFixed(1) }}K</div>
          </div>
        </div>
        
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition shadow-sm shadow-indigo-200">
          <span>➕</span> Yeni Banner
        </button>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex flex-1 overflow-hidden">
      
      <!-- Left Panel: Banner List -->
      <div class="w-1/3 min-w-[400px] bg-white border-r border-slate-200 flex flex-col">
        <!-- Filters -->
        <div class="p-4 border-b border-slate-100 space-y-3 bg-slate-50/50">
          <div class="relative">
            <span class="absolute left-3 top-2.5 text-slate-400">🔍</span>
            <input 
              v-model="filters.search"
              type="text" 
              placeholder="Banner ara..." 
              class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
            >
          </div>
          <div class="flex gap-2">
            <select v-model="filters.position" class="flex-1 bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-600 focus:outline-none focus:border-indigo-500">
              <option value="">Tüm Pozisyonlar</option>
              <option value="home_top">Ana Sayfa Üst</option>
              <option value="home_middle">Ana Sayfa Orta</option>
              <option value="sidebar">Kenar Çubuğu</option>
            </select>
            <select v-model="filters.status" class="flex-1 bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-600 focus:outline-none focus:border-indigo-500">
              <option value="">Tüm Durumlar</option>
              <option value="active">Yayında</option>
              <option value="scheduled">Zamanlanmış</option>
              <option value="inactive">Pasif</option>
            </select>
          </div>
        </div>

        <!-- List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-2">
          <div 
            v-for="banner in filteredBanners" 
            :key="banner.id"
            @click="selectBanner(banner)"
            class="group p-3 rounded-xl border cursor-pointer transition-all duration-200 hover:shadow-md"
            :class="selectedBanner?.id === banner.id ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200' : 'bg-white border-slate-100 hover:border-indigo-100'"
          >
            <div class="flex gap-3">
              <!-- Thumbnail -->
              <div class="w-24 h-16 rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-slate-200 relative">
                <img :src="banner.image_url" class="w-full h-full object-cover" alt="">
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition"></div>
              </div>
              
              <!-- Info -->
              <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start mb-1">
                  <h3 class="font-bold text-slate-800 text-sm truncate pr-2" :class="selectedBanner?.id === banner.id ? 'text-indigo-700' : ''">
                    {{ banner.title }}
                  </h3>
                  <span 
                    class="text-[10px] px-1.5 py-0.5 rounded-full border font-medium whitespace-nowrap"
                    :class="getStatusBadgeClass(banner.status)"
                  >
                    {{ getStatusLabel(banner.status) }}
                  </span>
                </div>
                
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1.5">
                  <span class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-600">{{ getPositionLabel(banner.position) }}</span>
                  <span v-if="banner.order" class="text-slate-400">#{{ banner.order }}</span>
                </div>

                <div class="flex items-center justify-between text-xs">
                  <div class="flex gap-3 text-slate-500">
                    <span class="flex items-center gap-1">
                      👁️ {{ (banner.views / 1000).toFixed(1) }}K
                    </span>
                    <span class="flex items-center gap-1">
                      🖱️ {{ banner.clicks }}
                    </span>
                  </div>
                  <div class="font-bold" :class="banner.ctr > 2 ? 'text-emerald-600' : 'text-slate-600'">
                    CTR: %{{ banner.ctr }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="flex-1 bg-slate-50 overflow-y-auto p-6" v-if="selectedBanner">
        <div class="max-w-5xl mx-auto space-y-6">
          
          <!-- Banner Preview & Basic Info -->
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="relative aspect-[3/1] bg-slate-100 border-b border-slate-100 group">
              <img :src="selectedBanner.image_url" class="w-full h-full object-cover" alt="">
              <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition">
                <button class="bg-white/90 hover:bg-white text-slate-700 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm backdrop-blur-sm">
                  ✏️ Düzenle
                </button>
                <button class="bg-white/90 hover:bg-white text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm backdrop-blur-sm">
                  🗑️ Sil
                </button>
              </div>
            </div>
            
            <div class="p-5">
              <div class="flex justify-between items-start mb-4">
                <div>
                  <h2 class="text-xl font-bold text-slate-800">{{ selectedBanner.title }}</h2>
                  <a :href="selectedBanner.link" target="_blank" class="text-sm text-indigo-600 hover:underline flex items-center gap-1 mt-1">
                    🔗 {{ selectedBanner.link }}
                  </a>
                </div>
                <div class="text-right">
                  <div class="text-sm text-slate-500">Yayın Tarihleri</div>
                  <div class="font-medium text-slate-700">
                    {{ selectedBanner.start_date || 'Başlangıç Yok' }} — {{ selectedBanner.end_date || 'Süresiz' }}
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-4 gap-4 pt-4 border-t border-slate-100">
                <div>
                  <div class="text-xs text-slate-500 mb-1">Görüntülenme</div>
                  <div class="text-lg font-bold text-slate-800">{{ selectedBanner.views.toLocaleString() }}</div>
                </div>
                <div>
                  <div class="text-xs text-slate-500 mb-1">Tıklama</div>
                  <div class="text-lg font-bold text-slate-800">{{ selectedBanner.clicks.toLocaleString() }}</div>
                </div>
                <div>
                  <div class="text-xs text-slate-500 mb-1">Tıklama Oranı (CTR)</div>
                  <div class="text-lg font-bold" :class="selectedBanner.ctr > 2 ? 'text-emerald-600' : 'text-orange-600'">
                    %{{ selectedBanner.ctr }}
                  </div>
                </div>
                <div>
                  <div class="text-xs text-slate-500 mb-1">Sıralama</div>
                  <div class="text-lg font-bold text-slate-800">{{ selectedBanner.order }}. Sıra</div>
                </div>
              </div>
            </div>
          </div>

          <!-- AI Analysis Section -->
          <div class="grid grid-cols-3 gap-6">
            <!-- Left: AI Component -->
            <div class="col-span-1">
              <BannerAIAnalyzer 
                :banner-id="selectedBanner.id"
                :image-url="selectedBanner.image_url"
                :title="selectedBanner.title"
                :ctr="selectedBanner.ctr"
              />
            </div>

            <!-- Right: Detailed Charts/Metrics (Placeholder for now) -->
            <div class="col-span-2 space-y-6">
              <!-- Performance Chart Placeholder -->
              <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                  📈 Performans Grafiği
                  <span class="text-xs font-normal text-slate-400">(Son 30 Gün)</span>
                </h3>
                <div class="h-48 bg-slate-50 rounded-lg border border-slate-100 flex items-center justify-center text-slate-400 text-sm">
                  [Grafik Alanı: Görüntüleme vs Tıklama]
                </div>
              </div>

              <!-- Device Breakdown -->
              <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-4">Cihaz Dağılımı</h3>
                <div class="space-y-3">
                  <div>
                    <div class="flex justify-between text-xs mb-1">
                      <span class="font-medium text-slate-600">Mobil</span>
                      <span class="font-bold text-slate-800">%65</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                      <div class="h-full bg-indigo-500 w-[65%]"></div>
                    </div>
                  </div>
                  <div>
                    <div class="flex justify-between text-xs mb-1">
                      <span class="font-medium text-slate-600">Masaüstü</span>
                      <span class="font-bold text-slate-800">%30</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                      <div class="h-full bg-blue-400 w-[30%]"></div>
                    </div>
                  </div>
                  <div>
                    <div class="flex justify-between text-xs mb-1">
                      <span class="font-medium text-slate-600">Tablet</span>
                      <span class="font-bold text-slate-800">%5</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                      <div class="h-full bg-teal-400 w-[5%]"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="flex-1 flex items-center justify-center bg-slate-50 text-slate-400">
        <div class="text-center">
          <div class="text-4xl mb-2">👈</div>
          <p>Detayları görmek için bir banner seçin</p>
        </div>
      </div>

    </div>
  </div>
</template>
