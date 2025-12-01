<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
      <BadgeIcon name="trending-up" cls="w-6 h-6 text-green-600" /> Kampanya Tahmin Grafiği
    </h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button @click="fetchForecast" class="btn btn-info">Tahmin Et</button>
    </div>

    <canvas ref="forecastChart" height="120"></canvas>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'
import Chart from 'chart.js/auto'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

type Campaign = { id: number; name: string }
type ForecastPoint = { date: string; actual: number; predicted: number }

const campaigns = ref<Campaign[]>([])
const selectedId = ref<number | null>(null)
const forecastChart = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null
const forecastData = ref<ForecastPoint[]>([])

const fetchCampaigns = async () => {
  const { data } = await axios.get('/admin/campaign-list')
  campaigns.value = data
}

const fetchForecast = async () => {
  if (!selectedId.value) return
  const { data } = await axios.get('/admin/campaign-forecast', {
    params: { campaignId: selectedId.value }
  })
  forecastData.value = data
  renderChart()
}

const renderChart = () => {
  const labels = forecastData.value.map(p => p.date)
  const actuals = forecastData.value.map(p => p.actual)
  const predictions = forecastData.value.map(p => p.predicted)

  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(forecastChart.value!, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Gerçek Skor',
          data: actuals,
          borderColor: '#3B82F6',
          backgroundColor: 'rgba(59,130,246,0.1)',
          fill: true,
          tension: 0.3
        },
        {
          label: 'Tahmin Skoru',
          data: predictions,
          borderColor: '#10B981',
          backgroundColor: 'rgba(16,185,129,0.1)',
          fill: true,
          tension: 0.3
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: { display: true, text: 'Skor' }
        },
        x: {
          title: { display: true, text: 'Tarih' }
        }
      }
    }
  })
}

onMounted(fetchCampaigns)
</script>

<style scoped>
canvas {
  max-width: 100%;
}
</style>