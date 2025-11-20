<template>
  <div class="reports-analytics">
    <!-- Header -->
    <div class="header">
      <div>
        <h1>📊 Raporlama & Analitik</h1>
        <p class="subtitle">Detaylı satış ve performans raporları</p>
      </div>
      <div class="header-actions">
        <button @click="exportReport('excel')" class="btn btn-success">
          📥 Excel İndir
        </button>
        <button @click="exportReport('pdf')" class="btn btn-secondary">
          📄 PDF İndir
        </button>
      </div>
    </div>

    <!-- Period Selector -->
    <div class="period-selector">
      <button 
        v-for="period in periods" 
        :key="period.value"
        @click="selectPeriod(period.value)"
        :class="['period-btn', { active: selectedPeriod === period.value }]"
      >
        {{ period.label }}
      </button>
      <div class="custom-range">
        <input v-model="customRange.start" type="date" />
        <span>→</span>
        <input v-model="customRange.end" type="date" />
        <button @click="applyCustomRange" class="btn btn-sm btn-primary">Uygula</button>
      </div>
    </div>

    <!-- Key Metrics -->
    <div class="metrics-grid">
      <div class="metric-card">
        <div class="metric-header">
          <span class="metric-icon">💰</span>
          <span class="metric-label">Toplam Gelir</span>
        </div>
        <div class="metric-value">{{ formatCurrency(metrics.totalRevenue) }}</div>
        <div class="metric-change" :class="metrics.revenueChange >= 0 ? 'positive' : 'negative'">
          <span>{{ metrics.revenueChange >= 0 ? '↗' : '↘' }}</span>
          {{ Math.abs(metrics.revenueChange) }}% önceki döneme göre
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <span class="metric-icon">📦</span>
          <span class="metric-label">Toplam Sipariş</span>
        </div>
        <div class="metric-value">{{ metrics.totalOrders.toLocaleString('tr-TR') }}</div>
        <div class="metric-change" :class="metrics.ordersChange >= 0 ? 'positive' : 'negative'">
          <span>{{ metrics.ordersChange >= 0 ? '↗' : '↘' }}</span>
          {{ Math.abs(metrics.ordersChange) }}% önceki döneme göre
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <span class="metric-icon">🛒</span>
          <span class="metric-label">Ortalama Sepet</span>
        </div>
        <div class="metric-value">{{ formatCurrency(metrics.avgOrderValue) }}</div>
        <div class="metric-change" :class="metrics.avgChange >= 0 ? 'positive' : 'negative'">
          <span>{{ metrics.avgChange >= 0 ? '↗' : '↘' }}</span>
          {{ Math.abs(metrics.avgChange) }}% önceki döneme göre
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <span class="metric-icon">📈</span>
          <span class="metric-label">Dönüşüm Oranı</span>
        </div>
        <div class="metric-value">{{ metrics.conversionRate.toFixed(2) }}%</div>
        <div class="metric-change" :class="metrics.conversionChange >= 0 ? 'positive' : 'negative'">
          <span>{{ metrics.conversionChange >= 0 ? '↗' : '↘' }}</span>
          {{ Math.abs(metrics.conversionChange) }}% önceki döneme göre
        </div>
      </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="charts-row">
      <div class="chart-card">
        <div class="chart-header">
          <h3>💹 Gelir Trendi</h3>
          <select v-model="revenueChartType" class="chart-type-selector">
            <option value="line">Çizgi</option>
            <option value="bar">Bar</option>
            <option value="area">Alan</option>
          </select>
        </div>
        <div class="chart-container">
          <canvas ref="revenueChart"></canvas>
        </div>
      </div>

      <div class="chart-card">
        <div class="chart-header">
          <h3>📊 Sipariş Durumu</h3>
        </div>
        <div class="chart-container">
          <canvas ref="orderStatusChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="charts-row">
      <div class="chart-card">
        <div class="chart-header">
          <h3>🏆 En Çok Satan Kategoriler</h3>
        </div>
        <div class="chart-container">
          <canvas ref="categoryChart"></canvas>
        </div>
      </div>

      <div class="chart-card">
        <div class="chart-header">
          <h3>⏰ Saatlik Satış Dağılımı</h3>
        </div>
        <div class="chart-container">
          <canvas ref="hourlyChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Top Performers -->
    <div class="performers-section">
      <div class="performers-card">
        <h3>🏅 En Çok Satan Ürünler</h3>
        <div class="performers-list">
          <div v-for="(product, index) in topProducts" :key="product.id" class="performer-item">
            <div class="rank">{{ index + 1 }}</div>
            <div class="performer-info">
              <strong>{{ product.name }}</strong>
              <small>{{ product.category }}</small>
            </div>
            <div class="performer-stats">
              <div class="stat">
                <label>Satış</label>
                <strong>{{ product.sales }}</strong>
              </div>
              <div class="stat">
                <label>Gelir</label>
                <strong>{{ formatCurrency(product.revenue) }}</strong>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="performers-card">
        <h3>👥 En Çok Satış Yapan Satıcılar</h3>
        <div class="performers-list">
          <div v-for="(seller, index) in topSellers" :key="seller.id" class="performer-item">
            <div class="rank">{{ index + 1 }}</div>
            <div class="performer-info">
              <strong>{{ seller.name }}</strong>
              <small>{{ seller.email }}</small>
            </div>
            <div class="performer-stats">
              <div class="stat">
                <label>Sipariş</label>
                <strong>{{ seller.orders }}</strong>
              </div>
              <div class="stat">
                <label>Gelir</label>
                <strong>{{ formatCurrency(seller.revenue) }}</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Tables -->
    <div class="tables-section">
      <div class="table-card">
        <div class="table-header">
          <h3>📅 Günlük Detay</h3>
          <div class="table-actions">
            <input v-model="tableSearch" type="text" placeholder="Ara..." />
          </div>
        </div>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Tarih</th>
                <th>Sipariş</th>
                <th>Gelir</th>
                <th>Ort. Sepet</th>
                <th>Müşteri</th>
                <th>Trend</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="day in dailyData" :key="day.date">
                <td>{{ formatDate(day.date) }}</td>
                <td>{{ day.orders }}</td>
                <td>{{ formatCurrency(day.revenue) }}</td>
                <td>{{ formatCurrency(day.avgOrder) }}</td>
                <td>{{ day.customers }}</td>
                <td>
                  <span class="trend" :class="day.trend >= 0 ? 'up' : 'down'">
                    {{ day.trend >= 0 ? '↗' : '↘' }} {{ Math.abs(day.trend) }}%
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
      <div class="summary-card">
        <h4>💳 Ödeme Yöntemleri</h4>
        <div class="summary-list">
          <div v-for="payment in paymentMethods" :key="payment.method" class="summary-item">
            <span class="summary-label">{{ payment.method }}</span>
            <span class="summary-value">{{ formatCurrency(payment.amount) }}</span>
            <span class="summary-percent">{{ payment.percent }}%</span>
          </div>
        </div>
      </div>

      <div class="summary-card">
        <h4>🚚 Kargo Durumu</h4>
        <div class="summary-list">
          <div v-for="shipping in shippingStatus" :key="shipping.status" class="summary-item">
            <span class="summary-label">{{ shipping.status }}</span>
            <span class="summary-value">{{ shipping.count }} sipariş</span>
            <span class="summary-percent">{{ shipping.percent }}%</span>
          </div>
        </div>
      </div>

      <div class="summary-card">
        <h4>🌍 Şehir Dağılımı</h4>
        <div class="summary-list">
          <div v-for="city in topCities" :key="city.name" class="summary-item">
            <span class="summary-label">{{ city.name }}</span>
            <span class="summary-value">{{ formatCurrency(city.revenue) }}</span>
            <span class="summary-percent">{{ city.percent }}%</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, watch, nextTick } from 'vue'
