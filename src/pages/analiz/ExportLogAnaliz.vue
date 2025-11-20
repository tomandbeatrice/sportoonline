<template>
  <section class="export-analiz">
    <h2>📊 Export Log Analizi</h2>
    <ExportLogChart />
  </section>
</template>

<script setup>
import ExportLogChart from '@/components/ExportLogChart.vue'
</script>

<style scoped>
.export-analiz {
  padding: 2rem;
  max-width: 1200px;
  margin: auto;
}
h2 {
  font-size: 1.6rem;
  margin-bottom: 1rem;
}
</style>