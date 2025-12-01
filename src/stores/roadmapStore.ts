// stores/roadmapStore.ts
import { defineStore } from 'pinia'

export const useRoadmapStore = defineStore('roadmap', {
  state: () => ({
    sprints: [
      { id: 'sprint-8', title: 'Katkı Skoru Paneli', status: 'pending' }
    ]
  }),
  actions: {
    markAsDone(id: string) {
      const sprint = this.sprints.find(s => s.id === id)
      if (sprint) sprint.status = 'done'
    }
  }
})