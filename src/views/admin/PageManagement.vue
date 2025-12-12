<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import PageContentAI from '@/components/admin/page/PageContentAI.vue'

// --- Types ---
interface Page {
  id: number
  title: string
  slug: string
  status: 'published' | 'draft' | 'archived'
  type: 'system' | 'custom'
  updated_at: string
  author: string
  views: number
  content_preview: string
}

// --- State ---
const loading = ref(false)
const pages = ref<Page[]>([])
const selectedPage = ref<Page | null>(null)

const filters = ref({
  search: '',
  status: '',
  type: ''
})

const stats = ref({
  total: 0,
  published: 0,
  draft: 0,
  totalViews: 0
})

// --- Computed ---
const filteredPages = computed(() => {
  return pages.value.filter(p => {
    const matchSearch = p.title.toLowerCase().includes(filters.value.search.toLowerCase()) || 
                        p.slug.toLowerCase().includes(filters.value.search.toLowerCase())
    const matchStatus = filters.value.status ? p.status === filters.value.status : true
    const matchType = filters.value.type ? p.type === filters.value.type : true
    return matchSearch && matchStatus && matchType
  })
})

// --- Methods ---
const loadPages = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/admin/pages')
    
    // Safely handle response data structure
    const responseData = response.data
    if (responseData && Array.isArray(responseData.data)) {
      pages.value = responseData.data
    } else if (Array.isArray(responseData)) {
      pages.value = responseData
    } else {
      console.error('Invalid pages data format:', responseData)
      pages.value = []
    }
    
    // Fetch stats
    try {
        const statsResponse = await axios.get('/api/admin/pages/stats')
        stats.value = statsResponse.data
    } catch (e) {
        console.error('Stats yüklenemedi:', e)
        // Fallback stats
        stats.value.total = pages.value.length
        stats.value.published = pages.value.filter(p => p.status === 'published').length
        stats.value.draft = pages.value.filter(p => p.status === 'draft').length
        stats.value.totalViews = pages.value.reduce((sum, p) => sum + (p.views || 0), 0)
    }

    if (pages.value.length > 0 && !selectedPage.value) {
      selectedPage.value = pages.value[0]
    }
  } catch (error) {
    console.error('Failed to fetch pages:', error)
  } finally {
    loading.value = false
  }
}

const selectPage = (page: Page) => {
  selectedPage.value = page
}

