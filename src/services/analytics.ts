/**
 * Analytics Service
 * Google Analytics 4 integration
 */

interface AnalyticsConfig {
  measurementId: string
  debug?: boolean
}

interface PageViewParams {
  page_title?: string
  page_location?: string
  page_path?: string
}

interface EventParams {
  category?: string
  label?: string
  value?: number
  [key: string]: any
}

class AnalyticsService {
  private initialized = false
  private config: AnalyticsConfig | null = null
  private queue: Array<() => void> = []

  /**
   * Initialize Google Analytics
   */
  init(config: AnalyticsConfig) {
    if (this.initialized) {
      console.warn('Analytics already initialized')
      return
    }

    this.config = config

    // Load gtag.js script
    const script = document.createElement('script')
    script.async = true
    script.src = `https://www.googletagmanager.com/gtag/js?id=${config.measurementId}`
    document.head.appendChild(script)

    // Initialize dataLayer
    window.dataLayer = window.dataLayer || []
    this.gtag('js', new Date())
    this.gtag('config', config.measurementId, {
      send_page_view: false, // We'll manually track page views
      debug_mode: config.debug || false
    })

    this.initialized = true

    // Process queued events
    this.queue.forEach(fn => fn())
    this.queue = []

    console.log('✅ Analytics initialized:', config.measurementId)
  }

  /**
   * Google Analytics gtag function
   */
  private gtag(...args: any[]) {
    if (typeof window === 'undefined') return
    window.dataLayer = window.dataLayer || []
    window.dataLayer.push(arguments)
  }

  /**
   * Track page view
   */
  pageView(params: PageViewParams = {}) {
    if (!this.initialized) {
      this.queue.push(() => this.pageView(params))
      return
    }

    this.gtag('event', 'page_view', {
      page_title: params.page_title || document.title,
      page_location: params.page_location || window.location.href,
      page_path: params.page_path || window.location.pathname,
      ...params
    })
  }

  /**
   * Track custom event
   */
  event(eventName: string, params: EventParams = {}) {
    if (!this.initialized) {
      this.queue.push(() => this.event(eventName, params))
      return
    }

    this.gtag('event', eventName, params)
  }

  /**
   * Track user properties
   */
  setUserProperties(properties: Record<string, any>) {
    if (!this.initialized) {
      this.queue.push(() => this.setUserProperties(properties))
      return
    }

    this.gtag('set', 'user_properties', properties)
  }

  /**
   * Set user ID
   */
  setUserId(userId: string | number) {
    if (!this.initialized) {
      this.queue.push(() => this.setUserId(userId))
      return
    }

    this.gtag('set', { user_id: userId })
  }

  /**
   * Track e-commerce events
   */
  ecommerce = {
    /**
     * View item
     */
    viewItem: (item: any) => {
      this.event('view_item', {
        currency: 'TRY',
        value: item.price,
        items: [{
          item_id: item.id,
          item_name: item.name,
          item_category: item.category,
          price: item.price,
          quantity: 1
        }]
      })
    },

    /**
     * Add to cart
     */
    addToCart: (item: any, quantity: number = 1) => {
      this.event('add_to_cart', {
        currency: 'TRY',
        value: item.price * quantity,
        items: [{
          item_id: item.id,
          item_name: item.name,
          item_category: item.category,
          price: item.price,
          quantity
        }]
      })
    },

    /**
     * Remove from cart
     */
    removeFromCart: (item: any, quantity: number = 1) => {
      this.event('remove_from_cart', {
        currency: 'TRY',
        value: item.price * quantity,
        items: [{
          item_id: item.id,
          item_name: item.name,
          price: item.price,
          quantity
        }]
      })
    },

    /**
     * Begin checkout
     */
    beginCheckout: (items: any[], value: number) => {
      this.event('begin_checkout', {
        currency: 'TRY',
        value,
        items: items.map(item => ({
          item_id: item.id,
          item_name: item.name,
          item_category: item.category,
          price: item.price,
          quantity: item.quantity
        }))
      })
    },

    /**
     * Purchase
     */
    purchase: (transactionId: string, items: any[], value: number, tax?: number, shipping?: number) => {
      this.event('purchase', {
        transaction_id: transactionId,
        currency: 'TRY',
        value,
        tax: tax || 0,
        shipping: shipping || 0,
        items: items.map(item => ({
          item_id: item.id,
          item_name: item.name,
          item_category: item.category,
          price: item.price,
          quantity: item.quantity
        }))
      })
    },

    /**
     * Refund
     */
    refund: (transactionId: string, value: number) => {
      this.event('refund', {
        transaction_id: transactionId,
        currency: 'TRY',
        value
      })
    }
  }

  /**
   * Track search
   */
  search(searchTerm: string, results?: number) {
    this.event('search', {
      search_term: searchTerm,
      ...(results !== undefined && { results })
    })
  }

  /**
   * Track login
   */
  login(method: string = 'email') {
    this.event('login', { method })
  }

  /**
   * Track signup
   */
  signup(method: string = 'email') {
    this.event('sign_up', { method })
  }

  /**
   * Track share
   */
  share(contentType: string, itemId: string) {
    this.event('share', {
      content_type: contentType,
      item_id: itemId
    })
  }

  /**
   * Track exception/error
   */
  exception(description: string, fatal: boolean = false) {
    this.event('exception', {
      description,
      fatal
    })
  }

  /**
   * Track timing
   */
  timing(name: string, value: number, category?: string) {
    this.event('timing_complete', {
      name,
      value,
      event_category: category
    })
  }
}

// Export singleton instance
export const analytics = new AnalyticsService()

// Extend Window interface for TypeScript
declare global {
  interface Window {
    dataLayer: any[]
  }
}

export default AnalyticsService
