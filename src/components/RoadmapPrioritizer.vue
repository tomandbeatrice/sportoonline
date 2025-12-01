<template>
  <div class="prioritizer">
    <h3>Önceliklendirilmiş Roadmap</h3>
    <ul>
      <li v-for="item in sortedRoadmap" :key="item.id">
        🔢 {{ item.title }} → Skor: {{ item.score }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const roadmap = [
  { id: 1, title: 'Test otomasyonu', score: 92 },
  { id: 2, title: 'Cockpit dışa aktarım API’si', score: 85 },
  { id: 3, title: 'Geri bildirim analizi', score: 78 }
]

const sortedRoadmap = computed(() =>
  roadmap.sort((a, b) => b.score - a.score)
)
</script>

<style scoped>
.prioritizer {
  margin-top: 2rem;
  background: #fefefe;
  padding: 16px;
  border-radius: 8px;
}
</style>