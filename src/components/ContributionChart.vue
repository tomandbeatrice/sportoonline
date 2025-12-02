<template>
  <div class="chart-panel">
    <apexchart type="bar" height="300" :options="chartOptions" :series="series" />
  </div>
</template>

<script setup lang="ts">
import ApexCharts from 'vue3-apexcharts'

const series = [
  {
    name: 'Katkı Skoru',
    data: [92, 78, 85]
  }
]

const chartOptions = {
  chart: {
    type: 'bar'
  },
  xaxis: {
    categories: ['Engin', 'Zeynep', 'Baran']
  },
  colors: ['#00b894'],
  title: {
    text: 'Sprint 8 Katkı Skoru',
    align: 'center'
  }
}
</script>

<style scoped>
.chart-panel {
  margin-top: 2rem;
}
</style>