import axios from 'axios'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

interface Metrics {
  totalRevenue: number
  totalOrders: number
  avgOrderValue: number
  conversionRate: number
  revenueChange: number
  ordersChange: number
  avgChange: number
  conversionChange: number
}

const selectedPeriod = ref('7days')
const revenueChartType = ref('line')
const tableSearch = ref('')
const customRange = reactive({
  start: '',
  end: ''
})

const periods = [
  { label: 'Bugün', value: 'today' },
  { label: 'Dün', value: 'yesterday' },
  { label: 'Son 7 Gün', value: '7days' },
  { label: 'Son 30 Gün', value: '30days' },
  { label: 'Bu Ay', value: 'thisMonth' },
  { label: 'Geçen Ay', value: 'lastMonth' },
  { label: 'Bu Yıl', value: 'thisYear' }
]

const metrics = ref<Metrics>({
  totalRevenue: 0,
  totalOrders: 0,
  avgOrderValue: 0,
  conversionRate: 0,
  revenueChange: 0,
  ordersChange: 0,
  avgChange: 0,
  conversionChange: 0
})

const topProducts = ref([])
const topSellers = ref([])
const dailyData = ref([])
const paymentMethods = ref([])
const shippingStatus = ref([])
const topCities = ref([])

const revenueChart = ref<HTMLCanvasElement>()
const orderStatusChart = ref<HTMLCanvasElement>()
const categoryChart = ref<HTMLCanvasElement>()
const hourlyChart = ref<HTMLCanvasElement>()

