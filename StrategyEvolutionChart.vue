<template>
  <div>
    <h3>Strateji Evrimi Grafiği</h3>
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Chart } from 'chart.js/auto'

const canvas = ref(null)

onMounted(async () => {
  const res = await fetch('/api/admin/strategy-evolution')
  const data = await res.json()

  const labels = Object.keys(data)
  const values = Object.values(data)

  new Chart(canvas.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Yeni Segment Ağırlıkları',
        data: values,
        backgroundColor: ['#4CAF50', '#2196F3', '#FFC107']
      }]
    },
    options: {
      scales: {
        y: { beginAtZero: true }
      }
    }
  })
})
</script>