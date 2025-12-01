<template>
  <section class="forecast">
    <h3>🔮 Sprint Tahmini</h3>
    <p>Beklenen Başarı: %{{ forecast.predictedSuccess }}</p>
    <p>Beklenen Motivasyon: %{{ forecast.predictedMotivation }}</p>
    <ul v-if="forecast.riskyModules.length">
      <li v-for="modul in forecast.riskyModules" :key="modul">⚠️ Riskli: {{ modul }}</li>
    </ul>
  </section>
</template>

<script setup lang="ts">
import { getSprintForecast } from '@/services/forecastService'
const props = defineProps<{ sprintHistory: SprintHistory[] }>()

const forecast = getSprintForecast(props.sprintHistory)
</script>

<style scoped>
.forecast {
  margin-top: 2rem;
  padding: 1rem;
  background-color: var(--cardSoft);
  border-radius: 12px;
}
</style>