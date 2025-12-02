<template>
  <section class="trend-panel">
    <h3>Modül Trend Analizi</h3>
    <canvas ref="trendCanvas" class="chart-canvas" />
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import Chart from 'chart.js/auto'

const trendCanvas = ref<HTMLCanvasElement | null>(null)

onMounted(() => {
  if (!trendCanvas.value) return

  new Chart(trendCanvas.value, {
    type: 'bar',
    data: {
      labels: ['Login', 'Dashboard', 'Export', 'Risk'],
      datasets: [{
        label: 'Kullanım Yoğunluğu',
        data: [65, 80, 45, 30],
        backgroundColor: 'rgba(255,111,60,0.6)',
        borderRadius: 6,
        maxBarThickness: 40
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { mode: 'index', intersect: false }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: 'var(--text)' }
        },
        y: {
          beginAtZero: true,
          ticks: { color: 'var(--text)' }
        }
      }
    }
  })
})
</script>

<style scoped>
.trend-panel {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1.2rem;
  background-color: var(--card);
  color: var(--text);
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  gap: 1rem;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.chart-canvas {
  width: 100%;
  max-width: 500px;
  height: 260px;
}
</style>