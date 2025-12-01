<template>
  <div class="roadmap-sync">
    <button @click="syncToRoadmap">Roadmap’e Entegre Et</button>
    <ul v-if="accepted.length">
      <li v-for="s in accepted" :key="s.id">✅ {{ s.title }}</li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { useRoadmapSync } from '@/composables/useRoadmapSync'
const props = defineProps<{ modulId: string }>()
const { accepted, fetchSuggestions, syncToRoadmap } = useRoadmapSync(props.modulId)

onMounted(() => fetchSuggestions())
</script>

<style scoped>
.roadmap-sync {
  margin-top: 1rem;
  padding: 0.5rem;
  background-color: var(--bgSoft);
  border-radius: 6px;
}
button {
  background-color: var(--primary);
  color: white;
  padding: 0.4rem 0.8rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>