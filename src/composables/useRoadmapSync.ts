import { ref } from 'vue'
import { getSuggestions, addToRoadmap, getCompletedModules, calculateProgress } from '@/services/roadmapService'

export function useRoadmapSync(modulId: string) {
  const suggestions = ref<Suggestion[]>([])
  const accepted = ref<Suggestion[]>([])
  const progress = ref<number>(0)

  async function fetchSuggestions() {
    suggestions.value = await getSuggestions(modulId)
    accepted.value = suggestions.value.filter(s => s.score > 0.8 && s.type === 'technical')
  }

  async function syncToRoadmap() {
    for (const suggestion of accepted.value) {
      await addToRoadmap(modulId, suggestion)
    }
    updateRoadmapProgress()
  }

  function updateRoadmapProgress() {
    const completed = getCompletedModules()
    const allAccepted = getAcceptedSuggestions() // global veya modül bazlı olabilir
    progress.value = calculateProgress(completed, allAccepted)
  }

  return {
    suggestions,
    accepted,
    progress,
    fetchSuggestions,
    syncToRoadmap,
    updateRoadmapProgress
  }
}