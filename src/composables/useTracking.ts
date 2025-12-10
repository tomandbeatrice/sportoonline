import { ref } from 'vue'
import axios from 'axios'

// Singleton instance for tracking
let viewStartTime: number | null = null
let currentProductId: number | null = null

export function useTracking() {
  const isTracking = ref(false)

  /**
   * Ürün görüntüleme kaydı
   */
  const trackProductView = async (productId: number, source?: string, referrer?: string) => {
    try {
      await axios.post('/api/v1/tracking/product-view', {
        product_id: productId,
        source: source || getSource(),
        referrer: referrer || document.referrer,
      })
      
      // Görüntüleme süresini takip etmeye başla
      viewStartTime = Date.now()
      currentProductId = productId
    } catch (error) {
      console.error('Ürün görüntüleme kaydedilemedi:', error)
    }
  }

  /**
   * Görüntüleme süresini kaydet (sayfa kapatılırken)
   */
  const trackViewDuration = async () => {
    if (viewStartTime && currentProductId) {
      const duration = Math.floor((Date.now() - viewStartTime) / 1000)
      
      if (duration >= 3) { // En az 3 saniye kalmışsa kaydet
        try {
          await axios.post('/api/v1/tracking/view-duration', {
            product_id: currentProductId,
            duration: Math.min(duration, 3600), // Max 1 saat
          })
        } catch (error) {
          console.error('Görüntüleme süresi kaydedilemedi:', error)
        }
      }
      
      viewStartTime = null
      currentProductId = null
    }
  }

  /**
   * Kampanya görüntüleme kaydı
   */
  const trackCampaignView = async (campaignId: number, placement?: string) => {
    try {
      await axios.post('/api/v1/tracking/campaign-view', {
        campaign_id: campaignId,
        placement: placement,
      })
    } catch (error) {
      console.error('Kampanya görüntüleme kaydedilemedi:', error)
    }
  }

  /**
   * Kampanya tıklama kaydı
   */
  const trackCampaignClick = async (campaignId: number, clickTarget?: string) => {
    try {
      await axios.post('/api/v1/tracking/campaign-click', {
        campaign_id: campaignId,
        click_target: clickTarget,
      })
    } catch (error) {
      console.error('Kampanya tıklama kaydedilemedi:', error)
    }
  }

  /**
   * Arama kaydı
   */
  const trackSearch = async (query: string, resultsCount: number): Promise<number | null> => {
    try {
      const response = await axios.post('/api/v1/tracking/search', {
        query: query,
        results_count: resultsCount,
      })
      return response.data.search_id
    } catch (error) {
      console.error('Arama kaydedilemedi:', error)
      return null
    }
  }

  /**
   * Arama sonucuna tıklama kaydı
   */
  const trackSearchClick = async (searchId: number, productId: number) => {
    try {
      await axios.post('/api/v1/tracking/search-click', {
        search_id: searchId,
        product_id: productId,
      })
    } catch (error) {
      console.error('Arama tıklama kaydedilemedi:', error)
    }
  }

  /**
   * Sepet olayı kaydı
   */
  const trackCartEvent = async (
    productId: number,
    eventType: 'add' | 'remove' | 'update_quantity',
    quantity: number,
    price: number,
    source?: string
  ) => {
    try {
      await axios.post('/api/v1/tracking/cart-event', {
        product_id: productId,
        event_type: eventType,
        quantity: quantity,
        price: price,
        source: source,
      })
    } catch (error) {
      console.error('Sepet olayı kaydedilemedi:', error)
    }
  }

  /**
   * Kaynak belirleme
   */
  const getSource = (): string => {
    const path = window.location.pathname
    const query = new URLSearchParams(window.location.search)
    
    if (query.has('from_search')) return 'search'
    if (query.has('from_recommendation')) return 'recommendation'
    if (path.includes('/category')) return 'category'
    if (path.includes('/campaigns')) return 'campaign'
    if (document.referrer.includes('google.com')) return 'google'
    
    return 'direct'
  }

  /**
   * Son görüntülemeler
   */
  const getRecentViews = async (limit: number = 10) => {
    try {
      const response = await axios.get('/api/v1/tracking/recent-views', {
        params: { limit }
      })
      return response.data.recent_views
    } catch (error) {
      console.error('Son görüntülemeler alınamadı:', error)
      return []
    }
  }

  /**
   * Trend ürünler
   */
  const getTrendingProducts = async (limit: number = 10) => {
    try {
      const response = await axios.get('/api/v1/tracking/trending', {
        params: { limit }
      })
      return response.data.trending_products
    } catch (error) {
      console.error('Trend ürünler alınamadı:', error)
      return []
    }
  }

  /**
   * Popüler aramalar
   */
  const getPopularSearches = async (limit: number = 10) => {
    try {
      const response = await axios.get('/api/v1/tracking/popular-searches', {
        params: { limit }
      })
      return response.data.popular_searches
    } catch (error) {
      console.error('Popüler aramalar alınamadı:', error)
      return []
    }
  }

  // Sayfa kapatılırken görüntüleme süresini kaydet
  if (typeof window !== 'undefined') {
    window.addEventListener('beforeunload', trackViewDuration)
    window.addEventListener('pagehide', trackViewDuration)
  }

  return {
    isTracking,
    trackProductView,
    trackViewDuration,
    trackCampaignView,
    trackCampaignClick,
    trackSearch,
    trackSearchClick,
    trackCartEvent,
    getRecentViews,
    getTrendingProducts,
    getPopularSearches,
  }
}
