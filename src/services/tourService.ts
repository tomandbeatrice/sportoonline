import apiClient from './apiClient'
import { apiCache } from '@/utils/apiCache'

export interface Tour {
  id: number
  title: string
  location: string
  price: number
  rating: number
  reviews: number
  image: string
  duration: string
  dates: string[]
}

const CACHE_TTL = 5 * 60 * 1000 // 5 minutes

export const tourService = {
  async getTours() {
    return apiCache.fetchWithCache(
      'tours_list',
      () => apiClient.get<{ data: Tour[] }>('/services/tours'),
      CACHE_TTL
    )
  },
  
  async getTour(id: number) {
    return apiCache.fetchWithCache(
      `tour_${id}`,
      () => apiClient.get<{ data: Tour }>(`/services/tours/${id}`),
      CACHE_TTL
    )
  }
}
