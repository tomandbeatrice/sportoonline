<template>
  <div class="min-h-screen bg-slate-50 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Satış Raporları</h1>
          <p class="text-slate-500">Detaylı satış analizleri ve raporlar</p>
        </div>
        <div class="flex gap-3">
          <select v-model="dateRange" class="px-4 py-2 border border-slate-200 rounded-lg text-sm">
            <option value="today">Bugün</option>
            <option value="week">Bu Hafta</option>
            <option value="month">Bu Ay</option>
            <option value="quarter">Bu Çeyrek</option>
            <option value="year">Bu Yıl</option>
            <option value="custom">Özel Tarih</option>
          </select>
          <button @click="exportReport" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm hover:bg-orange-700">
            📥 Rapor İndir (CSV)
          </button>
        </div>
      </div>

      <!-- Revenue Stats -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-6 shadow-sm">
          <p class="text-sm text-slate-500 mb-2">Toplam Gelir</p>
          <p class="text-3xl font-bold text-slate-900">{{ formatPrice(metrics.totalRevenue) }} TL</p>
          <p class="text-sm text-green-600 mt-2">↗️ +12.5% geçen aya göre</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm">
          <p class="text-sm text-slate-500 mb-2">Toplam Sipariş</p>
          <p class="text-3xl font-bold text-slate-900">{{ metrics.totalOrders }}</p>
          <p class="text-sm text-green-600 mt-2">↗️ +8.3% geçen aya göre</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm">
          <p class="text-sm text-slate-500 mb-2">Ortalama Sepet</p>
          <p class="text-3xl font-bold text-slate-900">{{ formatPrice(metrics.avgOrderValue) }} TL</p>
          <p class="text-sm text-blue-600 mt-2">↗️ +5.2% geçen aya göre</p>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm">
          <p class="text-sm text-slate-500 mb-2">İade Oranı</p>
          <p class="text-3xl font-bold text-slate-900">%{{ metrics.returnRate }}</p>
          <p class="text-sm text-red-600 mt-2">↘️ -2.1% geçen aya göre</p>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <h3 class="font-bold text-slate-900 mb-4">Gelir Trendi (30 Gün)</h3>
          <div class="h-64 flex items-end justify-between gap-1">
            <div 
              v-for="(day, index) in revenueTrend" 
              :key="index"
              class="flex-1 bg-gradient-to-t from-orange-500 to-orange-300 rounded-t hover:from-orange-600 hover:to-orange-400 cursor-pointer transition"
              :style="{height: (day.revenue / Math.max(...revenueTrend.map(d => d.revenue))) * 100 + '%'}"
              :title="`${day.date}: ${formatPrice(day.revenue)} TL`"
            ></div>
          </div>
          <div class="flex justify-between mt-4 text-xs text-slate-500">
            <span>30 gün önce</span>
            <span>Bugün</span>
          </div>
        </div>

        <!-- Category Distribution -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <h3 class="font-bold text-slate-900 mb-4">Kategori Dağılımı</h3>
          <div class="space-y-3">
            <div v-for="cat in categoryStats" :key="cat.name">
              <div class="flex justify-between text-sm mb-1">
                <span class="text-slate-700">{{ cat.name }}</span>
                <span class="font-medium text-slate-900">{{ formatPrice(cat.revenue) }} TL ({{ cat.percentage }}%)</span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-2">
                <div :style="{width: cat.percentage + '%'}" :class="cat.color" class="h-2 rounded-full"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Best Sellers -->
      <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h3 class="font-bold text-slate-900 mb-4">En Çok Satan Ürünler</h3>
        <table class="w-full">
          <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
              <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Ürün</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Satış</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Gelir</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Stok</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Trend</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="product in topProducts" :key="product.id" class="hover:bg-slate-50">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <img :src="product.image" class="w-12 h-12 rounded-lg object-cover" />
                  <div>
                    <p class="font-medium text-slate-900">{{ product.name }}</p>
                    <p class="text-xs text-slate-500">{{ product.sku }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3">
                <p class="font-bold text-slate-900">{{ product.sales }} adet</p>
              </td>
              <td class="px-4 py-3">
                <p class="font-bold text-slate-900">{{ formatPrice(product.revenue) }} TL</p>
              </td>
              <td class="px-4 py-3">
                <p :class="product.stock < 10 ? 'text-red-600' : 'text-green-600'" class="font-medium">
                  {{ product.stock }} adet
                </p>
              </td>
              <td class="px-4 py-3">
                <span :class="product.trend === 'up' ? 'text-green-600' : 'text-red-600'">
                  {{ product.trend === 'up' ? '↗️ +' : '↘️ ' }}{{ product.trendPercentage }}%
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Campaign Performance -->
      <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h3 class="font-bold text-slate-900 mb-4">Kampanya Performansı</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div v-for="campaign in campaignPerformance" :key="campaign.id" class="p-4 border border-slate-200 rounded-xl">
            <h4 class="font-medium text-slate-900 mb-2">{{ campaign.name }}</h4>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-slate-600">Görüntülenme:</span>
                <span class="font-medium">{{ campaign.views }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">Sipariş:</span>
                <span class="font-medium">{{ campaign.orders }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">Gelir:</span>
                <span class="font-medium text-green-600">{{ formatPrice(campaign.revenue) }} TL</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">ROI:</span>
                <span :class="campaign.roi > 0 ? 'text-green-600' : 'text-red-600'" class="font-bold">
                  %{{ campaign.roi }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Return Statistics -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Return Reasons -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <h3 class="font-bold text-slate-900 mb-4">İade Nedenleri</h3>
          <div class="space-y-3">
            <div v-for="reason in returnReasons" :key="reason.reason">
              <div class="flex justify-between text-sm mb-1">
                <span class="text-slate-700">{{ reason.reason }}</span>
                <span class="font-medium text-slate-900">{{ reason.count }} ({{ reason.percentage }}%)</span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-2">
                <div :style="{width: reason.percentage + '%'}" class="bg-red-500 h-2 rounded-full"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- SLA Performance -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <h3 class="font-bold text-slate-900 mb-4">SLA Performansı</h3>
          <div class="space-y-4">
            <div>
              <div class="flex justify-between mb-2">
                <span class="text-sm text-slate-600">Sipariş Hazırlama Süresi</span>
                <span class="text-sm font-medium text-slate-900">{{ slaMetrics.avgPreparationTime }} saat</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-200 rounded-full h-2">
                  <div :style="{width: slaMetrics.preparationSLA + '%'}" class="bg-green-500 h-2 rounded-full"></div>
                </div>
                <span class="text-sm font-bold text-green-600">{{ slaMetrics.preparationSLA }}%</span>
              </div>
            </div>
            <div>
              <div class="flex justify-between mb-2">
                <span class="text-sm text-slate-600">İade Yanıt Süresi</span>
                <span class="text-sm font-medium text-slate-900">{{ slaMetrics.avgReturnResponseTime }} saat</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-200 rounded-full h-2">
                  <div :style="{width: slaMetrics.returnResponseSLA + '%'}" class="bg-blue-500 h-2 rounded-full"></div>
                </div>
                <span class="text-sm font-bold text-blue-600">{{ slaMetrics.returnResponseSLA }}%</span>
              </div>
            </div>
            <div>
              <div class="flex justify-between mb-2">
                <span class="text-sm text-slate-600">Müşteri Memnuniyeti</span>
                <span class="text-sm font-medium text-slate-900">{{ slaMetrics.customerSatisfaction }}/5</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-200 rounded-full h-2">
                  <div :style="{width: (slaMetrics.customerSatisfaction / 5) * 100 + '%'}" class="bg-purple-500 h-2 rounded-full"></div>
                </div>
                <span class="text-sm font-bold text-purple-600">{{ ((slaMetrics.customerSatisfaction / 5) * 100).toFixed(0) }}%</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

// State
const dateRange = ref('month')

const metrics = ref({
  totalRevenue: 284500,
  totalOrders: 1247,
  avgOrderValue: 228,
  returnRate: 3.2
})

const revenueTrend = ref(
  Array.from({ length: 30 }, (_, i) => ({
    date: `2025-11-${String(i + 1).padStart(2, '0')}`,
    revenue: Math.random() * 15000 + 5000
  }))
)

const categoryStats = ref([
  { name: 'Elektronik', revenue: 145600, percentage: 51, color: 'bg-blue-500' },
  { name: 'Giyim', revenue: 78900, percentage: 28, color: 'bg-purple-500' },
  { name: 'Spor', revenue: 45300, percentage: 16, color: 'bg-green-500' },
  { name: 'Diğer', revenue: 14700, percentage: 5, color: 'bg-slate-500' }
])

const topProducts = ref([
  {
    id: 1,
    name: 'iPhone 14 Pro 256GB',
    sku: 'IPH14-PRO-256',
    sales: 87,
    revenue: 3740730,
    stock: 12,
    image: 'https://via.placeholder.com/50',
    trend: 'up',
    trendPercentage: 15.3
  },
  {
    id: 2,
    name: 'Samsung Galaxy S24',
    sku: 'SAM-S24-128',
    sales: 64,
    revenue: 1855936,
    stock: 8,
    image: 'https://via.placeholder.com/50',
    trend: 'up',
    trendPercentage: 8.7
  },
  {
    id: 3,
    name: 'Nike Air Max 2024',
    sku: 'NIKE-AM-2024',
    sales: 142,
    revenue: 354858,
    stock: 3,
    image: 'https://via.placeholder.com/50',
    trend: 'down',
    trendPercentage: 5.2
  }
])

const campaignPerformance = ref([
  { id: 1, name: 'Kış İndirimi 2025', views: 12450, orders: 189, revenue: 45600, roi: 285 },
  { id: 2, name: 'Ücretsiz Kargo', views: 8900, orders: 156, revenue: 38900, roi: 195 },
  { id: 3, name: 'Elektronik Festivali', views: 5600, orders: 78, revenue: 42000, roi: 420 }
])

const returnReasons = ref([
  { reason: 'Ürün hasarlı geldi', count: 12, percentage: 38 },
  { reason: 'Beklentimi karşılamadı', count: 8, percentage: 25 },
  { reason: 'Yanlış ürün gönderildi', count: 6, percentage: 19 },
  { reason: 'Fikir değiştirdim', count: 4, percentage: 13 },
  { reason: 'Diğer', count: 2, percentage: 5 }
])

const slaMetrics = ref({
  avgPreparationTime: 18,
  preparationSLA: 92,
  avgReturnResponseTime: 12,
  returnResponseSLA: 88,
  customerSatisfaction: 4.6
})

// Methods
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(price)
}

const exportReport = () => {
  toast.success('Rapor indiriliyor...')
  
  // Create CSV data
  const csvHeaders = ['Metrik', 'Değer']
  const csvRows = [
    ['Toplam Gelir', `${formatPrice(metrics.value.totalRevenue)} TL`],
    ['Toplam Sipariş', metrics.value.totalOrders],
    ['Ortalama Sepet', `${formatPrice(metrics.value.avgOrderValue)} TL`],
    ['İade Oranı', `%${metrics.value.returnRate}`]
  ]
  
  const csvContent = [csvHeaders, ...csvRows].map(row => row.join(',')).join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = `satis-raporu-${new Date().toISOString().split('T')[0]}.csv`
  link.click()
}
</script>
