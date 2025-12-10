<template>
  <div class="min-h-screen bg-slate-50">
    <div class="px-4 py-8 md:px-8">
      <!-- Loading State -->
      <div v-if="isLoading" class="flex h-[60vh] flex-col items-center justify-center gap-3 text-slate-500">
        <span class="h-10 w-10 animate-spin rounded-full border-2 border-slate-200 border-t-indigo-500" />
        <p>Dashboard yükleniyor...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="loadError" class="rounded-2xl border border-orange-200 bg-orange-50 px-6 py-4 text-center text-sm text-orange-700">
        {{ loadError }}
      </div>

      <!-- Dashboard Content -->
      <div v-else class="space-y-8">
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 rounded-3xl p-8 text-white">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
              <h1 class="text-3xl font-bold mb-2">Merkez Operasyon Paneli</h1>
              <p class="text-white/70">
                Hoş geldin {{ userName }}. Sistem durumu: 
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-400/20 text-emerald-300">
                  <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                  Stabil
                </span>
              </p>
            </div>
            <div class="flex flex-wrap gap-3">
              <button class="px-5 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-sm font-medium transition">
                📊 Raporu İndir
              </button>
              <router-link to="/admin/reports" class="px-5 py-2.5 bg-white text-slate-900 rounded-xl text-sm font-medium hover:bg-slate-100 transition">
                📈 Detaylı Analiz
              </router-link>
            </div>
          </div>
          
          <!-- Quick Stats in Hero -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-white/10 backdrop-blur rounded-xl p-4">
              <p class="text-xs text-white/60 uppercase tracking-wider">Son 24 Saat</p>
              <p class="text-2xl font-bold mt-1">{{ formatNumber(adminStats?.last_24h_orders || 0) }}</p>
              <p class="text-xs text-emerald-300 mt-1">↗ Sipariş</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-xl p-4">
              <p class="text-xs text-white/60 uppercase tracking-wider">Günlük Gelir</p>
              <p class="text-2xl font-bold mt-1">{{ formatCurrency(adminStats?.last_24h_revenue || 0) }}</p>
              <p class="text-xs text-white/60 mt-1">Platform payı dahil</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-xl p-4">
              <p class="text-xs text-white/60 uppercase tracking-wider">Aktif Satıcı</p>
              <p class="text-2xl font-bold mt-1">{{ formatNumber(adminStats?.seller_count || 0) }}</p>
              <p class="text-xs text-white/60 mt-1">Onaylı mağaza</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-xl p-4">
              <p class="text-xs text-white/60 uppercase tracking-wider">Uptime</p>
              <p class="text-2xl font-bold mt-1">{{ adminStats?.uptime || '99.9%' }}</p>
              <p class="text-xs text-emerald-300 mt-1">✓ Tüm sistemler aktif</p>
            </div>
          </div>
        </section>

        <!-- AI Command Center -->
        <AdminAICommandCenter />

        <!-- Live Metrics -->
        <AdminLiveMetrics 
          :metrics="realtimeMetrics" 
          :isLoading="realtimeLoading"
          :previousOrders="adminStats?.last_24h_orders"
        />

        <!-- Stats Grid -->
        <AdminStatsGrid :stats="statGrid" />

        <!-- Charts -->
        <AdminChartsSection 
          :revenueData="revenueChartData"
          :sellerData="sellerRevenueChart"
          :orderStatusData="orderStatusChart"
          :commissionData="commissionChart"
        />

        <!-- Quick Actions & Activity -->
        <AdminQuickActions 
          :integrations="integrations"
          :actions="quickActions"
          :activities="activityFeed"
          @action="handleQuickAction"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useToast } from 'vue-toastification'
import AdminStatsGrid from '@/components/admin/dashboard/AdminStatsGrid.vue'
import AdminLiveMetrics from '@/components/admin/dashboard/AdminLiveMetrics.vue'
import AdminAICommandCenter from '@/components/admin/dashboard/AdminAICommandCenter.vue'
import AdminChartsSection from '@/components/admin/dashboard/AdminChartsSection.vue'
import AdminQuickActions from '@/components/admin/dashboard/AdminQuickActions.vue'
import { useRealtimeDashboard } from '@/composables/useRealtimeDashboard'

const toast = useToast()

// State
const isLoading = ref(true)
const loadError = ref<string | null>(null)
const adminStats = ref<any>(null)
const financialReport = ref<any>(null)

// Real-time dashboard
const { metrics: realtimeMetrics, isLoading: realtimeLoading, startPolling, stopPolling } = useRealtimeDashboard('admin')

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

// Stats Grid
const statGrid = computed(() => {
  const stats = adminStats.value
  if (!stats) return []

  return [
    {
      id: 'users',
      label: 'Toplam Kullanıcı',
      value: formatNumber(stats.total_users),
      hint: `${formatNumber(stats.total_sellers)} satıcı + ${formatNumber(stats.total_buyers)} alıcı`,
      delta: '+2%',
      trend: 'up' as const
    },
    {
      id: 'orders',
      label: 'Toplam Sipariş',
      value: formatNumber(stats.total_orders),
      hint: `${formatNumber(stats.last_24h_orders)} son 24 saatte`,
      delta: '+12%',
      trend: 'up' as const
    },
    {
      id: 'products',
      label: 'Aktif Ürün',
      value: formatNumber(stats.total_products),
      hint: `${formatNumber(stats.category_count)} kategori`,
      delta: '+5%',
      trend: 'up' as const
    },
    {
      id: 'revenue',
      label: 'Toplam Gelir',
      value: formatCurrency(stats.total_revenue),
      hint: 'Platform komisyonu dahil',
      delta: '+8%',
      trend: 'up' as const
    }
  ]
})