let revenueChartInstance: Chart | null = null
let orderStatusChartInstance: Chart | null = null
let categoryChartInstance: Chart | null = null
let hourlyChartInstance: Chart | null = null

onMounted(() => {
  loadData()
})

watch(selectedPeriod, () => {
  loadData()
})

watch(revenueChartType, () => {
  if (revenueChartInstance) {
    updateRevenueChart()
  }
})

const selectPeriod = (period: string) => {
  selectedPeriod.value = period
}

const applyCustomRange = () => {
  if (customRange.start && customRange.end) {
    loadData()
  }
}

const loadData = async () => {
  try {
    const params: any = { period: selectedPeriod.value }
    if (customRange.start && customRange.end) {
      params.start_date = customRange.start
      params.end_date = customRange.end
    }

    const [metricsRes, productsRes, sellersRes, dailyRes, summaryRes] = await Promise.all([
      axios.get('/api/admin/reports/metrics', { params }),
      axios.get('/api/admin/reports/top-products', { params }),
      axios.get('/api/admin/reports/top-sellers', { params }),
      axios.get('/api/admin/reports/daily', { params }),
      axios.get('/api/admin/reports/summary', { params })
    ])

    metrics.value = metricsRes.data
    topProducts.value = productsRes.data
    topSellers.value = sellersRes.data
    dailyData.value = dailyRes.data
    
    paymentMethods.value = summaryRes.data.paymentMethods
    shippingStatus.value = summaryRes.data.shippingStatus
    topCities.value = summaryRes.data.topCities

    await nextTick()
    initCharts()
  } catch (error) {
    console.error('Veri yükleme hatası:', error)
  }
}

const initCharts = () => {
  createRevenueChart()
  createOrderStatusChart()
  createCategoryChart()
  createHourlyChart()
}

const createRevenueChart = () => {
  if (!revenueChart.value) return

  if (revenueChartInstance) {
    revenueChartInstance.destroy()
  }

  const ctx = revenueChart.value.getContext('2d')
  if (!ctx) return

  const labels = dailyData.value.map((d: any) => formatDate(d.date))
  const data = dailyData.value.map((d: any) => d.revenue)

  revenueChartInstance = new Chart(ctx, {
    type: revenueChartType.value as any,
    data: {
      labels,
      datasets: [{
        label: 'Gelir (₺)',
        data,
        backgroundColor: 'rgba(37, 99, 235, 0.1)',
        borderColor: 'rgb(37, 99, 235)',
        borderWidth: 2,
        fill: revenueChartType.value === 'area',
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: (value) => formatCurrency(value as number)
          }
        }
      }
    }
  })
}

const updateRevenueChart = () => {
  createRevenueChart()
}

const createOrderStatusChart = () => {
  if (!orderStatusChart.value) return

  if (orderStatusChartInstance) {
    orderStatusChartInstance.destroy()
  }

  const ctx = orderStatusChart.value.getContext('2d')
  if (!ctx) return

  orderStatusChartInstance = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Tamamlandı', 'İşleniyor', 'Kargoda', 'Beklemede', 'İptal'],
      datasets: [{
        data: [45, 25, 15, 10, 5],
        backgroundColor: [
          'rgb(34, 197, 94)',
          'rgb(59, 130, 246)',
          'rgb(251, 146, 60)',
          'rgb(234, 179, 8)',
          'rgb(239, 68, 68)'
        ]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  })
}

const createCategoryChart = () => {
  if (!categoryChart.value) return

  if (categoryChartInstance) {
    categoryChartInstance.destroy()
  }

  const ctx = categoryChart.value.getContext('2d')
  if (!ctx) return

  categoryChartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Elektronik', 'Giyim', 'Ev & Yaşam', 'Spor', 'Kitap'],
      datasets: [{
        label: 'Satış Adedi',
        data: [120, 95, 78, 65, 45],
        backgroundColor: 'rgba(37, 99, 235, 0.8)'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: 'y',
      plugins: {
        legend: { display: false }
      }
    }
  })
}

