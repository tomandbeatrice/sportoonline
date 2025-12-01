<template>
  <section class="roadmap-panel">
    <ul class="module-list">
      <li v-for="modul in sprint.modules" :key="modul.id" class="module-item">
        <header class="module-header">
          <strong>{{ modul.name }}</strong>
          <span class="progress-info">
            İlerleme: %{{ modul.progress }} / Başarı: %{{ modul.successRate }}
          </span>
        </header>

        <SuggestionCard :modul="modul" />
        <ForumThread :modul="modul" />
        <RoadmapSync :modulId="modul.id" />
      </li>
    </ul>

    <div class="sprint-analysis-section">
      <MotivationPulse
        :modulStats="sprint.modules.map(m => ({
          progress: m.progress,
          successRate: m.successRate,
          suggestionsAccepted: m.suggestionsAccepted || 0
        }))"
      />
      <SprintCelebration :motivation="motivation" />
    </div>

    <div class="sprint-retrospective-section">
      <SprintForecast :sprintHistory="sprintHistory" />
      <SprintRadar :sprintHistory="sprintHistory" />
      <SprintArchive :archivedSprints="sprintHistory" />
    </div>
  </section>
</template>

<script setup lang="ts">
import SuggestionCard from '@/components/SuggestionCard.vue'
import ForumThread from '@/components/ForumThread.vue'
import RoadmapSync from '@/components/RoadmapSync.vue'
import MotivationPulse from '@/components/MotivationPulse.vue'
import SprintCelebration from '@/components/SprintCelebration.vue'
import SprintArchive from '@/components/SprintArchive.vue'
import SprintRadar from '@/components/SprintRadar.vue'
import SprintForecast from '@/components/SprintForecast.vue'

import { useMotivationPulse } from '@/composables/useMotivation'

defineProps<{
  sprint: {
    modules: Array<{
      id: string
      name: string
      progress: number
      successRate: number
      suggestionsAccepted?: number
    }>
  }
  sprintHistory: Array<{
    id: string
    name: string
    date: string
    successRate: number
    motivation: number
    modules: Array<{ id: string; name: string; progress: number }>
  }>
}>()

const { motivation, calculate } = useMotivationPulse(sprint.modules)
onMounted(() => calculate())
</script>

<style scoped>
.roadmap-panel {
  padding: 1rem;
  background-color: var(--bgSoft);
  border-radius: 12px;
}

.module-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.module-item {
  margin-bottom: 1.5rem;
  padding: 1rem;
  background-color: var(--cardSoft);
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
}

.module-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.progress-info {
  font-size: 0.9rem;
  color: var(--textSecondary);
}

.sprint-analysis-section,
.sprint-retrospective-section {
  margin-top: 2rem;
}
</style>