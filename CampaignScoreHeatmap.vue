<template>
  <div>
    <h2>Kampanya Skor Yoğunluk Haritası</h2>
    <canvas ref="heatmapCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Chart } from 'chart.js/auto'

const heatmapCanvas = ref(null)

onMounted(async () => {
  const sellerId = 45
  const res = await axios.get(`/api/seller/${sellerId}/campaign-suggestion-list`)
  const data = res.data

  const categories = [...new Set(data.map(item => item.campaign))]
  const scoreBuckets = [0, 5, 10, 15, 20]

  const matrix = categories.map(campaign => {
    return scoreBuckets.map(bucket => {
      const count = data.filter(item =>
        item.campaign === campaign &&
        item.score >= bucket &&
        item.score < bucket + 5
      ).length
      return count
    })
  })

  new Chart(heatmapCanvas.value, {
    type: 'matrix',
    data: {
      datasets: [{
        label: 'Skor Yoğunluğu',
        data: matrix.flatMap((row, i) =>
          row.map((value, j) => ({
            x: scoreBuckets[j],
            y: categories[i],
            v: value
          }))
        ),
        backgroundColor(ctx) {
          const value = ctx.dataset.data[ctx.dataIndex].v
          return value > 5 ? '#e74c3c' : value > 2 ? '#f39c12' : '#2ecc71'
        },
        width: 40,
        height: 40
      }]
    },
    options: {
      scales: {
        x: { title: { display: true, text: 'Skor Aralığı' } },
        y: { title: { display: true, text: 'Kampanya' } }
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