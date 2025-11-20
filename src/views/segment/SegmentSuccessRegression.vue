<template>
  <div>
    <h3>Segment Başarı Regresyonu</h3>
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Chart } from 'chart.js/auto'

const canvas = ref(null)

const segmentSuccessData = {
  Premium: [78, 82, 85, 91],
  Standard: [65, 70, 74, 81],
  Low: [40, 45, 50, 55]
}

onMounted(() => {
  const datasets = Object.entries(segmentSuccessData).map(([segment, scores]) => ({
    label: segment,
    data: scores.map((y, i) => ({ x: i + 1, y })),
    borderColor:
      segment === 'Premium' ? '#4CAF50' :
      segment === 'Standard' ? '#2196F3' :
      '#FFC107',
    fill: false,
    tension: 0.3
  }))

  new Chart(canvas.value, {
    type: 'line',
    data: { datasets },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      },
      scales: {
        x: {
          title: { display: true, text: 'Kampanya Sırası' },
          ticks: { stepSize: 1 }
        },
        y: {
          title: { display: true, text: 'Başarı Skoru' },
          min: 0,
          max: 100
        }
      }
    }
  })
})
</script>