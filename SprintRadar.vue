<template>
  <section class="sprint-radar">
    <h3>📈 Sprint Radar</h3>
    <canvas ref="chartCanvas" width="600" height="300"></canvas>
  </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps<{
  sprintHistory: Array<{
    name: string
    date: string
    successRate: number
    motivation: number
  }>
}>()

const chartCanvas = ref<HTMLCanvasElement>()

onMounted(() => {
  if (!chartCanvas.value) return

  const labels = props.sprintHistory.map(s => s.date)
  const successData = props.sprintHistory.map(s => s.successRate)
  const motivationData = props.sprintHistory.map(s => s.motivation)

  new Chart(chartCanvas.value, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'Başarı Oranı',
          data: successData,
          borderColor: '#4caf50',
          fill: false
        },
        {
          label: 'Motivasyon',
          data: motivationData,
          borderColor: '#ff9800',
          fill: false
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true, max: 100 }
      }
    }
  })
})
</script>

<style scoped>
.sprint-radar {
  margin-top: 2rem;
  padding: 1rem;
  background-color: var(--bgSoft);
  border-radius: 12px;
}
</style>