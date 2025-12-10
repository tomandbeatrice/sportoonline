import apiClient from './apiClient'
import { apiCache } from '@/utils/apiCache'

export interface Car {
  id: number
  brand?: string
  model: string
  type: string
  transmission: string
  fuel: string
  price_per_day: number
  image: string
  supplier: string
  features?: string[]
}

const CACHE_TTL = 5 * 60 * 1000 // 5 minutes

export const carService = {
  async getCars() {
    return apiCache.fetchWithCache(
      'cars_list',
      () => apiClient.get<{ data: Car[] }>('/services/cars'),
      CACHE_TTL
    )
  },
  
  async getCar(id: number) {
    return apiCache.fetchWithCache(
      `car_${id}`,
      () => apiClient.get<{ data: Car }>(`/services/cars/${id}`),
      CACHE_TTL
    )
  }
}
