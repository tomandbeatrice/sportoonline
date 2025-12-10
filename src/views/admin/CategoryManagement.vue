<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import CategoryAIInsight from '@/components/admin/category/CategoryAIInsight.vue'

const categories = ref([])
const loading = ref(false)
const selectedCategory = ref<any>(null)
const searchQuery = ref('')
const activeTab = ref('all') // all, active, inactive

const filteredCategories = computed(() => {
  let result = categories.value

  // Tab Filter
  if (activeTab.value !== 'all') {
    result = result.filter(c => c.status === activeTab.value)
  }

  // Search Filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(c => 
      (c.name || '').toLowerCase().includes(query) || 
      (c.parent || '').toLowerCase().includes(query)
    )
  }

  return result
})

const selectCategory = (category: any) => {
  selectedCategory.value = category
}

const getStatusBadge = (status: string) => {
  switch (status) {
    case 'active': return 'bg-emerald-100 text-emerald-700'
    case 'inactive': return 'bg-slate-100 text-slate-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

onMounted(async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/admin/categories')
    categories.value = response.data.data || response.data
    
    if (categories.value.length > 0) {
      selectedCategory.value = categories.value[0]
    }
  } catch (error) {
    console.error('Failed to fetch categories:', error)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="h-[calc(100vh-4rem)] flex flex-col bg-slate-50">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
          📁 Kategori Yönetimi
          <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">AI SEO</span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">Kategori ağacı, SEO optimizasyonu ve trend analizi</p>
      </div>
      <div class="flex gap-3">
        <button class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 transition flex items-center gap-2">
          <span>🌳</span> Ağaç Görünümü
        </button>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 flex items-center gap-2">
          <span>➕</span> Yeni Kategori
        </button>
      </div>
    </header>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
      <!-- Left Panel: List -->
      <div class="flex-1 flex flex-col min-w-0 border-r border-slate-200 bg-white">
        
        <!-- Tabs -->
        <div class="flex border-b border-slate-200">
          <button 
            @click="activeTab = 'all'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'all' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            Tümü
          </button>
          <button 
            @click="activeTab = 'active'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'active' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            🟢 Aktif
          </button>
          <button 
            @click="activeTab = 'inactive'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'inactive' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            ⚪ Pasif
          </button>
        </div>

        <!-- Tab Content -->
        <div class="flex-1 overflow-y-auto p-4">
          
          <!-- Search & Filters -->
          <div class="flex gap-2 mb-4">
            <div class="relative flex-1">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Kategori adı ara..." 
                class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
              >
            </div>
            <button class="px-3 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">
              🌪️ Filtrele
            </button>
          </div>

          <!-- Category List -->
          <div class="space-y-2">
            <div 
              v-for="category in filteredCategories" 
              :key="category.id"
              @click="selectCategory(category)"
              class="p-3 rounded-xl border transition cursor-pointer group relative overflow-hidden"
              :class="selectedCategory?.id === category.id ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200' : 'bg-white border-slate-100 hover:border-indigo-200 hover:shadow-md'"
            >
              <div class="flex items-center gap-4">
                <img :src="category.image" class="w-12 h-12 rounded-lg object-cover shadow-sm" alt="">
                <div class="flex-1 min-w-0">
                  <div class="flex justify-between items-start">
                    <h3 class="font-bold text-slate-900 truncate">{{ category.name }}</h3>
                    <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                      {{ category.parent }}
                    </span>
                  </div>
                  <p class="text-sm text-slate-500 truncate">{{ category.description }}</p>
                  <div class="flex items-center gap-3 mt-1 text-xs text-slate-500">
                    <span>📦 {{ category.productCount }} Ürün</span>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <span>🔗 /{{ category.slug }}</span>
                  </div>
                </div>
                <div class="text-right">
                  <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide" :class="getStatusBadge(category.status)">
                    {{ category.status === 'active' ? 'Aktif' : 'Pasif' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Right Panel: Detail & AI Insights -->
      <div class="w-96 bg-slate-50 border-l border-slate-200 flex flex-col overflow-hidden" v-if="selectedCategory">
        <div class="p-6 overflow-y-auto">
          
          <!-- Category Header -->
          <div class="mb-6 relative">
            <div class="h-32 rounded-xl overflow-hidden mb-4 relative">
              <img :src="selectedCategory.image" class="w-full h-full object-cover" alt="">
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
              <h2 class="absolute bottom-3 left-3 text-2xl font-bold text-white shadow-sm">{{ selectedCategory.name }}</h2>
            </div>
            
            <div class="flex gap-2 mb-4">
              <button class="flex-1 py-2 bg-white border border-slate-200 rounded-lg text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                ✏️ Düzenle
              </button>
              <button class="flex-1 py-2 bg-white border border-slate-200 rounded-lg text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                👁️ Sitede Gör
              </button>
            </div>
          </div>

          <!-- AI Insights Component -->
          <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
              <span>🧠</span> AI Analizi
            </h3>
            <CategoryAIInsight 
              :category-name="selectedCategory.name"
              :product-count="selectedCategory.productCount"
              :search-volume="selectedCategory.searchVolume"
            />
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-2 gap-3 mb-6">
            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
              <p class="text-xs text-slate-500 mb-1">Üst Kategori</p>
              <p class="font-bold text-slate-800">{{ selectedCategory.parent }}</p>
            </div>
            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
              <p class="text-xs text-slate-500 mb-1">URL Yapısı</p>
              <p class="font-bold text-slate-800 truncate text-xs" :title="selectedCategory.slug">/{{ selectedCategory.slug }}</p>
            </div>
          </div>

          <!-- Description -->
          <div class="bg-white rounded-xl border border-slate-200 p-4">
            <h4 class="text-slate-800 font-bold text-sm mb-2">📝 Açıklama</h4>
            <p class="text-slate-600 text-xs leading-relaxed">
              {{ selectedCategory.description }}
            </p>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}
::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
