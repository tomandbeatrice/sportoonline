<script setup lang="ts">
import { Chart, registerables } from 'chart.js'
import { onMounted, ref } from 'vue'
import type { ModuleLog } from '@types/module'

Chart.register(...registerables)

const props = defineProps<{ logs: ModuleLog[] }>()
const canvasRef = ref<HTMLCanvasElement | null>(null)

function parseDuration(duration: string): number {
  const match = duration.match(/(\\d+)s/)
  return match ? parseInt(match[1]) : 0
}

onMounted(() => {
  if (!canvasRef.value) return

  const labels = props.logs.map(log => log.module)
  const durations = props.logs.map(log => parseDuration(log.duration))

  new Chart(canvasRef.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Süre (saniye)',
        data: durations,
        backgroundColor: ['#66bb6a', '#29b6f6'],
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        title: { display: true, text: 'Modül Çalışma Süreleri' }
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