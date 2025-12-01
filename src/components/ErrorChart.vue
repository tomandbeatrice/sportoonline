<script setup lang="ts">
import { Chart, registerables } from 'chart.js'
import { onMounted, ref } from 'vue'
import type { ModuleLog } from '@types/module'

Chart.register(...registerables)

const props = defineProps<{ logs: ModuleLog[] }>()
const canvasRef = ref<HTMLCanvasElement | null>(null)

onMounted(() => {
  if (!canvasRef.value) return

  const labels = props.logs.map(log => log.module)
  const errorCounts = props.logs.map(log => log.errorCount)

  new Chart(canvasRef.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Hata Sayısı',
        data: errorCounts,
        backgroundColor: ['#ef5350', '#ffa726'],
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        title: { display: true, text: 'Modül Bazlı Hata Yoğunluğu' }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1 }
        }
      }
    }
  })
})
</script>

<template>
  <canvas ref="canvasRef" width="400" height="200"></canvas>
</template>