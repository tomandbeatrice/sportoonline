import apiClient from './apiClient'
import type { Task, Campaign, Order, Banner } from '@/types/marketplace'

export const marketplaceService = {
  // Services
  getServices() {
    return apiClient.get('/v1/services')
  },

  // Tasks
  getUserTasks() {
    return apiClient.get<Task[]>('/user-tasks')
  },
  
  updateUserTask(id: number, data: Partial<Task>) {
    // Map frontend 'completed' to backend 'is_completed'
    const backendData: Record<string, unknown> = { ...data }
    if ('completed' in data) {
      backendData.is_completed = data.completed
      delete backendData.completed
    }
    return apiClient.put(`/user-tasks/${id}`, backendData)
  },
  
  createUserTask(data: Partial<Task>) {
    return apiClient.post<Task>('/user-tasks', data)
  },

  // Campaigns
  getActiveCampaigns() {
    return apiClient.get<Campaign[]>('/marketplace/campaigns')
  },

  // Orders
  getActiveOrders() {
    return apiClient.get<Order[]>('/orders/active')
  },

  // Banners
  getBanners() {
    return apiClient.get<Banner[]>('/marketplace/banners')
  }
}
