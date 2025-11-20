<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4">📆 Kampanya Yaşam Döngüsü</h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button @click="fetchLifecycle" class="btn btn-accent">Analiz Et</button>
    </div>

    <canvas ref="lifecycleChart" height="120"></canvas>

    <ul class="timeline mt-8">
      <li v-for="step in lifecycle" :key="step.stage">
        <div class="timeline-start">{{ step.date }}</div>
        <div class="timeline-middle"></div>
        <div class="timeline-end">{{ step.stage }} → {{ step.description }}</div>
      </li>
    </ul>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'
import Chart from 'chart.js/auto'

type Campaign = { id: number; name: string }
type LifecycleStep = { stage: string; date: string; description: string; value: number }

const campaigns = ref<Campaign[]>([])
const selectedId = ref<number | null>(null)
const lifecycle = ref<LifecycleStep[]>([])
const lifecycleChart = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const fetchCampaigns = async () => {
  const { data } = await axios.get('/admin/campaign-list')
  campaigns.value = data
}

const fetchLifecycle = async () => {
  if (!selectedId.value) return
  const { data } = await axios.get('/admin/campaign-lifecycle', {
    params: { campaignId: selectedId.value }
  })
  lifecycle.value = data
  renderChart()
}

const renderChart = () => {
  const labels = lifecycle.value.map(item => item.stage)
  const values = lifecycle.value.map(item => item.value)

  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(lifecycleChart.value!, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Etki Skoru',
        data: values,
        borderColor: '#F59E0B',
        backgroundColor: 'rgba(245,158,11,0.1)',
        fill: true,
        tension: 0.3
      }]
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
          title: { display: true, text: 'Aşama' }
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
.timeline {
  border-left: 2px solid #ccc;
  padding-left: 1rem;
}
.timeline li {
  margin-bottom: 1rem;
}
.timeline-start {
  font-weight: bold;
  color: #3B82F6;
}
.timeline-end {
  color: #374151;
}
</style>