<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import ReturnAIAnalysis from '@/components/admin/return/ReturnAIAnalysis.vue'
import axios from 'axios'

// State
const returns = ref<any[]>([])
const loading = ref(false)

// Fetch returns from API
const fetchReturns = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/returns')
    returns.value = data.returns || data.data || data || []
  } catch (error) {
    console.error('Failed to fetch returns:', error)
    // Fallback demo data
    returns.value = [
      { id: 4501, orderId: 'ORD-2024-8892', customer: 'Ahmet Yılmaz', product: 'Nike Air Zoom', amount: 3450.00, date: '2024-03-21', status: 'pending', reason: 'Beden Uyumsuzluğu', reasonCategory: 'size_fit', images: [], customerHistory: { totalReturns: 2, returnRate: 5 } }
    ]
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchReturns()
})

const selectedReturn = ref<any>(null)
const searchQuery = ref('')
const activeTab = ref('pending') // all, pending, approved, rejected

const filteredReturns = computed(() => {
  let result = returns.value

  // Tab Filter
  if (activeTab.value !== 'all') {
    result = result.filter(r => r.status === activeTab.value)
  }

  // Search Filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(r => 
      r.customer.toLowerCase().includes(query) || 
      r.orderId.toLowerCase().includes(query) ||
      r.id.toString().includes(query)
    )
  }

  return result
})

const selectReturn = (item: any) => {
  selectedReturn.value = item
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)
}

const getStatusBadge = (status: string) => {
  switch (status) {
    case 'approved': return 'bg-emerald-100 text-emerald-700'
    case 'pending': return 'bg-orange-100 text-orange-700'
    case 'rejected': return 'bg-red-100 text-red-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const getReasonIcon = (category: string) => {
  switch (category) {
    case 'defective': return '⚠️'
    case 'size_fit': return '📏'
    case 'changed_mind': return '🤔'
    case 'damaged': return '🔨'
    default: return '📦'
  }
}

onMounted(() => {
  if (returns.value.length > 0) {
    selectedReturn.value = returns.value[0]
  }
})
</script>

<template>
  <div class="h-[calc(100vh-4rem)] flex flex-col bg-slate-50">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
          ↩️ İade Yönetimi
          <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">AI Policy Check</span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">İade talepleri, görsel analiz ve sahtecilik kontrolü</p>
      </div>
      <div class="flex gap-3">
        <button class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 transition flex items-center gap-2">
          <span>📊</span> Raporlar
        </button>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 flex items-center gap-2">
          <span>⚙️</span> İade Politikaları
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
            @click="activeTab = 'pending'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'pending' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            ⏳ Bekleyenler
          </button>
          <button 
            @click="activeTab = 'approved'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'approved' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            ✅ Onaylananlar
          </button>
          <button 
            @click="activeTab = 'all'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'all' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            Tümü
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
                placeholder="Talep no, müşteri veya sipariş no..." 
                class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
              >
            </div>
            <button class="px-3 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">
              🌪️ Filtrele
            </button>
          </div>

          <!-- Return List -->
          <div class="space-y-2">
            <div 
              v-for="item in filteredReturns" 
              :key="item.id"
              @click="selectReturn(item)"
              class="p-3 rounded-xl border transition cursor-pointer group relative overflow-hidden"
              :class="selectedReturn?.id === item.id ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200' : 'bg-white border-slate-100 hover:border-indigo-200 hover:shadow-md'"
            >
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center text-2xl">
                  {{ getReasonIcon(item.reasonCategory) }}
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex justify-between items-start">
                    <h3 class="font-bold text-slate-900 truncate">#{{ item.id }} - {{ item.customer }}</h3>
                    <span class="font-bold text-slate-900">{{ formatCurrency(item.amount) }}</span>
                  </div>
                  <p class="text-sm text-slate-500 truncate">{{ item.product }}</p>
                  <div class="flex items-center gap-3 mt-1 text-xs text-slate-500">
                    <span>📅 {{ item.date }}</span>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <span>{{ item.reason }}</span>
                  </div>
                </div>
                <div class="text-right">
                  <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide" :class="getStatusBadge(item.status)">
                    {{ item.status === 'pending' ? 'Bekliyor' : (item.status === 'approved' ? 'Onaylandı' : 'Reddedildi') }}
                  </span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Right Panel: Detail & AI Insights -->
      <div class="w-96 bg-slate-50 border-l border-slate-200 flex flex-col overflow-hidden" v-if="selectedReturn">
        <div class="p-6 overflow-y-auto">
          
          <!-- Return Header -->
          <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-bold text-slate-500 uppercase">Talep Detayı</span>
              <span class="text-xs font-mono text-slate-400">{{ selectedReturn.orderId }}</span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 leading-tight mb-1">{{ selectedReturn.product }}</h2>
            <p class="text-slate-500 text-sm">{{ selectedReturn.reason }}</p>
          </div>

          <!-- AI Insights Component -->
          <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
              <span>🧠</span> AI Karar Destek
            </h3>
            <ReturnAIAnalysis 
              :return-id="selectedReturn.id"
              :reason="selectedReturn.reason"
              :customer-history="selectedReturn.customerHistory"
              :images="selectedReturn.images"
            />
          </div>

          <!-- Return Images -->
          <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6" v-if="selectedReturn.images.length > 0">
            <div class="p-3 border-b border-slate-100 bg-slate-50">
              <h4 class="font-bold text-slate-800 text-sm">📷 Müşteri Görselleri</h4>
            </div>
            <div class="p-3 grid grid-cols-2 gap-2">
              <div v-for="(img, index) in selectedReturn.images" :key="index" class="aspect-square bg-slate-100 rounded-lg flex items-center justify-center text-slate-400 text-xs">
                Görsel {{ index + 1 }}
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="grid grid-cols-2 gap-3" v-if="selectedReturn.status === 'pending'">
            <button class="py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-lg shadow-emerald-200 transition">
              Onayla
            </button>
            <button class="py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-lg shadow-red-200 transition">
              Reddet
            </button>
          </div>
          
          <div class="mt-3" v-if="selectedReturn.status === 'pending'">
             <button class="w-full py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold transition text-sm">
              💬 Müşteriyle İletişime Geç
            </button>
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
