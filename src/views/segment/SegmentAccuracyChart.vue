<template>
  <div>
    <h3>Segment Doğruluk Grafiği</h3>
    <div v-if="error" class="error-banner">
      ⚠️ {{ error }}
    </div>
    <canvas v-else ref="canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Chart } from 'chart.js/auto'
import axios from 'axios'

const canvas = ref(null)
const error = ref(null)

onMounted(async () => {
  try {
    const res = await axios.get('/api/admin/segment-accuracy-breakdown')
    const data = res.data

    const labels = Object.keys(data)
    const values = labels.map(segment => data[segment].accuracy_percent)

    new Chart(canvas.value, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Doğruluk (%)',
          data: values,
          backgroundColor: ['#ff4d4d', '#4da6ff', '#66cc66']
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            title: { display: true, text: 'Doğruluk Oranı (%)' }
          },
          x: {
          title: { display: true, text: 'Segment' }
        }
      }
    }
  })
  } catch (err) {
    console.warn('Backend not available:', err.message)
    error.value = 'Backend bağlantısı kurulamadı. Laravel backend sunucusunu başlatın.'
  }
})
</script>