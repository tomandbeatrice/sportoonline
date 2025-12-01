<script setup lang="ts">
import { computed } from 'vue'
import { Pie } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale,
} from 'chart.js'
import type { ModuleLog } from '@types/module'

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale)

defineProps<{ logs: ModuleLog[] }>()

const statusCounts = computed(() => {
  const counts: Record<string, number> = {}
  for (const log of logs) {
    counts[log.status] = (counts[log.status] || 0) + 1
  }
  return counts
})

const chartData = computed(() => ({
  labels: Object.keys(statusCounts.value),
  datasets: [
    {
      data: Object.values(statusCounts.value),
      backgroundColor: ['#4caf50', '#ff9800', '#f44336', '#2196f3'],
    },
  ],
}))
</script>

<template>
  <div>
    <h4>Modül Durumları (Pie Chart)</h4>
    <Pie :data="chartData" />
  </div>
</template>

<style scoped>
h4 {
  margin-bottom: 1rem;
}
</style>