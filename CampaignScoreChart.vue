<template>
  <section class="p-6 space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Kampanya Başarı Skoru</h2>
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
  const res = await axios.get(`/api/seller/${sellerId}/campaign-score-summary`)
  const data = res.data

  const labels = data.map(item => item.campaign)
  const scores = data.map(item => item.score)

  new Chart(chartCanvas.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Başarı Skoru',
        data: scores,
        backgroundColor: '#6366F1'
      }]
    },
    options: {
      responsive: true,
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