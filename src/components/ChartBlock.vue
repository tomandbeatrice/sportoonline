<template>
  <div>
    <canvas :id="`chart-${module}`"></canvas>
  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  data: Array,
  module: String
})

let chartInstance = null

onMounted(() => {
  renderChart()
})

watch(() => props.data, () => {
  if (chartInstance) chartInstance.destroy()
  renderChart()
})

function renderChart() {
  const ctx = document.getElementById(`chart-${props.module}`).getContext('2d')
  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: props.data.map(d => new Date(d.timestamp).toLocaleTimeString()),
      datasets: [{
        label: `${props.module} verisi`,
        data: props.data.map(d => d.count),
        borderColor: '#42A5F5',
        backgroundColor: 'rgba(66,165,245,0.2)',
        fill: true
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true }
      }
    }
  })
}
</script>