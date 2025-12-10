<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import CustomerSegmentation from '@/components/admin/customer/CustomerSegmentation.vue'
import CustomerDetailAI from '@/components/admin/customer/CustomerDetailAI.vue'

const customers = ref([])
const loading = ref(false)
const selectedCustomer = ref<any>(null)
const searchQuery = ref('')
const activeTab = ref('list') // list, segments

const filteredCustomers = computed(() => {
  if (!searchQuery.value) return customers.value
  const query = searchQuery.value.toLowerCase()
  return customers.value.filter(c => 
    (c.name || '').toLowerCase().includes(query) || 
    (c.email || '').toLowerCase().includes(query) ||
    (c.phone || '').includes(query)
  )
})

const selectCustomer = (customer: any) => {
  selectedCustomer.value = customer
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)
}

const getStatusBadge = (status: string) => {
  switch (status) {
    case 'active': return 'bg-emerald-100 text-emerald-700'
    case 'inactive': return 'bg-slate-100 text-slate-700'
    case 'blocked': return 'bg-red-100 text-red-700'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const getSegmentBadge = (segment: string) => {
  switch (segment) {
    case 'VIP': return 'bg-purple-100 text-purple-700 border-purple-200'
    case 'Sadık': return 'bg-blue-100 text-blue-700 border-blue-200'
    case 'Yeni': return 'bg-green-100 text-green-700 border-green-200'
    case 'Riskli': return 'bg-orange-100 text-orange-700 border-orange-200'
    default: return 'bg-slate-100 text-slate-700 border-slate-200'
  }
}

onMounted(async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/admin/customers')
    customers.value = response.data.data || response.data
    
    // Select first customer by default
    if (customers.value.length > 0) {
      selectedCustomer.value = customers.value[0]
    }
  } catch (error) {
    console.error('Failed to fetch customers:', error)
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
          👥 Müşteri Yönetimi
          <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">AI Powered</span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">Müşteri segmentasyonu ve yaşam boyu değer analizi</p>
      </div>
      <div class="flex gap-3">
        <button class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 transition flex items-center gap-2">
          <span>📥</span> Dışa Aktar
        </button>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 flex items-center gap-2">
          <span>➕</span> Yeni Müşteri
        </button>
      </div>
    </header>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
      <!-- Left Panel: List & Segmentation -->
      <div class="flex-1 flex flex-col min-w-0 border-r border-slate-200 bg-white">
        
        <!-- Tabs -->
        <div class="flex border-b border-slate-200">
          <button 
            @click="activeTab = 'list'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'list' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            📋 Müşteri Listesi
          </button>
          <button 
            @click="activeTab = 'segments'"
            class="flex-1 py-3 text-sm font-bold border-b-2 transition"
            :class="activeTab === 'segments' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
          >
            📊 Segment Analizi
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
                  placeholder="Müşteri ara..." 
                  class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                >
              </div>
              <button class="px-3 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">
                🌪️ Filtrele
              </button>
            </div>

            <!-- Customer List -->
            <div class="space-y-2">
              <div 
                v-for="customer in filteredCustomers" 
                :key="customer.id"
                @click="selectCustomer(customer)"
                class="p-3 rounded-xl border transition cursor-pointer group relative overflow-hidden"
                :class="selectedCustomer?.id === customer.id ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200' : 'bg-white border-slate-100 hover:border-indigo-200 hover:shadow-md'"
              >
                <div class="flex items-center gap-4">
                  <img :src="customer.avatar" class="w-12 h-12 rounded-full border-2 border-white shadow-sm" alt="">
                  <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                      <h3 class="font-bold text-slate-900 truncate">{{ customer.name }}</h3>
                      <span class="text-xs px-2 py-0.5 rounded-full border font-bold" :class="getSegmentBadge(customer.segment)">
                        {{ customer.segment }}
                      </span>
                    </div>
                    <p class="text-sm text-slate-500 truncate">{{ customer.email }}</p>
                    <div class="flex items-center gap-3 mt-1 text-xs text-slate-500">
                      <span>🛒 {{ customer.orderCount }} Sipariş</span>
                      <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                      <span class="font-medium text-slate-700">{{ formatCurrency(customer.totalSpent) }}</span>
                    </div>
                  </div>
                  <div class="text-right">
                    <span class="inline-block w-2 h-2 rounded-full" :class="customer.status === 'active' ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Segments View -->
          <div v-else class="h-full">
            <CustomerSegmentation />
          </div>

        </div>
      </div>

      <!-- Right Panel: Detail & AI Insights -->
      <div class="w-96 bg-slate-50 border-l border-slate-200 flex flex-col overflow-hidden" v-if="selectedCustomer">
        <div class="p-6 overflow-y-auto">
          
          <!-- Profile Header -->
          <div class="text-center mb-6">
            <div class="relative inline-block">
              <img :src="selectedCustomer.avatar" class="w-24 h-24 rounded-full border-4 border-white shadow-lg mx-auto mb-3" alt="">
              <span class="absolute bottom-2 right-2 w-5 h-5 rounded-full border-2 border-white" :class="selectedCustomer.status === 'active' ? 'bg-emerald-500' : 'bg-slate-400'"></span>
            </div>
            <h2 class="text-xl font-bold text-slate-900">{{ selectedCustomer.name }}</h2>
            <p class="text-slate-500 text-sm">{{ selectedCustomer.email }}</p>
            <p class="text-slate-400 text-xs mt-1">{{ selectedCustomer.phone }}</p>
            
            <div class="flex justify-center gap-2 mt-4">
              <button class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition" title="Düzenle">
                ✏️
              </button>
              <button class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition" title="Mesaj Gönder">
                💬
              </button>
              <button class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition" title="Sipariş Geçmişi">
                📦
              </button>
            </div>
          </div>

          <!-- AI Insights Component -->
          <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
              <span>🧠</span> AI Analizi
            </h3>
            <CustomerDetailAI 
              :customer-id="selectedCustomer.id"
              :name="selectedCustomer.name"
              :total-spent="selectedCustomer.totalSpent"
              :order-count="selectedCustomer.orderCount"
            />
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-2 gap-3 mb-6">
            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
              <p class="text-xs text-slate-500 mb-1">Son Sipariş</p>
              <p class="font-bold text-slate-800">{{ selectedCustomer.lastOrderDate }}</p>
            </div>
            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
              <p class="text-xs text-slate-500 mb-1">Ort. Sepet</p>
              <p class="font-bold text-slate-800">
                {{ selectedCustomer.orderCount > 0 ? formatCurrency(selectedCustomer.totalSpent / selectedCustomer.orderCount) : '-' }}
              </p>
            </div>
          </div>

          <!-- Notes -->
          <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
            <h4 class="text-yellow-800 font-bold text-sm mb-2">📌 Müşteri Notları</h4>
            <p class="text-yellow-700 text-xs leading-relaxed">
              Müşteri premium ürünlere ilgi gösteriyor. İade oranı düşük. Son görüşmede kargo hızından memnun olduğunu belirtti.
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
