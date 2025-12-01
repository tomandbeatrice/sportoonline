<template>
  <section class="p-4">
    <h2 class="text-lg font-bold text-emerald-700">📊 Katılım Bazlı Kampanya Etkisi</h2>
    <canvas ref="chartCanvas" height="300"></canvas>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Chart } from 'chart.js/auto'

const chartCanvas = ref(null)

onMounted(async () => {
  const res = await axios.get('/api/admin/campaign-participation-effect')
  const data = res.data

  const labels = data.map(d => d.tag)
  const conversions = data.map(d => d.conversion_rate)

  new Chart(chartCanvas.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Dönüşüm Oranı (%)',
        data: conversions,
        backgroundColor: '#10b981'
      }]
    },
    options: {
      scales: {
        y: { beginAtZero: true }
      },
      plugins: {
        legend: { display: false }
      }
    }
  })
})
</script>