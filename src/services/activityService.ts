import apiClient from './apiClient'
import { apiCache } from '@/utils/apiCache'

export interface Activity {
  id: number
  title: string
  instructor?: string
  date: string
  location: string
  price: number
  image: string
  description?: string
}

const CACHE_TTL = 5 * 60 * 1000 // 5 minutes

export const activityService = {
  async getActivities() {
    return apiCache.fetchWithCache(
      'activities_list',
      () => apiClient.get<{ data: Activity[] }>('/services/activities'),
      CACHE_TTL
    )
  },

  async getActivity(id: number) {
    return apiCache.fetchWithCache(
      `activity_${id}`,
      () => apiClient.get<{ data: Activity }>(`/services/activities/${id}`),
      CACHE_TTL
    )
  }
}
