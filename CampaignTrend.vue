<!-- src/views/CampaignTrend.vue -->
<template>
  <section class="py-8 px-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-2xl font-bold mb-4">📊 Kampanya Dönüşüm Trendleri</h2>

    <div class="mb-6 flex gap-4 justify-center items-center">
      <select v-model="range" class="border px-3 py-2 rounded">
        <option value="7">Son 7 Gün</option>
        <option value="30">Son 30 Gün</option>
        <option value="90">Son 90 Gün</option>
      </select>

      <button
        @click="downloadCSV"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
      >
        📥 CSV Olarak İndir
      </button>
    </div>

    <canvas ref="trendChart" height="300"></canvas>
  </section>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import axios from '@/utils/axios'
import Chart from 'chart.js/auto'

const range = ref(30)
const trendChart = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const fetchData = async () => {
  try {
    const { data } = await axios.get(`/api/admin/campaign-trend?days=${range.value}`)

    const labels = data.map((item: any) => item.date)
    const earlyAccess = data.map((item: any) => item.early_access_100)
    const betaInvite = data.map((item: any) => item.beta_invite)
    const organic = data.map((item: any) => item.organic)

    if (chartInstance) chartInstance.destroy()

    chartInstance = new Chart(trendChart.value!, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: 'SPORTOON100',
            data: earlyAccess,
            borderColor: '#10B981',
            backgroundColor: 'rgba(16,185,129,0.1)',
            fill: true,
            tension: 0.3
          },
          {
            label: 'BETA50',
            data: betaInvite,
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59,130,246,0.1)',
            fill: true,
            tension: 0.3
          },
          {
            label: 'Organik',
            data: organic,
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
            title: {
              display: true,
              text: 'Dönüşüm Sayısı'
            }
          },
          x: {
            title: {
              display: true,
              text: 'Tarih'
            }
          }
        }
      }
    })
  } catch (err) {
    console.error('Trend verisi alınamadı:', err)
  }
}

const downloadCSV = () => {
  window.open(`/api/admin/export-campaign-trend?days=${range.value}`, '_blank')
}

onMounted(fetchData)
watch(range, fetchData)
</script>

<style scoped>
canvas {
  max-width: 100%;
}
</style>