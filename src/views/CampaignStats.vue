<template>
  <section class="p-6 bg-white rounded shadow max-w-4xl mx-auto">
    <h2 class="text-xl font-bold mb-4">📊 Kampanya Skor Analizi</h2>
    <canvas ref="chartRef" height="300"></canvas>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'
import Chart from 'chart.js/auto'

const { get } = useApi()
const chartRef = ref<HTMLCanvasElement | null>(null)

onMounted(async () => {
  const data = await get('/admin/campaign-score-trend')

  const labels = data.map((item: any) => item.name)
  const scores = data.map((item: any) => item.score)

  if (chartRef.value) {
    new Chart(chartRef.value, {
      type: 'bar',
      data: {
        labels,
        datasets: [
          {
            label: 'Skor',
            data: scores,
            backgroundColor: '#3b82f6'
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: true }
        },
        scales: {
          y: { beginAtZero: true, max: 100 }
        }
      }
    })
  }
})
</script>

<style scoped>
section {
  background-color: #f9fafb;
}
</style>