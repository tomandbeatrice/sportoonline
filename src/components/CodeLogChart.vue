<template>
  <div class="card bg-white shadow-md p-4 mb-6">
    <h2 class="text-lg font-semibold mb-2">📦 Type Bazlı Log Dağılımı</h2>
    <Bar :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup lang="ts">
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

import { computed } from 'vue'
defineProps<{ logs: Array<{ type: string }> }>()

const typeLabels = ['success', 'error', 'warning']
const typeColors: Record<string, string> = {
  success: '#2ecc71',
  error: '#e74c3c',
  warning: '#f1c40f'
}

const typeCounts = computed(() => {
  const counts: Record<string, number> = { success: 0, error: 0, warning: 0 }
  logs.forEach(log => {
    if (counts[log.type] !== undefined) counts[log.type]++
  })
  return counts
})

const chartData = {
  labels: typeLabels,
  datasets: [
    {
      label: 'Log Türü',
      backgroundColor: typeLabels.map(type => typeColors[type]),
      data: typeLabels.map(type => typeCounts.value[type])
    }
  ]
}

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { display: false }
  }
}
</script>