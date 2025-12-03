<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4">🚨 Olağan Dışı Davranış Analizi</h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button @click="fetchAnomalies" class="btn btn-error">Analiz Et</button>
    </div>

    <canvas ref="anomalyChart" height="120"></canvas>

    <table class="table table-zebra w-full mt-6 text-sm">
      <thead>
        <tr>
          <th>Tarih</th>
          <th>Metik</th>
          <th>Değer</th>
          <th>Beklenen</th>
          <th>Fark (%)</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in anomalies" :key="item.date + item.metric">
          <td>{{ item.date }}</td>
          <td>{{ item.metric }}</td>
          <td>{{ item.actual }}</td>
          <td>{{ item.expected }}</td>
          <td :class="diffClass(item.diff)">
            {{ item.diff }}%
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'
import Chart from 'chart.js/auto'

type Campaign = { id: number; name: string }
type AnomalyItem = {
  date: string
  metric: string
  actual: number
  expected: number
  diff: number
}

const campaigns = ref<Campaign[]>([])
const selectedId = ref<number | null>(null)
const anomalies = ref<AnomalyItem[]>([])
const anomalyChart = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const fetchCampaigns = async () => {
  const { data } = await axios.get('/admin/campaign-list')
  campaigns.value = data
}

const fetchAnomalies = async () => {
  if (!selectedId.value) return
  const { data } = await axios.get('/admin/campaign-anomaly', {
    params: { campaignId: selectedId.value }
  })
  anomalies.value = data
  renderChart()
}

const renderChart = () => {
  const labels = anomalies.value.map(item => item.date)
  const actuals = anomalies.value.map(item => item.actual)
  const expecteds = anomalies.value.map(item => item.expected)

  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(anomalyChart.value!, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Gerçek Değer',
          data: actuals,
          borderColor: '#EF4444',
          backgroundColor: 'rgba(239,68,68,0.1)',
          fill: true,
          tension: 0.3
        },
        {
          label: 'Beklenen Değer',
          data: expecteds,
          borderColor: '#9CA3AF',
          backgroundColor: 'rgba(156,163,175,0.1)',
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
          title: { display: true, text: 'Değer' }
        },
        x: {
          title: { display: true, text: 'Tarih' }
        }
      }
    }
  })
}

const diffClass = (diff: number) => {
  return diff > 20
    ? 'text-red-600 font-bold'
    : diff < -20
    ? 'text-blue-600 font-bold'
    : 'text-gray-600'
}

onMounted(fetchCampaigns)
</script>

<style scoped>
canvas {
  max-width: 100%;
}
</style>