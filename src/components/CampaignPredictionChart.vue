<template>
  <div class="campaign-prediction-chart">
    <div class="chart-header">
      <h4><i class="fas fa-crystal-ball"></i> {{ days }} Günlük Performans Tahmini</h4>
      <div class="controls">
        <select v-model.number="days" @change="loadPrediction" class="days-select">
          <option :value="3">3 Gün</option>
          <option :value="7">7 Gün</option>
          <option :value="14">14 Gün</option>
          <option :value="30">30 Gün</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
    </div>

    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
    </div>

    <div v-else-if="prediction" class="chart-content">
      <!-- Summary Cards -->
      <div class="summary-cards">
        <div class="summary-card">
          <i class="fas fa-chart-line"></i>
          <div class="summary-data">
            <span class="summary-label">Büyüme Oranı</span>
            <span class="summary-value" :class="growthClass">
              {{ (prediction.growth_rate * 100).toFixed(2) }}%
            </span>
          </div>
        </div>
        <div class="summary-card">
          <i class="fas fa-bullseye"></i>
          <div class="summary-data">
            <span class="summary-label">Ortalama Güven</span>
            <span class="summary-value">{{ avgConfidence.toFixed(0) }}%</span>
          </div>
        </div>
      </div>

      <!-- Prediction Chart -->
      <div class="chart-container">
        <canvas ref="chartCanvas"></canvas>
      </div>

      <!-- Prediction Table -->
      <div class="prediction-table">
        <table>
          <thead>
            <tr>
              <th>Gün</th>
              <th>Tahmini Dönüşüm</th>
              <th>Tahmini Gelir</th>
              <th>Tahmini Katılım</th>
              <th>Güven Skoru</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(pred, idx) in prediction.predictions" :key="idx">
              <td>{{ idx + 1 }}. Gün</td>
              <td>{{ pred.predicted_conversion_rate }}%</td>
              <td>{{ formatCurrency(pred.predicted_revenue) }}</td>
              <td>{{ pred.predicted_engagement_rate }}%</td>
              <td>
                <div class="confidence-bar">
                  <div class="confidence-fill" :style="{ width: `${pred.confidence}%` }"></div>
                  <span>{{ pred.confidence }}%</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import axios from 'axios'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

interface Props {
  campaignId: number
}

const props = defineProps<Props>()

interface Prediction {
  predictions: Array<{
    day: number
    predicted_conversion_rate: number
    predicted_revenue: number
    predicted_engagement_rate: number
    confidence: number
  }>
  growth_rate: number
  current_metrics: any
}

const loading = ref(false)
const error = ref<string | null>(null)
const prediction = ref<Prediction | null>(null)
const days = ref(7)
const chartCanvas = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const avgConfidence = computed(() => {
  if (!prediction.value) return 0
  const total = prediction.value.predictions.reduce((sum, p) => sum + p.confidence, 0)
  return total / prediction.value.predictions.length
})

const growthClass = computed(() => {
  if (!prediction.value) return ''
  const rate = prediction.value.growth_rate
  if (rate > 0.1) return 'positive-high'
  if (rate > 0) return 'positive'
  if (rate > -0.1) return 'neutral'
  return 'negative'
})

const loadPrediction = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get(
      `/api/admin/campaign-ai/predict/${props.campaignId}`,
      { params: { forecast_days: days.value } }
    )
    prediction.value = response.data
    await nextTick()
    renderChart()
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Tahmin yüklenemedi'
  } finally {
    loading.value = false
  }
}

const renderChart = () => {
  if (!chartCanvas.value || !prediction.value) return

  if (chartInstance) {
    chartInstance.destroy()
  }

  const ctx = chartCanvas.value.getContext('2d')
  if (!ctx) return

  const labels = prediction.value.predictions.map((_, idx) => `Gün ${idx + 1}`)
  const conversionData = prediction.value.predictions.map(p => p.predicted_conversion_rate)
  const revenueData = prediction.value.predictions.map(p => p.predicted_revenue)
  const confidenceData = prediction.value.predictions.map(p => p.confidence)

  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Tahmini Dönüşüm (%)',
          data: conversionData,
          borderColor: '#3498db',
          backgroundColor: 'rgba(52, 152, 219, 0.1)',
          yAxisID: 'y',
          tension: 0.4
        },
        {
          label: 'Tahmini Gelir (₺)',
          data: revenueData,
          borderColor: '#27ae60',
          backgroundColor: 'rgba(39, 174, 96, 0.1)',
          yAxisID: 'y1',
          tension: 0.4
        },
        {
          label: 'Güven Skoru (%)',
          data: confidenceData,
          borderColor: '#f39c12',
          backgroundColor: 'rgba(243, 156, 18, 0.1)',
          borderDash: [5, 5],
          yAxisID: 'y',
          tension: 0.4
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: 'index',
        intersect: false
      },
      plugins: {
        legend: {
          position: 'top'
        },
        tooltip: {
          callbacks: {
            label: (context) => {
              let label = context.dataset.label || ''
              if (label) label += ': '
              if (context.dataset.yAxisID === 'y1') {
                label += formatCurrency(context.parsed.y)
              } else {
                label += context.parsed.y.toFixed(2)
                if (!label.includes('Güven')) label += '%'
              }
              return label
            }
          }
        }
      },
      scales: {
        y: {
          type: 'linear',
          display: true,
          position: 'left',
          title: {
            display: true,
            text: 'Dönüşüm & Güven (%)'
          }
        },
        y1: {
          type: 'linear',
          display: true,
          position: 'right',
          title: {
            display: true,
            text: 'Gelir (₺)'
          },
          grid: {
            drawOnChartArea: false
          }
        }
      }
    }
  })
}

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY'
  }).format(value)
}

watch(() => props.campaignId, () => {
  loadPrediction()
})

onMounted(() => {
  loadPrediction()
})
</script>

<style scoped>
.campaign-prediction-chart {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.chart-header h4 {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
  font-size: 1.25rem;
}

.days-select {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  background: white;
  cursor: pointer;
}

.loading-state {
  text-align: center;
  padding: 3rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-state {
  text-align: center;
  padding: 2rem;
  color: #e74c3c;
}

.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.summary-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
}

.summary-card i {
  font-size: 2rem;
  color: #3498db;
}

.summary-data {
  display: flex;
  flex-direction: column;
}

.summary-label {
  font-size: 0.875rem;
  color: #666;
  margin-bottom: 0.25rem;
}

.summary-value {
  font-size: 1.5rem;
  font-weight: 700;
}

.positive-high { color: #27ae60; }
.positive { color: #2ecc71; }
.neutral { color: #95a5a6; }
.negative { color: #e74c3c; }

.chart-container {
  height: 400px;
  margin-bottom: 2rem;
}

.prediction-table {
  overflow-x: auto;
}

.prediction-table table {
  width: 100%;
  border-collapse: collapse;
}

.prediction-table th,
.prediction-table td {
  padding: 0.75rem;
  text-align: left;
  border-bottom: 1px solid #e0e0e0;
}

.prediction-table th {
  background: #f8f9fa;
  font-weight: 600;
  color: #2c3e50;
}

.prediction-table tbody tr:hover {
  background: #f8f9fa;
}

.confidence-bar {
  position: relative;
  width: 100px;
  height: 20px;
  background: #e0e0e0;
  border-radius: 10px;
  overflow: hidden;
}

.confidence-fill {
  position: absolute;
  height: 100%;
  background: linear-gradient(90deg, #3498db, #2ecc71);
  transition: width 0.3s;
}

.confidence-bar span {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 0.75rem;
  font-weight: 600;
  color: #2c3e50;
}
</style>
