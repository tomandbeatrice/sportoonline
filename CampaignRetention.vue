<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4">🔁 Kampanya Geri Dönüş Oranları</h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button @click="fetchRetention" class="btn btn-primary">Analiz Et</button>
    </div>

    <canvas ref="retentionChart" height="120"></canvas>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'
import Chart from 'chart.js/auto'

type Campaign = { id: number; name: string }
type RetentionPoint = { day: string; rate: number }

const campaigns = ref<Campaign[]>([])
const selectedId = ref<number | null>(null)
const retentionChart = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const fetchCampaigns = async () => {
  const { data } = await axios.get('/admin/campaign-list')
  campaigns.value = data
}

const fetchRetention = async () => {
  if (!selectedId.value) return
  const { data } = await axios.get('/admin/campaign-retention', {
    params: { campaignId: selectedId.value }
  })

  const labels = data.map((item: RetentionPoint) => item.day)
  const values = data.map((item: RetentionPoint) => item.rate)

  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(retentionChart.value!, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Geri Dönüş %',
        data: values,
        borderColor: '#6366F1',
        backgroundColor: 'rgba(99,102,241,0.1)',
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
          title: { display: true, text: 'Dönüşüm Oranı (%)' }
        },
        x: {
          title: { display: true, text: 'Gün' }
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