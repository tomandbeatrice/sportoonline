<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4">🔄 Geri Kazanım Analizi</h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button @click="fetchRecoveryData" class="btn btn-success">Analiz Et</button>
    </div>

    <canvas ref="recoveryChart" height="120"></canvas>

    <table class="table table-zebra w-full mt-6 text-sm">
      <thead>
        <tr>
          <th>Aksiyon</th>
          <th>Geri Dönüş %</th>
          <th>Uygulama Sayısı</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in recoveryData" :key="item.action">
          <td>{{ item.action }}</td>
          <td>{{ item.rate }}%</td>
          <td>{{ item.count }}</td>
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
type RecoveryItem = { action: string; rate: number; count: number }

const campaigns = ref<Campaign[]>([])
const selectedId = ref<number | null>(null)
const recoveryData = ref<RecoveryItem[]>([])
const recoveryChart = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const fetchCampaigns = async () => {
  const { data } = await axios.get('/admin/campaign-list')
  campaigns.value = data
}

const fetchRecoveryData = async () => {
  if (!selectedId.value) return
  const { data } = await axios.get('/admin/campaign-recovery', {
    params: { campaignId: selectedId.value }
  })
  recoveryData.value = data
  renderChart()
}

const renderChart = () => {
  const labels = recoveryData.value.map(item => item.action)
  const values = recoveryData.value.map(item => item.rate)

  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(recoveryChart.value!, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Geri Dönüş %',
        data: values,
        backgroundColor: '#10B981',
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
          title: { display: true, text: 'Geri Kazanım Oranı (%)' }
        },
        x: {
          title: { display: true, text: 'Aksiyon Tipi' }
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