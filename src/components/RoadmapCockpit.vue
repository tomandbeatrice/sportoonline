<template>
  <section class="roadmap-cockpit">
    <div class="roadmap-updater">
      <h4>📍 Roadmap Güncellemesi</h4>
      <p>Son güncelleme: {{ lastUpdated }}</p>
      <button @click="triggerUpdate">🔄 Güncelle</button>
    </div>

    <RoadmapTimeline :items="updatedRoadmap" />
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import RoadmapTimeline from '@/components/RoadmapTimeline.vue'
import { updateRoadmap } from '@/services/roadmapEngine'

const updatedRoadmap = ref([])
const lastUpdated = ref('Henüz güncellenmedi')

const triggerUpdate = async () => {
  const res = await fetch('/api/roadmap/update', { method: 'POST' })
  const data = await res.json()
  lastUpdated.value = data.timestamp
  updatedRoadmap.value = await updateRoadmap()
}

onMounted(async () => {
  updatedRoadmap.value = await updateRoadmap()
})
</script>

<style scoped>
.roadmap-cockpit {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  padding: 1rem;
  background: #f9f9f9;
  border-radius: 12px;
}

.roadmap-updater {
  background: #fff;
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}

button {
  margin-top: 0.5rem;
  background-color: #0078d4;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
}
</style>