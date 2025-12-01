<template>
  <div class="card bg-white shadow-md p-4 mb-6">
    <h2 class="text-lg font-semibold mb-2">👤 Kullanıcı Log Dağılımı</h2>
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
const props = defineProps<{ logs: Array<{ user_id: number }> }>()

const userCounts = computed(() => {
  const counts: Record<string, number> = {}
  props.logs.forEach(log => {
    const id = `User #${log.user_id}`
    counts[id] = (counts[id] || 0) + 1
  })
  return counts
})

const chartData = {
  labels: Object.keys(userCounts.value),
  datasets: [
    {
      label: 'Log Adedi',
      backgroundColor: '#8e44ad',
      data: Object.values(userCounts.value)
    }
  ]
}

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { display: false }
  },
  scales: {
    x: {
      ticks: { autoSkip: false }
    }
  }
}
</script>