<template>
  <div class="chart-wrapper">
    <h3>Modül İşlem Grafiği</h3>
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup lang="ts">
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale
} from 'chart.js'
import { ref, onMounted } from 'vue'
import axios from 'axios'

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale)

interface ModuleLog {
  module: string
  data: { timestamp: string; count: number }[]
}

const chartData = ref({
  labels: [] as string[],
  datasets: [] as any[]
})

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' as const },
    title: {
      display: true,
      text: 'Modül İşlem Sayısı',
      position: 'top' as const
    }
  }
}

onMounted(async () => {
  const res = await axios.get<ModuleLog[]>('http://127.0.0.1:8000/api/module-logs')
  const modules = res.data

  chartData.value.labels = modules[0]?.data.map(d => d.timestamp) || []

  chartData.value.datasets = modules.map(mod => ({
    label: mod.module,
    data: mod.data.map(d => d.count),
    borderColor: mod.module === 'export' ? '#42A5F5' : '#66BB6A',
    backgroundColor: 'rgba(0,0,0,0)',
    tension: 0.4
  }))
})
</script>

<style scoped>
.chart-wrapper {
  padding: 1rem;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}
.chart-wrapper:hover {
  transform: scale(1.01);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>