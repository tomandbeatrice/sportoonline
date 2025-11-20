<template>
  <div>
    <h2>Segment Öneri Heatmap</h2>
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Chart } from 'chart.js/auto'

const canvas = ref(null)

onMounted(async () => {
  const res = await axios.get('/api/admin/segment-heatmap')
  const raw = res.data

  const segments = ['Premium', 'Standard', 'Low']
  const sellers = raw.map(d => `#${d.seller_id}`)
  const matrix = segments.map(segment =>
    sellers.map(sellerId => {
      const match = raw.find(d => `#${d.seller_id}` === sellerId && d.segment === segment)
      return match ? 1 : 0
    })
  )

  new Chart(canvas.value, {
    type: 'heatmap',
    data: {
      labels: sellers,
      datasets: segments.map((segment, i) => ({
        label: segment,
        data: matrix[i],
        backgroundColor: `rgba(${255 - i * 80}, ${100 + i * 50}, 150, 0.6)`
      }))
    },
    options: {
      scales: {
        x: { title: { display: true, text: 'Satıcı ID' } },
        y: { title: { display: true, text: 'Segment' } }
      }
    }
  })
})
</script>