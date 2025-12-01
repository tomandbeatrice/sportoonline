<template>
  <div class="module-comparison">
    <h2>📊 Modül Karşılaştırma</h2>
    <BarChart :chart-data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useModuleComparisonData } from '@/composables/useModuleComparisonData'
import BarChart from '@/components/charts/BarChart.vue'

const { karşılaştırmaVerisi } = useModuleComparisonData()

const chartData = computed(() => ({
  labels: karşılaştırmaVerisi.value.map(m => m.ad),
  datasets: [
    {
      label: 'Süre (dk)',
      data: karşılaştırmaVerisi.value.map(m => m.süre),
      backgroundColor: karşılaştırmaVerisi.value.map(m => {
        if (m.hata > 3) return 'rgba(255, 80, 80, 0.8)'       // 🔴 Kritik hata
        if (m.hata >= 2) return 'rgba(255, 200, 80, 0.8)'     // 🟡 Orta seviye
        return 'rgba(80, 200, 120, 0.8)'                      // 🟢 Düşük hata
      }),
      borderRadius: 4
    }
  ]
}))

const chartOptions = {
  responsive: true,
  plugins: {
    tooltip: {
      callbacks: {
        label: (ctx: any) => {
          const modül = karşılaştırmaVerisi.value[ctx.dataIndex]
          return `Süre: ${modül.süre}dk, Hata: ${modül.hata}`
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      title: {
        display: true,
        text: 'Süre (dk)'
      }
    }
  }
}
</script>

<style scoped>
.module-comparison {
  padding: 1rem;
}
</style>