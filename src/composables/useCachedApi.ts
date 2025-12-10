import { ref, watchEffect } from 'vue'
import apiClient from '@/services/apiClient'

interface CacheOptions {
  ttl?: number // Time to live in milliseconds
  key?: string // Custom cache key
  staleWhileRevalidate?: boolean
}

const cache = new Map<string, { data: any; timestamp: number }>()

export function useCachedApi<T>(url: string, options: CacheOptions = {}) {
  const data = ref<T | null>(null)
  const error = ref<any>(null)
  const loading = ref(false)

  const ttl = options.ttl || 5 * 60 * 1000 // Default 5 minutes
  const cacheKey = options.key || url

  const fetchData = async (force = false) => {
    loading.value = true
    error.value = null

    // Check cache
    const cached = cache.get(cacheKey)
    const now = Date.now()

    if (cached) {
      const isExpired = now - cached.timestamp > ttl
      
      if (!isExpired || options.staleWhileRevalidate) {
        data.value = cached.data
        if (!isExpired && !force) {
          loading.value = false
          return
        }
      }
    }

    try {
      const response = await apiClient.get(url)
      data.value = response.data
      
      // Update cache
      cache.set(cacheKey, {
        data: response.data,
        timestamp: now
      })
    } catch (err) {
      error.value = err
    } finally {
      loading.value = false
    }
  }

  watchEffect(() => {
    fetchData()
  })

  return {
    data,
    error,
    loading,
    refresh: () => fetchData(true)
  }
}

export const clearApiCache = () => {
  cache.clear()
}
