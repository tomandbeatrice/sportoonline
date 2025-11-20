<template>
  <div class="advanced-reports p-6 space-y-6">
    <h2 class="text-2xl font-bold text-gray-900">Gelişmiş Raporlama</h2>

    <!-- Report Type Selection -->
    <div class="bg-white rounded-xl shadow-sm p-6">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button
          v-for="type in reportTypes"
          :key="type.id"
          @click="selectedReportType = type.id"
          class="p-4 border-2 rounded-lg transition-all"
          :class="selectedReportType === type.id ? 'border-blue-600 bg-blue-50' : 'border-gray-300 hover:border-blue-400'"
        >
          <div class="text-3xl mb-2">{{ type.icon }}</div>
          <div class="font-semibold text-gray-900">{{ type.label }}</div>
        </button>
      </div>
    </div>

    <!-- Date Range & Filters -->
    <div class="bg-white rounded-xl shadow-sm p-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Başlangıç Tarihi</label>
          <input
            v-model="filters.start_date"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Bitiş Tarihi</label>
          <input
            v-model="filters.end_date"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div v-if="selectedReportType === 'vendor'">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Satıcı</label>
          <select
            v-model="filters.vendor_id"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Tüm Satıcılar</option>
            <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
              {{ vendor.name }}
            </option>
          </select>
        </div>
        <div v-if="selectedReportType === 'product'">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
          <select
            v-model="filters.category_id"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Tüm Kategoriler</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
        <div class="flex items-end gap-2">
          <button
            @click="generateReport"
            :disabled="loading"
            class="flex-1 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold disabled:bg-gray-400"
          >
            {{ loading ? 'Yükleniyor...' : 'Rapor Oluştur' }}
          </button>
          <button
            @click="exportReport"
            :disabled="!reportData"
            class="px-6 py-2 border-2 border-green-600 text-green-600 hover:bg-green-50 rounded-lg font-semibold disabled:border-gray-300 disabled:text-gray-300"
          >
            📥 Excel
          </button>
        </div>
      </div>
    </div>

    <!-- Sales Report -->
    <div v-if="selectedReportType === 'sales' && reportData" class="space-y-6">
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Toplam Satış</div>
          <div class="text-3xl font-bold text-blue-600">₺{{ formatMoney(reportData.total_sales) }}</div>
          <div class="text-xs text-green-600 mt-1">↗ {{ reportData.growth_rate }}%</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Sipariş Sayısı</div>
          <div class="text-3xl font-bold text-purple-600">{{ reportData.total_orders }}</div>
          <div class="text-xs text-gray-500 mt-1">Ortalama: ₺{{ formatMoney(reportData.avg_order_value) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Net Kar</div>
          <div class="text-3xl font-bold text-green-600">₺{{ formatMoney(reportData.net_profit) }}</div>
          <div class="text-xs text-gray-500 mt-1">Kar Marjı: {{ reportData.profit_margin }}%</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Komisyon</div>
          <div class="text-3xl font-bold text-orange-600">₺{{ formatMoney(reportData.commission) }}</div>
          <div class="text-xs text-gray-500 mt-1">{{ reportData.commission_rate }}% oran</div>
        </div>
      </div>

      <!-- Sales Chart -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Satış Trendi</h3>
        <canvas ref="salesChart"></canvas>
      </div>

      <!-- Top Products Table -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">En Çok Satan Ürünler</h3>
        <table class="w-full">
          <thead class="border-b-2 border-gray-200">
            <tr>
              <th class="text-left py-3 px-4">Ürün</th>
              <th class="text-right py-3 px-4">Satış Adedi</th>
              <th class="text-right py-3 px-4">Gelir</th>
              <th class="text-right py-3 px-4">Kar</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="product in reportData.top_products" :key="product.id" class="hover:bg-gray-50">
              <td class="py-3 px-4">{{ product.name }}</td>
              <td class="text-right py-3 px-4">{{ product.quantity }}</td>
              <td class="text-right py-3 px-4">₺{{ formatMoney(product.revenue) }}</td>
              <td class="text-right py-3 px-4 text-green-600 font-semibold">₺{{ formatMoney(product.profit) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Vendor Performance Report -->
    <div v-if="selectedReportType === 'vendor' && reportData" class="space-y-6">
      <!-- Vendor Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Aktif Satıcı</div>
          <div class="text-3xl font-bold text-blue-600">{{ reportData.active_vendors }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Toplam Ürün</div>
          <div class="text-3xl font-bold text-purple-600">{{ reportData.total_products }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Ortalama Puan</div>
          <div class="text-3xl font-bold text-yellow-600 inline-flex items-center gap-2">{{ reportData.avg_rating.toFixed(1) }} <IconStar cls="w-8 h-8 text-yellow-400" :filled="true" /></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Komisyon Geliri</div>
          <div class="text-3xl font-bold text-green-600">₺{{ formatMoney(reportData.commission_revenue) }}</div>
        </div>
      </div>

      <!-- Vendor Rankings -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Satıcı Performans Sıralaması</h3>
        <table class="w-full">
          <thead class="border-b-2 border-gray-200">
            <tr>
              <th class="text-left py-3 px-4">Sıra</th>
              <th class="text-left py-3 px-4">Satıcı</th>
              <th class="text-right py-3 px-4">Satış</th>
              <th class="text-right py-3 px-4">Sipariş</th>
              <th class="text-right py-3 px-4">Puan</th>
              <th class="text-right py-3 px-4">Ort. Teslimat</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="(vendor, index) in reportData.vendor_rankings" :key="vendor.id" class="hover:bg-gray-50">
              <td class="py-3 px-4">
                <span class="font-bold text-lg" :class="index === 0 ? 'text-yellow-500' : index === 1 ? 'text-gray-400' : index === 2 ? 'text-orange-600' : 'text-gray-600'">
                  {{ index + 1 }}
                </span>
              </td>
              <td class="py-3 px-4 font-semibold">{{ vendor.name }}</td>
              <td class="text-right py-3 px-4">₺{{ formatMoney(vendor.total_sales) }}</td>
              <td class="text-right py-3 px-4">{{ vendor.order_count }}</td>
              <td class="text-right py-3 px-4 inline-flex items-center justify-end gap-1">{{ vendor.rating.toFixed(1) }} <IconStar cls="w-4 h-4 text-yellow-400" :filled="true" /></td>
              <td class="text-right py-3 px-4">{{ vendor.avg_delivery }} gün</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Product Analytics -->
    <div v-if="selectedReportType === 'product' && reportData" class="space-y-6">
      <!-- Product Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Toplam Ürün</div>
          <div class="text-3xl font-bold text-blue-600">{{ reportData.total_products }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Stok Değeri</div>
          <div class="text-3xl font-bold text-purple-600">₺{{ formatMoney(reportData.stock_value) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Stok Dışı</div>
          <div class="text-3xl font-bold text-red-600">{{ reportData.out_of_stock }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Düşük Stok</div>
          <div class="text-3xl font-bold text-orange-600">{{ reportData.low_stock }}</div>
        </div>
      </div>

      <!-- Category Performance -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Kategori Performansı</h3>
        <canvas ref="categoryChart"></canvas>
      </div>
    </div>

    <!-- Customer Segmentation -->
    <div v-if="selectedReportType === 'customer' && reportData" class="space-y-6">
      <!-- Customer Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Toplam Müşteri</div>
          <div class="text-3xl font-bold text-blue-600">{{ reportData.total_customers }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Aktif Müşteri</div>
          <div class="text-3xl font-bold text-green-600">{{ reportData.active_customers }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Ort. Sipariş Değeri</div>
          <div class="text-3xl font-bold text-purple-600">₺{{ formatMoney(reportData.avg_order_value) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="text-sm text-gray-600 mb-1">Müşteri LTV</div>
          <div class="text-3xl font-bold text-orange-600">₺{{ formatMoney(reportData.customer_ltv) }}</div>
        </div>
      </div>

      <!-- Customer Segments -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Müşteri Segmentasyonu</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div
            v-for="segment in reportData.segments"
            :key="segment.name"
            class="border-2 border-gray-200 rounded-lg p-4 hover:border-blue-500 transition-colors"
          >
            <div class="text-lg font-bold text-gray-900 mb-2">{{ segment.name }}</div>
            <div class="text-sm text-gray-600 mb-3">{{ segment.description }}</div>
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-gray-600">Müşteri Sayısı:</span>
                <span class="font-semibold">{{ segment.count }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Toplam Harcama:</span>
                <span class="font-semibold">₺{{ formatMoney(segment.total_spent) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Ortalama Sipariş:</span>
                <span class="font-semibold">{{ segment.avg_orders }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!reportData && !loading" class="bg-white rounded-xl shadow-sm p-12 text-center">
      <div class="text-6xl mb-4">📊</div>
      <div class="text-xl font-semibold text-gray-900 mb-2">Gelişmiş Raporlama</div>
      <div class="text-gray-500">Bir rapor türü seçin ve "Rapor Oluştur" butonuna tıklayın</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'
import IconStar from '@/components/icons/IconStar.vue'

const reportTypes = [
  { id: 'sales', label: 'Satış Raporları', icon: '💰' },
  { id: 'vendor', label: 'Satıcı Performansı', icon: '🏪' },
  { id: 'product', label: 'Ürün Analitiği', icon: '📦' },
  { id: 'customer', label: 'Müşteri Segmentasyonu', icon: '👥' }
]

const selectedReportType = ref('sales')
const reportData = ref(null)
const loading = ref(false)
const vendors = ref([])
const categories = ref([])

const filters = ref({
  start_date: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0],
  vendor_id: '',
  category_id: ''
})

const salesChart = ref(null)
const categoryChart = ref(null)
let salesChartInstance = null
let categoryChartInstance = null

const generateReport = async () => {
  loading.value = true
  try {
    const { data } = await axios.get(`/api/admin/reports/${selectedReportType.value}`, {
      params: filters.value
    })
    reportData.value = data
    
    await nextTick()
    renderCharts()
  } catch (error) {
    console.error('Rapor oluşturulamadı:', error)
    alert('Rapor oluşturulamadı. Lütfen tekrar deneyin.')
  } finally {
    loading.value = false
  }
}

const exportReport = async () => {
  try {
    const response = await axios.get(`/api/admin/reports/${selectedReportType.value}/export`, {
      params: filters.value,
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `${selectedReportType.value}_report_${Date.now()}.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Rapor dışa aktarılamadı:', error)
    alert('Rapor dışa aktarılamadı.')
  }
}

const renderCharts = () => {
  if (selectedReportType.value === 'sales' && reportData.value?.sales_trend && salesChart.value) {
    if (salesChartInstance) salesChartInstance.destroy()
    
    salesChartInstance = new Chart(salesChart.value, {
      type: 'line',
      data: {
        labels: reportData.value.sales_trend.map(d => d.date),
        datasets: [{
          label: 'Satış (₺)',
          data: reportData.value.sales_trend.map(d => d.amount),
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        }
      }
    })
  }
  
  if (selectedReportType.value === 'product' && reportData.value?.category_performance && categoryChart.value) {
    if (categoryChartInstance) categoryChartInstance.destroy()
    
    categoryChartInstance = new Chart(categoryChart.value, {
      type: 'bar',
      data: {
        labels: reportData.value.category_performance.map(c => c.name),
        datasets: [{
          label: 'Satış (₺)',
          data: reportData.value.category_performance.map(c => c.sales),
          backgroundColor: 'rgba(147, 51, 234, 0.6)'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        }
      }
    })
  }
}

const formatMoney = (value) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value || 0)
}

const loadVendors = async () => {
  try {
    const { data } = await axios.get('/api/vendors')
    vendors.value = data.vendors || data
  } catch (error) {
    console.error('Satıcılar yüklenemedi:', error)
  }
}

const loadCategories = async () => {
  try {
    const { data } = await axios.get('/api/categories')
    categories.value = data.categories || data
  } catch (error) {
    console.error('Kategoriler yüklenemedi:', error)
  }
}

watch(selectedReportType, () => {
  reportData.value = null
  if (salesChartInstance) salesChartInstance.destroy()
  if (categoryChartInstance) categoryChartInstance.destroy()
})

onMounted(() => {
  loadVendors()
  loadCategories()
})
</script>
