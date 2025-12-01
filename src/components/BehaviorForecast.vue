<template>
  <ForecastChart :data="forecastData" />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import ForecastChart from '@/components/ForecastChart.vue'
import { predictBehavior } from '@/services/forecastEngine'

const forecastData = ref([])

onMounted(async () => {
  forecastData.value = await predictBehavior()
})
</script>