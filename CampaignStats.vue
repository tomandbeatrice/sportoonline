<template>
  <section class="py-8">
    <h2 class="text-2xl font-bold mb-4">Kampanya Dönüşüm Trendleri</h2>

    <div class="mb-4 flex gap-4 justify-center">
      <select v-model="range" class="border px-3 py-2 rounded">
        <option value="7">Son 7 Gün</option>
        <option value="30">Son 30 Gün</option>
        <option value="90">Son 90 Gün</option>
      </select>
    </div>

    <canvas ref="trendChart" height="300"></canvas>
  </section>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

const range = ref(7)
const trendChart = ref(null)
let chartInstance = null

const fetchData = async () => {
  const res = await axios.get(`/api/admin/campaign-trend?days=${range.value}`)
  const data = res.data

  const labels = data.map(item => item.date)
  const earlyAccess = data.map(item => item.early_access_100)
  const betaInvite = data.map(item => item.beta_invite)
  const organic = data.map(item => item.organic)

  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(trendChart.value, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'SPORTOON100',
          data: earlyAccess,
          borderColor: '#10B981',
          fill: false
        },
        {
          label: 'BETA50',
          data: betaInvite,
          borderColor: '#3B82F6',
          fill: false
        },
        {
          label: 'Organik',
          data: organic,
          borderColor: '#9CA3AF',
          fill: false
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  })
}

onMounted(fetchData)
watch(range, fetchData)
</script>