const createHourlyChart = () => {
  if (!hourlyChart.value) return

  if (hourlyChartInstance) {
    hourlyChartInstance.destroy()
  }

  const ctx = hourlyChart.value.getContext('2d')
  if (!ctx) return

  const hours = Array.from({ length: 24 }, (_, i) => `${i}:00`)
  const sales = [5, 3, 2, 1, 2, 8, 15, 25, 35, 42, 48, 52, 55, 58, 60, 62, 65, 70, 68, 55, 45, 32, 20, 10]

  hourlyChartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: hours,
      datasets: [{
        label: 'Sipariş Sayısı',
        data: sales,
        backgroundColor: 'rgba(34, 197, 94, 0.1)',
        borderColor: 'rgb(34, 197, 94)',
        borderWidth: 2,
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  })
}

const exportReport = async (format: 'excel' | 'pdf') => {
  try {
    const params: any = { 
      period: selectedPeriod.value,
      format 
    }
    if (customRange.start && customRange.end) {
      params.start_date = customRange.start
      params.end_date = customRange.end
    }

    const response = await axios.get('/api/admin/reports/export', {
      params,
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `rapor-${Date.now()}.${format === 'excel' ? 'xlsx' : 'pdf'}`)
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Export hatası:', error)
    alert('Rapor indirilirken bir hata oluştu')
  }
}

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY',
    minimumFractionDigits: 0
  }).format(value)
}

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('tr-TR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}
</script>

<style scoped>
.reports-analytics {
  padding: 24px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.header h1 {
  font-size: 28px;
  font-weight: 600;
  margin: 0 0 4px 0;
}

.subtitle {
  color: #666;
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.period-selector {
  display: flex;
  gap: 8px;
  align-items: center;
  margin-bottom: 24px;
  padding: 16px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  flex-wrap: wrap;
}

.period-btn {
  padding: 8px 16px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.period-btn:hover {
  background: #f3f4f6;
}

.period-btn.active {
  background: #2563eb;
  color: white;
  border-color: #2563eb;
}

.custom-range {
  display: flex;
  gap: 8px;
  align-items: center;
  margin-left: auto;
}

.custom-range input {
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.metric-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
}

.metric-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}

.metric-icon {
  font-size: 24px;
}

.metric-label {
  font-size: 14px;
  color: #6b7280;
  font-weight: 500;
}

.metric-value {
  font-size: 32px;
  font-weight: 700;
  color: #111;
  margin-bottom: 8px;
}

.metric-change {
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 4px;
}

.metric-change.positive {
  color: #059669;
}

.metric-change.negative {
  color: #dc2626;
}

.charts-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.chart-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.chart-header h3 {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
}

.chart-type-selector {
  padding: 6px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 13px;
}

.chart-container {
  height: 300px;
}

.performers-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.performers-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
}

.performers-card h3 {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 16px 0;
}

.performers-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.performer-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px;
  background: #f9fafb;
  border-radius: 6px;
}

.rank {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #2563eb;
  color: white;
  border-radius: 50%;
  font-weight: 600;
  font-size: 14px;
}

.performer-info {
  flex: 1;
}

.performer-info strong {
  display: block;
  margin-bottom: 4px;
}

.performer-info small {
  color: #6b7280;
  font-size: 12px;
}

.performer-stats {
  display: flex;
  gap: 24px;
}

.performer-stats .stat {
  text-align: right;
}

.performer-stats .stat label {
  display: block;
  font-size: 11px;
  color: #6b7280;
  margin-bottom: 2px;
}

.performer-stats .stat strong {
  font-size: 14px;
}

.tables-section {
  margin-bottom: 24px;
}

.table-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid #e5e7eb;
}

.table-header h3 {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
}

.table-actions input {
  padding: 6px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 13px;
}

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f9fafb;
}

th {
  padding: 12px 16px;
  text-align: left;
  font-weight: 600;
  font-size: 13px;
  color: #374151;
}

td {
  padding: 12px 16px;
  border-top: 1px solid #f3f4f6;
}

tbody tr:hover {
  background: #f9fafb;
}

.trend {
  font-size: 13px;
  font-weight: 500;
}

.trend.up {
  color: #059669;
}

.trend.down {
  color: #dc2626;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 16px;
}

.summary-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
}

.summary-card h4 {
  font-size: 15px;
  font-weight: 600;
  margin: 0 0 16px 0;
}

.summary-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.summary-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px;
  background: #f9fafb;
  border-radius: 6px;
}

.summary-label {
  flex: 1;
  font-size: 13px;
  font-weight: 500;
}

.summary-value {
  font-size: 13px;
  font-weight: 600;
}

.summary-percent {
  font-size: 12px;
  color: #6b7280;
  min-width: 40px;
  text-align: right;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-primary {
  background: #2563eb;
  color: white;
}

.btn-success {
  background: #059669;
  color: white;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 13px;
}
</style>
