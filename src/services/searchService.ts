import apiClient from './apiClient'
import { apiCache } from '@/utils/apiCache'

export interface SearchResult {
  id: number | string
  type: 'tour' | 'car' | 'insurance' | 'activity' | 'product' | 'category' | 'seller' | 'brand' | 'page'
  title: string
  subtitle?: string
  image?: string
  price?: number
  badge?: string
  url: string
}

// Shorter cache for search (30 seconds)
const SEARCH_CACHE_TTL = 30 * 1000

export const searchService = {
  async search(query: string) {
    // Cache search results by query
    return apiCache.fetchWithCache(
      `search_${query.toLowerCase().trim()}`,
      () => apiClient.get<{ data: SearchResult[] }>(`/services/search?q=${encodeURIComponent(query)}`),
      SEARCH_CACHE_TTL
    )
  }
}
