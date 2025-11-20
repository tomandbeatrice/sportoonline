<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4">📉 Kampanya Terk Oranları</h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button @click="fetchDropData" class="btn btn-error">Analiz Et</button>
    </div>

    <canvas ref="dropChart" height="120"></canvas>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'
import Chart from 'chart.js/auto'

type Campaign = { id: number; name: string }
type DropPoint = { step: string; rate: number }

const campaigns = ref<Campaign[]>([])
const selectedId = ref<number | null>(null)
const dropChart = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const fetchCampaigns = async () => {
  const { data } = await axios.get('/admin/campaign-list')
  campaigns.value = data
}

const fetchDropData = async () => {
  if (!selectedId.value) return
  const { data } = await axios.get('/admin/campaign-drop', {
    params: { campaignId: selectedId.value }
  })

  const labels = data.map((item: DropPoint) => item.step)
  const values = data.map((item: DropPoint) => item.rate)

  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(dropChart.value!, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Terk Oranı %',
        data: values,
        backgroundColor: '#EF4444',
        borderRadius: 4
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
          title: { display: true, text: 'Terk Oranı (%)' }
        },
        x: {
          title: { display: true, text: 'Adım' }
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