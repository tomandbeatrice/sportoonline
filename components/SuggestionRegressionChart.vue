<template>
  <div>
    <h3>Öneri Güven vs Başarı Skoru</h3>
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Chart } from 'chart.js/auto'
import axios from 'axios'

const canvas = ref(null)

onMounted(async () => {
  const res = await axios.get('/api/admin/suggestion-history')
  const points = res.data.suggestion_history.map(item => ({
    x: item.confidence_score,
    y: item.actual_success_score
  }))

  new Chart(canvas.value, {
    type: 'scatter',
    data: {
      datasets: [{
        label: 'Öneri Noktaları',
        data: points,
        backgroundColor: '#4da6ff'
      }]
    },
    options: {
      responsive: true,
      scales: {
        x: {
          title: { display: true, text: 'Güven Katsayısı (%)' },
          min: 0,
          max: 100
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