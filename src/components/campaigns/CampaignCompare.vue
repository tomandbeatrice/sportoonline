<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4">⚖️ Kampanya Karşılaştırması</h2>

    <div class="flex gap-4 mb-6">
      <select v-model="firstId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <select v-model="secondId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button @click="compare" class="btn btn-primary">Karşılaştır</button>
    </div>

    <canvas ref="compareChart" height="120"></canvas>

    <table class="table table-zebra w-full mt-6 text-sm">
      <thead>
        <tr>
          <th>Metik</th>
          <th>{{ first?.name || 'Kampanya 1' }}</th>
          <th>{{ second?.name || 'Kampanya 2' }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="metric in metrics" :key="metric.key">
          <td>{{ metric.label }}</td>
          <td>{{ first?.[metric.key] ?? '-' }}</td>
          <td>{{ second?.[metric.key] ?? '-' }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'
import Chart from 'chart.js/auto'

type Campaign = {
  id: number
  name: string
  score: number
  conversion_rate: number
  clicks: number
  avg_duration: number
}

const campaigns = ref<Campaign[]>([])
const firstId = ref<number | null>(null)
const secondId = ref<number | null>(null)
const first = ref<Campaign | null>(null)
const second = ref<Campaign | null>(null)
const compareChart = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const metrics = [
  { key: 'score', label: 'Skor' },
  { key: 'conversion_rate', label: 'Dönüşüm %' },
  { key: 'clicks', label: 'Tıklama' },
  { key: 'avg_duration', label: 'Ortalama Süre (s)' }
]

const fetchCampaigns = async () => {
  const { data } = await axios.get('/admin/campaign-list')
  campaigns.value = data
}

const compare = async () => {
  if (!firstId.value || !secondId.value) return

  const { data } = await axios.get('/admin/campaign-compare', {
    params: { first: firstId.value, second: secondId.value }
  })

  first.value = data.first
  second.value = data.second
  renderChart()
}

const renderChart = () => {
  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(compareChart.value!, {
    type: 'bar',
    data: {
      labels: metrics.map(m => m.label),
      datasets: [
        {
          label: first.value?.name || 'Kampanya 1',
          data: metrics.map(m => first.value?.[m.key] ?? 0),
          backgroundColor: '#3B82F6'
        },
        {
          label: second.value?.name || 'Kampanya 2',
          data: metrics.map(m => second.value?.[m.key] ?? 0),
          backgroundColor: '#10B981'
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      },
      scales: {
        y: { beginAtZero: true }
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