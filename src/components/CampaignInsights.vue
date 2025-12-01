<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
      <BadgeIcon name="bar-chart" class="w-6 h-6 text-blue-600" />
      Kampanya Genel İçgörü Paneli
    </h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button @click="fetchInsights" class="btn btn-primary">Verileri Getir</button>
    </div>

    <div v-if="insights" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-gray-50 p-4 rounded border">
        <h3 class="font-semibold mb-2 flex items-center gap-2">
          <BadgeIcon name="trending-up" class="w-5 h-5 text-green-600" />
          Performans
        </h3>
        <p>Skor: {{ insights.score }}</p>
        <p>Dönüşüm: {{ insights.conversion_rate }}%</p>
        <p>Gelir: ₺{{ insights.revenue }}</p>
      </div>

      <div class="bg-gray-50 p-4 rounded border">
        <h3 class="font-semibold mb-2 flex items-center gap-2">
          <BadgeIcon name="message-circle" class="w-5 h-5 text-purple-600" />
          Geri Bildirim
        </h3>
        <p>Olumlu: {{ insights.feedback.positive }}</p>
        <p>Olumsuz: {{ insights.feedback.negative }}</p>
        <p>Nötr: {{ insights.feedback.neutral }}</p>
      </div>

      <div class="bg-gray-50 p-4 rounded border">
        <h3 class="font-semibold mb-2 flex items-center gap-2">
          <BadgeIcon name="trending-down" class="w-5 h-5 text-red-600" />
          Terk & 
          <BadgeIcon name="refresh-cw" class="w-5 h-5 text-blue-500" />
          Geri Kazanım
        </h3>
        <p>Terk Oranı: {{ insights.drop_rate }}%</p>
        <p>Geri Kazanım: {{ insights.recovery_rate }}%</p>
      </div>

      <div class="bg-gray-50 p-4 rounded border">
        <h3 class="font-semibold mb-2 flex items-center gap-2">
          <BadgeIcon name="cpu" class="w-5 h-5 text-indigo-600" />
          Tahmin & Öneri
        </h3>
        <p>Tahmini Skor: {{ insights.forecast_score }}</p>
        <p>Öneri: {{ insights.optimizer_action }}</p>
      </div>
    </div>

    <canvas ref="insightChart" height="120" class="mt-8"></canvas>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import BadgeIcon from '@/components/BadgeIcon.vue'
import axios from '@/utils/axios'
import Chart from 'chart.js/auto'

type Campaign = { id: number; name: string }
type Insights = {
  score: number
  conversion_rate: number
  revenue: number
  feedback: { positive: number; negative: number; neutral: number }
  drop_rate: number
  recovery_rate: number
  forecast_score: number
  optimizer_action: string
}

const campaigns = ref<Campaign[]>([])
const selectedId = ref<number | null>(null)
const insights = ref<Insights | null>(null)
const insightChart = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const fetchCampaigns = async () => {
  const { data } = await axios.get('/admin/campaign-list')
  campaigns.value = data
}

const fetchInsights = async () => {
  if (!selectedId.value) return
  const { data } = await axios.get('/admin/campaign-insights', {
    params: { campaignId: selectedId.value }
  })
  insights.value = data
  renderChart()
}

const renderChart = () => {
  if (!insights.value) return

  const labels = ['Skor', 'Dönüşüm', 'Gelir', 'Terk', 'Geri Kazanım', 'Tahmin']
  const values = [
    insights.value.score,
    insights.value.conversion_rate,
    insights.value.revenue,
    insights.value.drop_rate,
    insights.value.recovery_rate,
    insights.value.forecast_score
  ]

  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(insightChart.value!, {
    type: 'radar',
    data: {
      labels,
      datasets: [{
        label: 'Genel Kampanya Skoru',
        data: values,
        backgroundColor: 'rgba(59,130,246,0.2)',
        borderColor: '#3B82F6',
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
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