<template>
  <div>
    <h2>Kampanya Başarı Korelasyonları</h2>
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Chart } from 'chart.js/auto'

const canvas = ref(null)

onMounted(async () => {
  const res = await axios.get('/api/admin/campaign-scatter-data')
  const data = res.data

  const colors = {
    'Outlet Boost': '#3498db',
    'Flash Sale': '#e67e22',
    'Organik': '#2ecc71'
  }

  const datasets = Object.keys(colors).map(type => ({
    label: type,
    data: data.filter(d => d.type === type).map(d => ({ x: d.x, y: d.y })),
    backgroundColor: colors[type]
  }))

  new Chart(canvas.value, {
    type: 'scatter',
    data: { datasets },
    options: {
      scales: {
        x: { title: { display: true, text: 'Yorum Yanıt Oranı (%)' } },
        y: { title: { display: true, text: 'Kampanya Skoru' } }
      },
      plugins: {
        tooltip: {
          callbacks: {
            label: ctx => `Skor: ${ctx.raw.y}, Yanıt: ${ctx.raw.x}%`
          }
        }
      }
    }
  })
})
</script>