const getStatusBadgeClass = (status: string) => {
  switch (status) {
    case 'published': return 'bg-emerald-100 text-emerald-700 border-emerald-200'
    case 'draft': return 'bg-amber-100 text-amber-700 border-amber-200'
    case 'archived': return 'bg-slate-100 text-slate-600 border-slate-200'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const getStatusLabel = (status: string) => {
  const map: Record<string, string> = {
    published: 'Yayında',
    draft: 'Taslak',
    archived: 'Arşiv'
  }
  return map[status] || status
}

onMounted(() => {
  loadPages()
})
</script>

<template>
  <div class="h-[calc(100vh-4rem)] flex flex-col bg-slate-50 overflow-hidden">
    <!-- Top Stats Bar -->
    <div class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
          📄 Sayfa Yönetimi
          <span class="text-xs font-normal bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full border border-indigo-200">CMS</span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">İçerik sayfalarını yönetin ve optimize edin</p>
      </div>
      
      <div class="flex gap-4">
        <div class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-lg border border-slate-200">
          <div class="text-center">
            <div class="text-xs text-slate-500 font-medium">Toplam</div>
            <div class="text-lg font-bold text-slate-700">{{ stats.total }}</div>
          </div>
          <div class="w-px h-8 bg-slate-200"></div>
          <div class="text-center">
            <div class="text-xs text-slate-500 font-medium">Yayında</div>
            <div class="text-lg font-bold text-emerald-600">{{ stats.published }}</div>
          </div>
          <div class="w-px h-8 bg-slate-200"></div>
          <div class="text-center">
            <div class="text-xs text-slate-500 font-medium">Okunma</div>
            <div class="text-lg font-bold text-blue-600">{{ (stats.totalViews / 1000).toFixed(1) }}K</div>
          </div>
        </div>
        
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition shadow-sm shadow-indigo-200">
          <span>➕</span> Yeni Sayfa
        </button>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex flex-1 overflow-hidden">
      
      <!-- Left Panel: Page List -->
      <div class="w-1/3 min-w-[400px] bg-white border-r border-slate-200 flex flex-col">
        <!-- Filters -->
        <div class="p-4 border-b border-slate-100 space-y-3 bg-slate-50/50">
          <div class="relative">
            <span class="absolute left-3 top-2.5 text-slate-400">🔍</span>
            <input 
              v-model="filters.search"
              type="text" 
              placeholder="Sayfa başlığı veya slug..." 
              class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
            >
          </div>
          <div class="flex gap-2">
            <select v-model="filters.status" class="flex-1 bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-600 focus:outline-none focus:border-indigo-500">
              <option value="">Tüm Durumlar</option>
              <option value="published">Yayında</option>
              <option value="draft">Taslak</option>
            </select>
            <select v-model="filters.type" class="flex-1 bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-600 focus:outline-none focus:border-indigo-500">
              <option value="">Tüm Tipler</option>
              <option value="system">Sistem</option>
              <option value="custom">Özel</option>
            </select>
          </div>
        </div>

        <!-- List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-2">
          <div 
            v-for="page in filteredPages" 
            :key="page.id"
            @click="selectPage(page)"
            class="group p-3 rounded-xl border cursor-pointer transition-all duration-200 hover:shadow-md"
            :class="selectedPage?.id === page.id ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200' : 'bg-white border-slate-100 hover:border-indigo-100'"
          >
            <div class="flex justify-between items-start mb-1">
              <h3 class="font-bold text-slate-800 text-sm truncate pr-2" :class="selectedPage?.id === page.id ? 'text-indigo-700' : ''">
                {{ page.title }}
              </h3>
              <span 
                class="text-[10px] px-1.5 py-0.5 rounded-full border font-medium whitespace-nowrap"
                :class="getStatusBadgeClass(page.status)"
              >
                {{ getStatusLabel(page.status) }}
              </span>
            </div>
            
            <div class="text-xs text-slate-500 font-mono mb-2 truncate">/{{ page.slug }}</div>

            <div class="flex items-center justify-between text-xs text-slate-400">
              <div class="flex items-center gap-2">
                <span v-if="page.type === 'system'" class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-500 font-medium">Sistem</span>
                <span v-else class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded font-medium">Özel</span>
                <span>{{ page.updated_at }}</span>
              </div>
              <div class="flex items-center gap-1">
                👁️ {{ (page.views / 1000).toFixed(1) }}K
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="flex-1 bg-slate-50 overflow-y-auto p-6" v-if="selectedPage">
        <div class="max-w-5xl mx-auto space-y-6">
          
          <!-- Page Header & Actions -->
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex justify-between items-start">
            <div>
              <div class="flex items-center gap-3 mb-1">
                <h2 class="text-xl font-bold text-slate-800">{{ selectedPage.title }}</h2>
                <a :href="'/' + selectedPage.slug" target="_blank" class="text-slate-400 hover:text-indigo-600 transition">
                  🔗
                </a>
              </div>
              <p class="text-sm text-slate-500">
                Son güncelleme: <span class="font-medium text-slate-700">{{ selectedPage.updated_at }}</span> 
                • Yazar: <span class="font-medium text-slate-700">{{ selectedPage.author }}</span>
              </p>
            </div>
            <div class="flex gap-2">
              <button class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-bold transition">
                Önizle
              </button>
              <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm shadow-indigo-200">
                ✏️ İçeriği Düzenle
              </button>
            </div>
          </div>

          <!-- Content Preview (Mock Editor) -->
          <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-slate-50 border-b border-slate-200 px-4 py-2 flex gap-2">
              <div class="w-3 h-3 rounded-full bg-red-400"></div>
              <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
              <div class="w-3 h-3 rounded-full bg-green-400"></div>
            </div>
            <div class="p-6 prose prose-sm max-w-none text-slate-600">
              <h3 class="text-slate-800">{{ selectedPage.title }}</h3>
              <p>{{ selectedPage.content_preview }}</p>
              <p class="opacity-50">[...Devamı...]</p>
            </div>
          </div>

          <!-- AI Analysis Section -->
          <div class="grid grid-cols-3 gap-6">
            <!-- Left: AI Component -->
            <div class="col-span-1">
              <PageContentAI 
                :page-id="selectedPage.id"
                :title="selectedPage.title"
                :slug="selectedPage.slug"
                :content-preview="selectedPage.content_preview"
              />
            </div>

            <!-- Right: Traffic Stats -->
            <div class="col-span-2 space-y-6">
              <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                  📈 Trafik Analizi
                  <span class="text-xs font-normal text-slate-400">(Son 30 Gün)</span>
                </h3>
                <div class="h-48 bg-slate-50 rounded-lg border border-slate-100 flex items-center justify-center text-slate-400 text-sm">
                  [Grafik Alanı: Günlük Ziyaretçi]
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4">
                  <div class="text-center p-3 bg-slate-50 rounded-lg">
                    <div class="text-xs text-slate-500">Ort. Süre</div>
                    <div class="font-bold text-slate-700">2dk 15sn</div>
                  </div>
                  <div class="text-center p-3 bg-slate-50 rounded-lg">
                    <div class="text-xs text-slate-500">Hemen Çıkma</div>
                    <div class="font-bold text-slate-700">%42</div>
                  </div>
                  <div class="text-center p-3 bg-slate-50 rounded-lg">
                    <div class="text-xs text-slate-500">Dönüşüm</div>
                    <div class="font-bold text-slate-700">%1.2</div>
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
          <p>Detayları görmek için bir sayfa seçin</p>
        </div>
      </div>

    </div>
  </div>
</template>