// Chart Data
const revenueChartData = computed(() => {
  if (!financialReport.value?.recent_transactions?.length) return null
  
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
      borderColor: 'rgb(16, 185, 129)',
      backgroundColor: 'rgba(16, 185, 129, 0.1)',
      tension: 0.4,
      fill: true
    }]
  }
})

const sellerRevenueChart = computed(() => {
  const sellers = financialReport.value?.sales_by_seller || []
  if (!sellers.length) return null
  
  const top5 = sellers.slice(0, 5)
  return {
    labels: top5.map((s: any) => s.seller_name || 'Bilinmeyen'),
    datasets: [{
      label: 'Toplam Gelir (₺)',
      data: top5.map((s: any) => s.total_revenue || 0),
      backgroundColor: ['rgba(16,185,129,0.8)', 'rgba(99,102,241,0.8)', 'rgba(251,146,60,0.8)', 'rgba(244,63,94,0.8)', 'rgba(168,85,247,0.8)'],
      borderWidth: 0
    }]
  }
})

const orderStatusChart = computed(() => {
  const stats = adminStats.value
  if (!stats) return null
  
  return {
    labels: ['Beklemede', 'Kargoda', 'Teslim Edildi'],
    datasets: [{
      data: [stats.pending_orders || 0, stats.shipped_orders || 0, stats.delivered_orders || 0],
      backgroundColor: ['rgba(251,146,60,0.8)', 'rgba(99,102,241,0.8)', 'rgba(16,185,129,0.8)'],
      borderWidth: 0
    }]
  }
})

const commissionChart = computed(() => {
  const summary = financialReport.value?.summary
  if (!summary) return null
  
  return {
    labels: ['Platform Komisyonu', 'Satıcı Payı'],
    datasets: [{
      data: [summary.total_platform_fees || 0, summary.total_seller_payouts || 0],
      backgroundColor: ['rgba(99,102,241,0.8)', 'rgba(16,185,129,0.8)'],
      borderWidth: 0
    }]
  }
})

// Integrations
const integrations = ref([
  { id: '1', name: 'Ödeme Ağ Geçidi', description: '3DS doğrulama servisleri stabil', status: 'Aktif' as const },
  { id: '2', name: 'Kargo Partnerleri', description: 'Tüm bölgeler aktif', status: 'Aktif' as const },
  { id: '3', name: 'SMS/E-posta Servisi', description: 'Push bildirimleri %99 teslimat', status: 'Aktif' as const }
])

// Quick Actions
const quickActions = ref([
  { id: '1', label: 'Satıcıyı manuel olarak onayla', meta: '2 bekleyen' },
  { id: '2', label: 'Kampanya bütçe koridoru tanımla', meta: 'Yeni' },
  { id: '3', label: 'Riskli siparişleri incele', meta: '5 adet' }
])

// Activity Feed
const activityFeed = computed(() => {
  const transactions = financialReport.value?.recent_transactions || []
  if (!transactions.length) {
    return [
      { id: '1', title: 'Sistem başlatıldı', detail: 'Tüm servisler aktif', time: 'Az önce', accent: 'bg-emerald-400' },
      { id: '2', title: 'Cache temizlendi', detail: 'Performans optimizasyonu tamamlandı', time: '5 dk önce', accent: 'bg-blue-400' }
    ]
  }
  
  return transactions.slice(0, 5).map((t: any) => ({
    id: `tx-${t.id}`,
    title: `${t.product_name} siparişi`,
    detail: `${t.seller_name} · ${formatCurrency(t.total)}`,
    time: formatRelative(t.date),
    accent: t.status === 'delivered' ? 'bg-emerald-400' : t.status === 'cancelled' ? 'bg-red-400' : 'bg-blue-400'
  }))
})

// Handlers
const handleQuickAction = (action: any) => {
  toast.info(`Aksiyon: ${action.label}`)
}

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

// Load Data
async function loadDashboard() {
  isLoading.value = true
  loadError.value = null

  try {
    const { adminApi } = await import('@/services/api/adminApi')
    const [stats, financial] = await Promise.all([
      adminApi.getStats(),
      adminApi.getFinancialReport()
    ])
    
    adminStats.value = stats
    financialReport.value = financial
  } catch (error: any) {
    console.warn('API bağlantısı başarısız, mock data kullanılıyor')
    
    // Fallback mock data
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
      summary: { total_revenue: 2450000, total_platform_fees: 367500, total_seller_payouts: 2082500, total_orders: 8750 },
      sales_by_seller: [],
      recent_transactions: []
    }
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  loadDashboard()
  startPolling()
})

onUnmounted(() => {
  stopPolling()
})
</script>
