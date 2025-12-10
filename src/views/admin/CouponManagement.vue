<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import CouponAIPlanner from '@/components/admin/coupon/CouponAIPlanner.vue'
import axios from 'axios'

// State
const coupons = ref<any[]>([])
const loading = ref(false)

// Fetch coupons from API
const fetchCoupons = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/coupons')
    coupons.value = data.coupons || data || []
  } catch (error) {
    console.error('Failed to fetch coupons:', error)
    // Fallback demo data
    coupons.value = [
      { id: 1, code: 'YAZ2024', description: 'Yaz sezonu indirimi', discount: 20, type: 'percentage', status: 'active', usageCount: 1450, usageLimit: 5000, minBasket: 500, startDate: '2024-06-01', endDate: '2024-08-31', targetSegment: 'Tümü' }
    ]
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchCoupons()
})

const selectedCoupon = ref<any>(null)
const searchQuery = ref('')
const activeTab = ref('active') // active, expired, disabled

const filteredCoupons = computed(() => {
  let result = coupons.value

  // Tab Filter
  if (activeTab.value !== 'all') {
    result = result.filter(c => c.status === activeTab.value)
  }

  // Search Filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(c => 
      c.code.toLowerCase().includes(query) || 
      c.description.toLowerCase().includes(query)
    )
  }

  return result
})

const selectCoupon = (coupon: any) => {
  selectedCoupon.value = coupon
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 0 }).format(value)
}

