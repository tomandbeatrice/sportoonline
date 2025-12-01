<template>
  <section class="p-6 space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Kampanya Bazlı Dönüşüm</h2>
    <canvas ref="chartCanvas" height="300"></canvas>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

const chartCanvas = ref(null)

onMounted(async () => {
  const userRes = await axios.get('/api/user')
  const sellerId = userRes.data.id

  const res = await axios.get(`/api/seller/${sellerId}/campaign-conversion`)
  const data = res.data

  const labels = data.map(item => item.campaign)
  const conversionRates = data.map(item => item.conversionRate)

  new Chart(chartCanvas.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Dönüşüm Oranı (%)',
        data: conversionRates,
        backgroundColor: ['#10B981', '#3B82F6', '#9CA3AF']
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          max: 100
        }
      }
    }
  })
})
</script>