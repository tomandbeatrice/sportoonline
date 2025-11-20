<!-- src/components/LogChart.vue -->
<template>
  <div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-2">📊 Hata Tipi Dağılımı</h2>
    <canvas ref="chartRef" height="120"></canvas>
  </div>
</template>

<script setup lang="ts">
import { onMounted, watch, ref } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps<{ data: number[] }>()
const chartRef = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const renderChart = () => {
  if (!chartRef.value) return

  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(chartRef.value, {
    type: 'bar',
    data: {
      labels: ['Başarı', 'Hata', 'Uyarı', 'Bilgi'],
      datasets: [{
        label: 'Log Sayısı',
        data: props.data,
        backgroundColor: ['#34d399', '#f87171', '#fbbf24', '#60a5fa'],
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  })
}

onMounted(renderChart)
watch(() => props.data, renderChart)
</script>