const getStatusBadge = (status: string) => {
  switch (status) {
    case 'active': return 'bg-emerald-100 text-emerald-700'
    case 'expired': return 'bg-slate-100 text-slate-700'
    case 'disabled': return 'bg-red-100 text-red-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const getTypeIcon = (type: string) => {
  switch (type) {
    case 'percentage': return 'percent' // Using text for now, or icon component
    case 'fixed': return 'tag'
    case 'shipping': return 'truck'
    default: return 'tag'
  }
}

const getTypeLabel = (type: string) => {
  switch (type) {
    case 'percentage': return 'Yüzde İndirim'
    case 'fixed': return 'Sabit Tutar'
    case 'shipping': return 'Ücretsiz Kargo'
    default: return 'Kupon'
  }
}

onMounted(() => {
  if (coupons.value.length > 0) {
    selectedCoupon.value = coupons.value[0]
  }
})
</script>

<template>
  <div class="h-[calc(100vh-4rem)] flex flex-col bg-slate-50">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
          🏷️ Kupon Yönetimi
          <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">AI Planner</span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">Akıllı indirim planlama ve etki analizi</p>
      </div>
      <div class="flex gap-3">
        <button class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 transition flex items-center gap-2">
          <span>📊</span> Raporlar
        </button>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 flex items-center gap-2">
          <span>➕</span> Yeni Kupon
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
            @click="activeTab = 'active'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'active' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            🟢 Aktif
          </button>
          <button 
            @click="activeTab = 'expired'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'expired' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            🏁 Süresi Dolan
          </button>
          <button 
            @click="activeTab = 'disabled'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'disabled' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            🔴 Pasif
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
                placeholder="Kupon kodu veya açıklama ara..." 
                class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
              >
            </div>
            <button class="px-3 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">
              🌪️ Filtrele
            </button>
          </div>

          <!-- Coupon List -->
          <div class="space-y-2">
            <div 
              v-for="coupon in filteredCoupons" 
              :key="coupon.id"
              @click="selectCoupon(coupon)"
              class="p-3 rounded-xl border transition cursor-pointer group relative overflow-hidden"
              :class="selectedCoupon?.id === coupon.id ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200' : 'bg-white border-slate-100 hover:border-indigo-200 hover:shadow-md'"
            >
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center text-xl font-bold text-slate-600 border border-slate-200 border-dashed">
                  <span v-if="coupon.type === 'percentage'">%</span>
                  <span v-else-if="coupon.type === 'fixed'">₺</span>
                  <span v-else>🚚</span>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex justify-between items-start">
                    <h3 class="font-bold text-slate-900 truncate font-mono tracking-wide">{{ coupon.code }}</h3>
                    <span class="font-bold text-indigo-600">
                      {{ coupon.type === 'percentage' ? '%' + coupon.discount : (coupon.type === 'fixed' ? formatCurrency(coupon.discount) : 'Ücretsiz Kargo') }}
                    </span>
                  </div>
                  <p class="text-sm text-slate-500 truncate">{{ coupon.description }}</p>
                  <div class="flex items-center gap-3 mt-1 text-xs text-slate-500">
                    <span>📅 {{ coupon.endDate }}</span>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <span>{{ coupon.usageCount }} / {{ coupon.usageLimit }}</span>
                  </div>
                </div>
                <div class="text-right">
                  <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide" :class="getStatusBadge(coupon.status)">
                    {{ coupon.status === 'active' ? 'Aktif' : (coupon.status === 'expired' ? 'Bitti' : 'Pasif') }}
                  </span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Right Panel: Detail & AI Insights -->
      <div class="w-96 bg-slate-50 border-l border-slate-200 flex flex-col overflow-hidden" v-if="selectedCoupon">
        <div class="p-6 overflow-y-auto">
          
          <!-- Coupon Header -->
          <div class="text-center mb-6 bg-white p-6 rounded-xl border border-slate-200 border-dashed relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
            <h2 class="text-3xl font-black text-slate-900 tracking-widest font-mono mb-1">{{ selectedCoupon.code }}</h2>
            <p class="text-indigo-600 font-bold text-lg mb-2">
              {{ selectedCoupon.type === 'percentage' ? '%' + selectedCoupon.discount + ' İndirim' : (selectedCoupon.type === 'fixed' ? formatCurrency(selectedCoupon.discount) + ' İndirim' : 'Ücretsiz Kargo') }}
            </p>
            <p class="text-slate-500 text-xs">{{ selectedCoupon.description }}</p>
          </div>

          <!-- AI Insights Component -->
          <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
              <span>🧠</span> AI Planlama
            </h3>
            <CouponAIPlanner 
              :code="selectedCoupon.code"
              :discount="selectedCoupon.discount"
              :type="selectedCoupon.type"
              :target-segment="selectedCoupon.targetSegment"
            />
          </div>

          <!-- Usage Stats -->
          <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="p-3 border-b border-slate-100 bg-slate-50">
              <h4 class="font-bold text-slate-800 text-sm">📊 Kullanım İstatistikleri</h4>
            </div>
            <div class="p-4">
              <div class="mb-4">
                <div class="flex justify-between text-xs mb-1">
                  <span class="text-slate-500">Kullanım Limiti</span>
                  <span class="font-bold text-slate-700">%{{ Math.round((selectedCoupon.usageCount / selectedCoupon.usageLimit) * 100) }}</span>
                </div>
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                  <div class="h-full bg-indigo-500 rounded-full" :style="{ width: (selectedCoupon.usageCount / selectedCoupon.usageLimit) * 100 + '%' }"></div>
                </div>
                <div class="flex justify-between text-xs mt-1 text-slate-400">
                  <span>{{ selectedCoupon.usageCount }} Kullanıldı</span>
                  <span>{{ selectedCoupon.usageLimit }} Toplam</span>
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div>
                  <p class="text-xs text-slate-500">Min. Sepet</p>
                  <p class="font-bold text-slate-800">{{ formatCurrency(selectedCoupon.minBasket) }}</p>
                </div>
                <div>
                  <p class="text-xs text-slate-500">Hedef Kitle</p>
                  <p class="font-bold text-slate-800 truncate">{{ selectedCoupon.targetSegment }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="grid grid-cols-2 gap-3">
            <button class="py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold transition text-sm">
              ✏️ Düzenle
            </button>
            <button class="py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold transition text-sm">
              📋 Kopyala
            </button>
            <button class="col-span-2 py-3 bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 rounded-xl font-bold transition text-sm">
              🗑️ Kuponu Sil
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
