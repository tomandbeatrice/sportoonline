<template>
  <section class="module-log-chart">
    <h2>📊 Modül Yoğunluğu</h2>
    <canvas ref="chartCanvas"></canvas>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

const chartCanvas = ref<HTMLCanvasElement | null>(null)

const fetchAndRenderChart = async () => {
  const res = await axios.get('/module-logs')
  const modules = res.data

  const labels = modules[0].data.map((d: any) =>
    new Date(d.timestamp).toLocaleTimeString('tr-TR')
  )

  const datasets = modules.map((mod: any) => ({
    label: mod.module,
    data: mod.data.map((d: any) => d.count),
    fill: false,
    borderColor: mod.module === 'export' ? '#007bff' : '#28a745',
    tension: 0.3
  }))

  new Chart(chartCanvas.value!, {
    type: 'line',
    data: {
      labels,
      datasets
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top'
        },
        title: {
          display: true,
          text: 'Modül Bazlı İşlem Yoğunluğu'
        }
      }
    }
  })
}

onMounted(fetchAndRenderChart)
</script>

<style scoped>
.module-log-chart {
  max-width: 800px;
  margin: auto;
}
canvas {
  width: 100%;
  height: 400px;
}
</style>