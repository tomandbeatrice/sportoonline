<template>
  <div class="success-rate-chart">
    <h2>📈 Modül Başarı Oranı</h2>
    <v-chart :option="chartOptions" autoresize />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useSuccessRateData } from './successRate/useSuccessRateData'
import type { EChartsOption } from 'echarts'

const { modülVerisi } = useSuccessRateData()

const chartOptions = computed<EChartsOption>(() => ({
  tooltip: { trigger: 'axis' },
  legend: { data: ['Başarılı', 'Hatalı'] },
  xAxis: { type: 'category', data: modülVerisi.value.map(m => m.ad) },
  yAxis: { type: 'value' },
  series: [
    {
      name: 'Başarılı',
      type: 'bar',
      data: modülVerisi.value.map(m => m.successCount),
      itemStyle: { color: '#4CAF50' }
    },
    {
      name: 'Hatalı',
      type: 'bar',
      data: modülVerisi.value.map(m => m.failCount),
      itemStyle: { color: '#F44336' }
    }
  ]
}))
</script>

<style scoped>
.success-rate-chart {
  max-width: 700px;
  margin: auto;
}
</style>