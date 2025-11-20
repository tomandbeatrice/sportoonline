<script setup lang="ts">
import { Chart, registerables } from 'chart.js'
import { onMounted, ref } from 'vue'
import type { ModuleLog } from '@types/module'

Chart.register(...registerables)

const props = defineProps<{ log: ModuleLog }>()
const canvasRef = ref<HTMLCanvasElement | null>(null)

onMounted(() => {
  if (!canvasRef.value) return

  const labels = props.log.data.map(d =>
    new Date(d.timestamp).toLocaleTimeString('tr-TR', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
    })
  )

  const values = props.log.data.map(d => d.count)

  new Chart(canvasRef.value, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: props.log.module,
        data: values,
        borderColor: '#42a5f5',
        backgroundColor: 'rgba(66,165,245,0.2)',
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        title: { display: true, text: `Veri Akışı: ${props.log.module}` }
      }
    }
  })
})
</script>

<template>
  <canvas ref="canvasRef" width="400" height="200"></canvas>
</template>