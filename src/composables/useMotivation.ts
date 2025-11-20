import { ref } from 'vue'

export interface ModulStats {
  progress: number
  successRate: number
  suggestionsAccepted: number
}

export function useMotivationPulse(modulStats: ModulStats[]) {
  const motivation = ref(0)

  const calculate = () => {
    if (!modulStats.length) {
      motivation.value = 0
      return
    }

    const totalProgress = modulStats.reduce((sum, m) => sum + m.progress, 0)
    const totalSuccess = modulStats.reduce((sum, m) => sum + m.successRate, 0)
    const acceptedCount = modulStats.filter(m => m.suggestionsAccepted > 0).length

    const avgProgress = totalProgress / modulStats.length
    const avgSuccess = totalSuccess / modulStats.length
    const suggestionImpactRatio = acceptedCount / modulStats.length

    const weightedScore =
      avgProgress * 0.4 +
      avgSuccess * 0.4 +
      suggestionImpactRatio * 100 * 0.2

    motivation.value = Math.round(weightedScore)
  }

  return {
    motivation,
    calculate
  }
}