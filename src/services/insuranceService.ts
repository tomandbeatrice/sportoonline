import apiClient from './apiClient'
import { apiCache } from '@/utils/apiCache'

export interface Insurance {
  id: number
  title: string
  provider: string
  coverage: string
  price: number
  features: string[]
  description?: string
}

const CACHE_TTL = 5 * 60 * 1000 // 5 minutes

export const insuranceService = {
  async getInsuranceOptions() {
    return apiCache.fetchWithCache(
      'insurance_list',
      () => apiClient.get<{ data: Insurance[] }>('/services/insurance'),
      CACHE_TTL
    )
  },

  async getInsurance(id: number) {
    return apiCache.fetchWithCache(
      `insurance_${id}`,
      () => apiClient.get<{ data: Insurance }>(`/services/insurance/${id}`),
      CACHE_TTL
    )
  }
}
