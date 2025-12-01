<template>
  <div>
    <h3>Segment Ağırlık Trendleri</h3>
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Chart } from 'chart.js/auto'

const canvas = ref(null)

onMounted(() => {
  const history = JSON.parse(localStorage.getItem('strategyHistory') || '[]')
  const weightsLog = JSON.parse(localStorage.getItem('weightsLog') || '{}')

  const labels = history
  const datasets = ['Premium', 'Standard', 'Low'].map(segment => ({
    label: segment,
    data: labels.map(date => weightsLog[date]?.[segment] ?? null),
    borderColor: segment === 'Premium' ? '#ff4d4d' : segment === 'Standard' ? '#4da6ff' : '#66cc66',
    fill: false,
    tension: 0.3
  }))

  new Chart(canvas.value, {
    type: 'line',
    data: { labels, datasets },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      },
      scales: {
        x: { title: { display: true, text: 'Güncelleme Tarihi' } },
        y: { title: { display: true, text: 'Segment Ağırlığı' }, min: 0.5, max: 2 }
      }
    }
  })
})
</script>