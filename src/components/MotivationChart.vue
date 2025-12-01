<template>
  <div class="motivation-chart">
    <h2>📈 Roadmap İlerleme</h2>
    <p>Toplam Modül: {{ totalModules }}</p>
    <p>Tamamlanan Sprint: {{ completedModules }}</p>
    <p>İlerleme: {{ progress }}%</p>
    <progress :value="progress" max="100"></progress>

    <h2 style="margin-top: 2rem;">🧠 Ekip Motivasyon Skoru</h2>
    <apexchart type="radar" height="300" :options="options" :series="series" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { fetchMotivationStats } from '@/services/roadmapService'

const completedModules = 9
const totalModules = 11
const progress = Math.round((completedModules / totalModules) * 100)

const series = ref([
  {
    name: 'Motivasyon Skoru',
    data: []
  }
])

const options = {
  chart: { type: 'radar' },
  labels: ['Engin', 'Zeynep', 'Baran'],
  title: {
    text: 'Sprint 13 Ekip Motivasyonu',
    align: 'center'
  }
}

onMounted(async () => {
  const stats = await fetchMotivationStats()
  series.value[0].data = stats
})
</script>

<style scoped>
.motivation-chart {
  padding: 1rem;
  background-color: #f0f9ff;
  border-radius: 8px;
}
progress {
  width: 100%;
  height: 20px;
}
</style>