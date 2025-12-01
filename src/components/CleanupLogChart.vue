<template>
  <div class="cleanup-chart">
    <h3>Export Cleanup Durumu</h3>
    <canvas id="cleanupChart"></canvas>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import Chart from 'chart.js/auto'

const logData = ref(null)

onMounted(async () => {
  try {
    const res = await fetch('/api/exports/cleanup-log')
    logData.value = await res.json()
    renderChart()
  } catch (err) {
    console.error('Log verisi alınamadı:', err)
  }
})

function renderChart() {
  const ctx = document.getElementById('cleanupChart').getContext('2d')
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Silinen', 'Atlanan', 'Hatalı'],
      datasets: [{
        data: [logData.value.deleted, logData.value.skipped, logData.value.errors],
        backgroundColor: ['#66bb6a', '#ffa726', '#ef5350']
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  })
}
</script>

<style scoped>
.cleanup-chart {
  background: #f4f4f4;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 0 6px rgba(0,0,0,0.1);
}
</style>