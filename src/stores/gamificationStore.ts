import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useGamificationStore = defineStore('gamification', () => {
  const points = ref(1250)
  const level = ref(3)
  const streak = ref(5)
  
  const nextLevelPoints = computed(() => level.value * 500)
  const progress = computed(() => (points.value % 500) / 500 * 100)

  const badges = ref([
    { id: 1, name: 'İlk Sipariş', icon: '🎉', unlocked: true },
    { id: 2, name: 'Sadık Müşteri', icon: '⭐', unlocked: true },
    { id: 3, name: 'Gurme', icon: '🍔', unlocked: false },
    { id: 4, name: 'Gezgin', icon: '🚕', unlocked: false },
  ])

  const addPoints = (amount: number) => {
    points.value += amount
    checkLevelUp()
  }

  const checkLevelUp = () => {
    if (points.value >= nextLevelPoints.value) {
      level.value++
      // Show level up modal (logic to be implemented)
    }
  }

  return {
    points,
    level,
    streak,
    nextLevelPoints,
    progress,
    badges,
    addPoints
  }
})
