<template>
  <div class="min-h-screen bg-slate-50/50">
    <div class="max-w-[1600px] mx-auto p-6 space-y-6">
      
      <!-- Header Section -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Genel Bakış</h1>
          <p class="text-slate-500 mt-1">
            Hoş geldin {{ userName }}, bugün işletmenizde neler oluyor?
          </p>
        </div>
        <div class="flex items-center gap-3">
          <span class="text-sm text-slate-500 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
            {{ new Date().toLocaleDateString('tr-TR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
          </span>
          <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
            <Download class="w-4 h-4" />
            Rapor Al
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex h-64 items-center justify-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      </div>

      <div v-else class="space-y-6">
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Daily Sales -->
          <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-slate-500">Günlük Satış</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ formatCurrency(adminStats?.last_24h_revenue || 0) }}</h3>
              </div>
              <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                <TrendingUp class="w-5 h-5" />
              </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
              <span class="text-emerald-600 font-medium flex items-center gap-1">
                <ArrowUpRight class="w-3 h-3" /> %12
              </span>
              <span class="text-slate-400 ml-2">düne göre</span>
            </div>
          </div>

          <!-- Daily Orders -->
          <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-slate-500">Bugünkü Sipariş</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ formatNumber(adminStats?.last_24h_orders || 0) }}</h3>
              </div>
              <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <ShoppingBag class="w-5 h-5" />
              </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
              <span class="text-emerald-600 font-medium flex items-center gap-1">
                <ArrowUpRight class="w-3 h-3" /> %5
              </span>
              <span class="text-slate-400 ml-2">düne göre</span>
            </div>
          </div>

          <!-- Active Sellers -->
          <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-slate-500">Aktif Satıcı</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ formatNumber(adminStats?.seller_count || 0) }}</h3>
              </div>
              <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                <Store class="w-5 h-5" />
              </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
              <span class="text-slate-600 font-medium">48</span>
              <span class="text-slate-400 ml-2">onaylı mağaza</span>
            </div>
          </div>

          <!-- Pending Actions -->
          <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium text-slate-500">Bekleyen İşlemler</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ formatNumber(adminStats?.pending_orders || 0) }}</h3>
              </div>
              <div class="p-2 bg-orange-50 text-orange-600 rounded-lg">
                <AlertCircle class="w-5 h-5" />
              </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
              <span class="text-orange-600 font-medium">Aksiyon gerekiyor</span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Chart Section -->
          <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-6">
              <h3 class="font-semibold text-slate-900">Satış Performansı</h3>
              <select class="text-sm border-slate-200 rounded-lg text-slate-600 focus:ring-indigo-500 focus:border-indigo-500">
                <option>Son 7 Gün</option>
                <option>Son 30 Gün</option>
                <option>Bu Yıl</option>
              </select>
            </div>
            <div class="h-[300px] w-full">
               <LineChart 
                  v-if="revenueChartData"
                  :data="revenueChartData"
                  :height="300"
                />
            </div>
          </div>

          <!-- To-Do List / Action Center -->
          <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
            <h3 class="font-semibold text-slate-900 mb-4">Yapılacaklar Listesi</h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-100 cursor-pointer hover:bg-orange-100 transition-colors">
                <div class="flex items-center gap-3">
                  <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                  <div>
                    <p class="text-sm font-medium text-slate-900">Satıcı Başvurusu (2)</p>
                    <p class="text-xs text-slate-500">Belge onayı bekliyor</p>
                  </div>
                </div>
                <ChevronRight class="w-4 h-4 text-orange-400" />
              </div>

              <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100 cursor-pointer hover:bg-blue-100 transition-colors">
                <div class="flex items-center gap-3">
                  <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                  <div>
                    <p class="text-sm font-medium text-slate-900">Ürün Onayı (15)</p>
                    <p class="text-xs text-slate-500">Katalog kontrolü</p>
                  </div>
                </div>
                <ChevronRight class="w-4 h-4 text-blue-400" />
              </div>

              <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-100 cursor-pointer hover:bg-red-100 transition-colors">
                <div class="flex items-center gap-3">
                  <div class="w-2 h-2 rounded-full bg-red-500"></div>
                  <div>
                    <p class="text-sm font-medium text-slate-900">İade Talepleri (5)</p>
                    <p class="text-xs text-slate-500">Müşteri onayı</p>
                  </div>
                </div>
                <ChevronRight class="w-4 h-4 text-red-400" />
              </div>

              <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-100 cursor-pointer hover:bg-slate-100 transition-colors">
                <div class="flex items-center gap-3">
                  <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                  <div>
                    <p class="text-sm font-medium text-slate-900">Destek Talepleri (3)</p>
                    <p class="text-xs text-slate-500">Yanıt bekleyen</p>
                  </div>
                </div>
                <ChevronRight class="w-4 h-4 text-slate-400" />
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
          <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-900">Son Siparişler</h3>
            <router-link to="/admin/orders" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
              Tümünü Gör
            </router-link>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
              <thead class="bg-slate-50 text-slate-500 font-medium">
                <tr>
                  <th class="px-6 py-3">Sipariş No</th>
                  <th class="px-6 py-3">Müşteri</th>
                  <th class="px-6 py-3">Tutar</th>
                  <th class="px-6 py-3">Durum</th>
                  <th class="px-6 py-3">Tarih</th>
                  <th class="px-6 py-3 text-right">İşlem</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-slate-50/50 transition-colors">
                  <td class="px-6 py-4 font-medium text-slate-900">#{{ order.id }}</td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">
                        {{ order.customer.charAt(0) }}
                      </div>
                      <span>{{ order.customer }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 font-medium">{{ formatCurrency(order.total) }}</td>
                  <td class="px-6 py-4">
                    <span :class="[
                      'px-2.5 py-0.5 rounded-full text-xs font-medium',
                      order.status === 'delivered' ? 'bg-emerald-100 text-emerald-700' :
                      order.status === 'pending' ? 'bg-orange-100 text-orange-700' :
                      order.status === 'shipped' ? 'bg-blue-100 text-blue-700' :
                      'bg-slate-100 text-slate-700'
                    ]">
                      {{ getStatusLabel(order.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-slate-500">{{ formatRelative(order.date) }}</td>
                  <td class="px-6 py-4 text-right">
                    <button class="text-slate-400 hover:text-indigo-600 transition-colors">
                      <Eye class="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { 
  TrendingUp, 
  ShoppingBag, 
  Store, 
  AlertCircle, 
  ArrowUpRight, 
  Download, 
  ChevronRight,
  Eye
} from 'lucide-vue-next'
import LineChart from '@/components/charts/LineChart.vue'

// State
const isLoading = ref(true)
const adminStats = ref<any>(null)
const financialReport = ref<any>(null)

// Mock Data for Recent Orders (since API might not return this exact structure)
const recentOrders = ref([
  { id: '1234', customer: 'Ahmet Yılmaz', total: 1250, status: 'pending', date: new Date().toISOString() },
  { id: '1233', customer: 'Ayşe Demir', total: 450, status: 'shipped', date: new Date(Date.now() - 3600000).toISOString() },
  { id: '1232', customer: 'Mehmet Kaya', total: 2800, status: 'delivered', date: new Date(Date.now() - 7200000).toISOString() },
  { id: '1231', customer: 'Zeynep Çelik', total: 150, status: 'delivered', date: new Date(Date.now() - 86400000).toISOString() },
  { id: '1230', customer: 'Can Vural', total: 3400, status: 'cancelled', date: new Date(Date.now() - 172800000).toISOString() },
])

// User name
const userName = computed(() => {
  const user = localStorage.getItem('user')
  if (user) {
    try {
      return JSON.parse(user).name || 'Admin'
    } catch { return 'Admin' }
  }
  return 'Admin'
})

// Chart Data
const revenueChartData = computed(() => {
  if (!financialReport.value?.recent_transactions?.length) {
    // Mock chart data if empty
    const labels = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz']
    const data = [12000, 19000, 15000, 22000, 28000, 24000, 32000]
    return {
      labels,
      datasets: [{
        label: 'Gelir (₺)',
        data,
        borderColor: 'rgb(79, 70, 229)', // Indigo 600
        backgroundColor: 'rgba(79, 70, 229, 0.1)',
        tension: 0.4,
        fill: true
      }]
    }
  }
  
  const last7Days = Array.from({ length: 7 }, (_, i) => {
    const date = new Date()
    date.setDate(date.getDate() - (6 - i))
    return date.toISOString().split('T')[0]
  })
  
  const revenueByDay = last7Days.map(day => {
    const dayTransactions = financialReport.value?.recent_transactions.filter(
      (t: any) => t.date?.startsWith(day)
    ) || []
    return dayTransactions.reduce((sum: number, t: any) => sum + (t.total || 0), 0)
  })
  
  return {
    labels: last7Days.map(d => new Date(d).toLocaleDateString('tr-TR', { weekday: 'short', day: 'numeric' })),
    datasets: [{
      label: 'Gelir (₺)',
      data: revenueByDay,
      borderColor: 'rgb(79, 70, 229)',
      backgroundColor: 'rgba(79, 70, 229, 0.1)',
      tension: 0.4,
      fill: true
    }]
  }
})

// Helpers
function formatNumber(value: number) {
  return new Intl.NumberFormat('tr-TR').format(value ?? 0)
}

function formatCurrency(value: number) {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 0 }).format(value ?? 0)
}

function formatRelative(dateStr?: string) {
  if (!dateStr) return '—'
  const diff = Date.now() - new Date(dateStr).getTime()
  const minutes = Math.floor(diff / 60000)
  if (minutes < 60) return `${minutes} dk önce`
  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `${hours} sa önce`
  return `${Math.floor(hours / 24)} gün önce`
}

function getStatusLabel(status: string) {
  const map: Record<string, string> = {
    'pending': 'Beklemede',
    'shipped': 'Kargoda',
    'delivered': 'Teslim Edildi',
    'cancelled': 'İptal',
    'refunded': 'İade'
  }
  return map[status] || status
}

// Load Data
async function loadDashboard() {
  isLoading.value = true

  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Mock data
    adminStats.value = {
      total_users: 1250,
      total_sellers: 48,
      total_buyers: 1202,
      total_products: 3420,
      total_orders: 8750,
      total_revenue: 2450000,
      pending_orders: 45,
      shipped_orders: 120,
      delivered_orders: 8585,
      last_24h_orders: 156,
      last_24h_revenue: 48500,
      category_count: 24,
      seller_count: 48,
      uptime: '99.9%'
    }
    
    financialReport.value = {
      summary: { total_revenue: 2450000 },
      recent_transactions: []
    }
  } catch (error) {
    console.error(error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadDashboard()
})
</script>
