<template>
  <div class="forecast-panel">
    <h3>AI Roadmap Tahminleri</h3>

    <ul v-if="forecastData.length">
      <li v-for="item in forecastData" :key="item.id">
        🔮 {{ item.title }} → Tahmini Tarih: {{ item.estimated }}
      </li>
    </ul>

    <p v-else>⏳ Tahminler yükleniyor...</p>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { predictRoadmapCompletion } from '@/services/forecastAI'

const forecastData = ref([])

onMounted(async () => {
  forecastData.value = await predictRoadmapCompletion()
})
</script>

<style scoped>
.forecast-panel {
  margin-top: 2rem;
  background: #f0f4ff;
  padding: 16px;
  border-radius: 8px;
}
.forecast-panel ul {
  list-style: none;
  padding: 0;
}
.forecast-panel li {
  padding: 6px 0;
  font-size: 15px;
}
</style>