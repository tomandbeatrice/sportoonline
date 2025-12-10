<template>
  <div class="relative h-full w-full">
    <canvas ref="chartRef"></canvas>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, onUnmounted } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps<{
  data: number[]
  labels: string[]
  period: string
}>()

const chartRef = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const initChart = () => {
  if (!chartRef.value) return
  
  if (chartInstance) {
    chartInstance.destroy()
  }

  const ctx = chartRef.value.getContext('2d')
  if (!ctx) return

  // Gradient fill
  const gradient = ctx.createLinearGradient(0, 0, 0, 400)
  gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)')
  gradient.addColorStop(1, 'rgba(37, 99, 235, 0)')

  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: props.labels,
      datasets: [{
        label: 'Gelir (TL)',
        data: props.data,
        borderColor: '#2563eb',
        backgroundColor: gradient,
        borderWidth: 3,
        pointBackgroundColor: '#ffffff',
        pointBorderColor: '#2563eb',
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: '#1e293b',
          padding: 12,
          titleFont: { size: 13 },
          bodyFont: { size: 14, weight: 'bold' },
          displayColors: false,
          callbacks: {
            label: (context) => `${context.parsed.y.toLocaleString('tr-TR')} TL`
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: '#f1f5f9',
            // drawBorder: false
          },
          ticks: {
            font: { size: 11 },
            color: '#64748b',
            callback: (value) => `${value} ₺`
          }
        },
        x: {
          grid: {
            display: false
          },
          ticks: {
            font: { size: 11 },
            color: '#64748b'
          }
        }
      },
      interaction: {
        intersect: false,
        mode: 'index'
      }
    }
  })
}

watch(() => props.data, () => {
  initChart()
}, { deep: true })

onMounted(() => {
  initChart()
})

onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy()
  }
})
</script>
