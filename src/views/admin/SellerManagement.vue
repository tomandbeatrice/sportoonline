<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import SellerInsightAI from '@/components/admin/seller/SellerInsightAI.vue'

const sellers = ref([])
const loading = ref(false)
const selectedSeller = ref<any>(null)
const searchQuery = ref('')
const activeTab = ref('list') // list, applications

const filteredSellers = computed(() => {
  if (!searchQuery.value) return sellers.value
  const query = searchQuery.value.toLowerCase()
  return sellers.value.filter(s => 
    (s.storeName || '').toLowerCase().includes(query) || 
    (s.ownerName || '').toLowerCase().includes(query) ||
    (s.email || '').toLowerCase().includes(query)
  )
})

const selectSeller = (seller: any) => {
  selectedSeller.value = seller
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 0 }).format(value)
}

const getStatusBadge = (status: string) => {
  switch (status) {
    case 'active': return 'bg-emerald-100 text-emerald-700'
    case 'inactive': return 'bg-slate-100 text-slate-700'
    case 'suspended': return 'bg-red-100 text-red-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

onMounted(async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/admin/sellers')
    // Map API response to component structure if needed, or ensure API returns matching structure
    // Assuming API returns array of sellers
    sellers.value = response.data.data || response.data
    
    if (sellers.value.length > 0) {
      selectedSeller.value = sellers.value[0]
    }
  } catch (error) {
    console.error('Failed to fetch sellers:', error)
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
          🏪 Satıcı Yönetimi
          <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">AI Powered</span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">Mağaza performansı, risk analizi ve büyüme fırsatları</p>
      </div>
      <div class="flex gap-3">
        <button class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 transition flex items-center gap-2">
          <span>📥</span> Rapor Al
        </button>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 flex items-center gap-2">
          <span>➕</span> Satıcı Ekle
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
            @click="activeTab = 'list'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'list' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            📋 Mağaza Listesi
          </button>
          <button 
            @click="activeTab = 'applications'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'applications' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            📝 Başvurular (3)
          </button>
        </div>

        <!-- Tab Content -->
        <div class="flex-1 overflow-y-auto p-4">
          
          <!-- List View -->
          <div v-if="activeTab === 'list'" class="space-y-4">
            <!-- Search & Filters -->
            <div class="flex gap-2">
              <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                <input 
                  v-model="searchQuery"
                  type="text" 
                  placeholder="Mağaza veya satıcı ara..." 
                  class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                >
              </div>
              <button class="px-3 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">
                🌪️ Filtrele
              </button>
            </div>

            <!-- Seller List -->
            <div class="space-y-2">
              <div 
                v-for="seller in filteredSellers" 
                :key="seller.id"
                @click="selectSeller(seller)"
                class="p-3 rounded-xl border transition cursor-pointer group relative overflow-hidden"
                :class="selectedSeller?.id === seller.id ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200' : 'bg-white border-slate-100 hover:border-indigo-200 hover:shadow-md'"
              >
                <div class="flex items-center gap-4">
                  <img :src="seller.logo" class="w-12 h-12 rounded-lg shadow-sm" alt="">
                  <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                      <h3 class="font-bold text-slate-900 truncate">{{ seller.storeName }}</h3>
                      <div class="flex items-center gap-1">
                        <span class="text-yellow-500 text-xs">⭐</span>
                        <span class="text-xs font-bold text-slate-700">{{ seller.rating }}</span>
                      </div>
                    </div>
                    <p class="text-sm text-slate-500 truncate">{{ seller.ownerName }} • {{ seller.category }}</p>
                    <div class="flex items-center gap-3 mt-1 text-xs text-slate-500">
                      <span>📦 {{ seller.orderCount }} Sipariş</span>
                      <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                      <span class="font-medium text-slate-700">{{ formatCurrency(seller.totalSales) }} Ciro</span>
                    </div>
                  </div>
                  <div class="text-right">
                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide" :class="getStatusBadge(seller.status)">
                      {{ seller.status === 'active' ? 'Aktif' : (seller.status === 'suspended' ? 'Askıda' : 'Pasif') }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Applications View (Placeholder) -->
          <div v-else class="h-full flex items-center justify-center text-slate-400">
            <div class="text-center">
              <div class="text-4xl mb-2">📝</div>
              <p>Başvuru modülü yakında eklenecek</p>
            </div>
          </div>

        </div>
      </div>

      <!-- Right Panel: Detail & AI Insights -->
      <div class="w-96 bg-slate-50 border-l border-slate-200 flex flex-col overflow-hidden" v-if="selectedSeller">
        <div class="p-6 overflow-y-auto">
          
          <!-- Profile Header -->
          <div class="text-center mb-6">
            <div class="relative inline-block">
              <img :src="selectedSeller.logo" class="w-24 h-24 rounded-xl shadow-lg mx-auto mb-3 bg-white p-1" alt="">
              <span class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm border border-slate-100 text-lg" title="Rozet">
                🏆
              </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900">{{ selectedSeller.storeName }}</h2>
            <p class="text-slate-500 text-sm">{{ selectedSeller.email }}</p>
            <p class="text-slate-400 text-xs mt-1">Katılım: {{ selectedSeller.joinDate }}</p>
            
            <div class="flex justify-center gap-2 mt-4">
              <button class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition" title="Mağazayı Gör">
                🏪
              </button>
              <button class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition" title="Mesaj Gönder">
                💬
              </button>
              <button class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition" title="Ayarlar">
                ⚙️
              </button>
            </div>
          </div>

          <!-- AI Insights Component -->
          <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
              <span>🧠</span> AI Analizi
            </h3>
            <SellerInsightAI 
              :seller-id="selectedSeller.id"
              :store-name="selectedSeller.storeName"
              :rating="selectedSeller.rating"
              :total-sales="selectedSeller.totalSales"
            />
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-2 gap-3 mb-6">
            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
              <p class="text-xs text-slate-500 mb-1">Ürün Sayısı</p>
              <p class="font-bold text-slate-800">142</p>
            </div>
            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
              <p class="text-xs text-slate-500 mb-1">Takipçi</p>
              <p class="font-bold text-slate-800">1.2K</p>
            </div>
          </div>

          <!-- Admin Notes -->
          <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
            <h4 class="text-blue-800 font-bold text-sm mb-2">📌 Yönetici Notu</h4>
            <p class="text-blue-700 text-xs leading-relaxed">
              Satıcı kargo süreçlerinde iyileştirme yaptı. Müşteri memnuniyeti son 3 ayda %15 arttı. Premium satıcı adayı.
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
