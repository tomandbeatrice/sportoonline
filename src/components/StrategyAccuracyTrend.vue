<template>
  <div>
    <h3>Öneri Isabet Trend Grafiği</h3>
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Chart } from 'chart.js/auto'

const canvas = ref(null)

onMounted(() => {
  const accuracyLog = JSON.parse(localStorage.getItem('accuracyLog') || '{}')
  const labels = Object.keys(accuracyLog)
  const values = labels.map(date => accuracyLog[date])

  new Chart(canvas.value, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Doğruluk (%)',
        data: values,
        borderColor: '#4da6ff',
        fill: false,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      scales: {
        x: { title: { display: true, text: 'Güncelleme Tarihi' } },
        y: { title: { display: true, text: 'Doğruluk Oranı (%)' }, min: 0, max: 100 }
      }
    }
  })
})
</script>