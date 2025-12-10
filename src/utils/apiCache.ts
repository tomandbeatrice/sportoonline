/**
 * Simple in-memory API cache utility
 * Reduces redundant API calls by caching responses
 */

interface CacheEntry<T> {
  data: T
  timestamp: number
}

class ApiCache {
  private cache = new Map<string, CacheEntry<unknown>>()
  private defaultTTL: number

  constructor(defaultTTL = 5 * 60 * 1000) { // Default 5 minutes
    this.defaultTTL = defaultTTL
  }

  /**
   * Get cached data if available and not expired
   */
  get<T>(key: string, ttl = this.defaultTTL): T | null {
    const entry = this.cache.get(key)
    if (!entry) return null

    const isExpired = Date.now() - entry.timestamp > ttl
    if (isExpired) {
      this.cache.delete(key)
      return null
    }

    return entry.data as T
  }

  /**
   * Set data in cache
   */
  set<T>(key: string, data: T): void {
    this.cache.set(key, {
      data,
      timestamp: Date.now()
    })
  }

  /**
   * Check if key exists and is not expired
   */
  has(key: string, ttl = this.defaultTTL): boolean {
    return this.get(key, ttl) !== null
  }

  /**
   * Remove specific key from cache
   */
  remove(key: string): void {
    this.cache.delete(key)
  }

  /**
   * Clear all cache
   */
  clear(): void {
    this.cache.clear()
  }

  /**
   * Get cache size
   */
  size(): number {
    return this.cache.size
  }

  /**
   * Fetch with cache - main utility function
   * If cached data exists, returns it; otherwise fetches and caches
   */
  async fetchWithCache<T>(
    key: string,
    fetcher: () => Promise<T>,
    ttl = this.defaultTTL
  ): Promise<T> {
    const cached = this.get<T>(key, ttl)
    if (cached !== null) {
      return cached
    }

    const data = await fetcher()
    this.set(key, data)
    return data
  }
}

// Export singleton instance
export const apiCache = new ApiCache()

// Export class for custom instances
export { ApiCache }
