<template>
  <div>
    <h2>Kampanya Skor Trendleri</h2>
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Chart } from 'chart.js/auto'

const chartCanvas = ref(null)

onMounted(async () => {
  const sellerId = 45
  const res = await axios.get(`/api/seller/${sellerId}/campaign-suggestion-list`)
  const data = res.data

  const labels = data.map(item => item.lastRestart.split(' ')[0])
  const scores = data.map(item => item.score)
  const types = data.map(item => item.type)

  new Chart(chartCanvas.value, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Skor',
        data: scores,
        borderColor: '#3498db',
        backgroundColor: 'rgba(52, 152, 219, 0.2)',
        tension: 0.3,
        pointRadius: 5
      }]
    },
    options: {
      plugins: {
        tooltip: {
          callbacks: {
            label: (ctx) => `Skor: ${ctx.raw} (${types[ctx.dataIndex]})`
          }
        }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  })
})
</script>

<style scoped>
canvas {
  max-width: 100%;
  margin-top: 20px;
}
</style>