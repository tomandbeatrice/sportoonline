<template>
  <div class="analiz-paneli">
    <h2 class="flex items-center gap-2">
      <BadgeIcon name="bar-chart" cls="w-6 h-6 text-blue-600" /> Analiz Paneli
    </h2>

    <SuccessRateChart />
    <ModuleComparison />

    <!-- Opsiyonel özet -->
    <!-- <AnalizÖzeti :toplamHata="toplamHata" :toplamBaşarı="toplamBaşarı" /> -->
  </div>
</template>

<script setup lang="ts">
import SuccessRateChart from '@/components/Analiz/SuccessRateChart.vue'
import ModuleComparison from '@/components/Analiz/ModuleComparison.vue'
// import AnalizÖzeti from '@/components/Analiz/AnalizÖzeti.vue'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'
</script>

<style scoped>
.analiz-paneli {
  max-width: 800px;
  margin: auto;
  padding: 2rem 1rem;
}
</style>
const renkBasari = getComputedStyle(document.documentElement)
  .getPropertyValue('--renk-başarı').trim()

const renkHata = getComputedStyle(document.documentElement)
  .getPropertyValue('--renk-hata').trim()

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { labels: { color: renkBasari } },
  },
  scales: {
    x: { ticks: { color: renkHata } },
    y: { ticks: { color: renkBasari } }
  }
}

const chartData = {
  labels: ['Modül 1', 'Modül 2', 'Modül 3'],
  datasets: [
    {
      label: 'Hata Yoğunluğu',
      data: [12, 8, 15],
      backgroundColor: renkHata
    },
    {
      label: 'Süre Ortalaması',
      data: [4.2, 3.8, 5.1],
      backgroundColor: renkBasari
    }
  ]
}