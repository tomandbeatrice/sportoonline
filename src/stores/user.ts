// src/stores/user.ts
import { defineStore } from 'pinia'
export const useUserStore = defineStore('user', {
  state: () => ({
    role: localStorage.getItem('role') || 'guest'
  }),
  actions: {
    setRole(role: string) {
      this.role = role
      localStorage.setItem('role', role)
    }
  }
})