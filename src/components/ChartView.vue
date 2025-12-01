<script setup>
import { ref, onMounted } from 'vue'
import { useChartModule } from '@/composables/useChartModule'

const selectedCategory = ref('')
const {
  chartCanvas,
  categories,
  initChart,
  fetchAndUpdateChart
} = useChartModule(selectedCategory)

onMounted(() => {
  initChart()
  fetchAndUpdateChart()
  setInterval(fetchAndUpdateChart, 5000)
})
</script>
<template>
  <div>
    <select v-model="selectedCategory">
      <option value="">Tümü</option>
      <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
    </select>
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>