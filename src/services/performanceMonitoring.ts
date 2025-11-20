/**
 * Performance Monitoring Service
 * Track and report performance metrics
 */

import { analytics } from './analytics.js'
import { errorTracking } from './errorTracking.js'

export interface PerformanceMetric {
  name: string
  value: number
  rating: 'good' | 'needs-improvement' | 'poor'
  timestamp: Date
}

class PerformanceMonitoringService {
  private metrics: PerformanceMetric[] = []
  private observer: PerformanceObserver | null = null

  /**
   * Initialize performance monitoring
   */
  init() {
    // Monitor Web Vitals
    this.observeWebVitals()

    // Monitor navigation timing
    this.observeNavigationTiming()

    // Monitor resource timing
    this.observeResourceTiming()

    // Monitor long tasks
    this.observeLongTasks()

    console.log('✅ Performance monitoring initialized')
  }

  /**
   * Observe Core Web Vitals
   */
  private observeWebVitals() {
    // LCP - Largest Contentful Paint
    this.observeMetric('largest-contentful-paint', (entry: any) => {
      const value = entry.renderTime || entry.loadTime
      const rating = value <= 2500 ? 'good' : value <= 4000 ? 'needs-improvement' : 'poor'
      
      this.recordMetric('LCP', value, rating)
      analytics.timing('LCP', value, 'Web Vitals')
    })

    // FID - First Input Delay
    this.observeMetric('first-input', (entry: any) => {
      const value = entry.processingStart - entry.startTime
      const rating = value <= 100 ? 'good' : value <= 300 ? 'needs-improvement' : 'poor'
      
      this.recordMetric('FID', value, rating)
      analytics.timing('FID', value, 'Web Vitals')
    })

    // CLS - Cumulative Layout Shift
    let clsValue = 0
    this.observeMetric('layout-shift', (entry: any) => {
      if (!entry.hadRecentInput) {
        clsValue += entry.value
        const rating = clsValue <= 0.1 ? 'good' : clsValue <= 0.25 ? 'needs-improvement' : 'poor'
        
        this.recordMetric('CLS', clsValue, rating)
        analytics.timing('CLS', clsValue * 1000, 'Web Vitals')
      }
    })

    // INP - Interaction to Next Paint
    this.observeMetric('event', (entry: any) => {
      const value = entry.duration
      const rating = value <= 200 ? 'good' : value <= 500 ? 'needs-improvement' : 'poor'
      
      this.recordMetric('INP', value, rating)
    })
  }

  /**
   * Observe Navigation Timing
   */
  private observeNavigationTiming() {
    if (typeof window === 'undefined') return

    window.addEventListener('load', () => {
      setTimeout(() => {
        const navigation = performance.getEntriesByType('navigation')[0] as PerformanceNavigationTiming
        
        if (!navigation) return

        // DNS Lookup Time
        const dnsTime = navigation.domainLookupEnd - navigation.domainLookupStart
        this.recordMetric('DNS Lookup', dnsTime, this.getRating(dnsTime, 50, 100))

        // TCP Connection Time
        const tcpTime = navigation.connectEnd - navigation.connectStart
        this.recordMetric('TCP Connection', tcpTime, this.getRating(tcpTime, 100, 300))

        // TTFB - Time to First Byte
        const ttfb = navigation.responseStart - navigation.requestStart
        this.recordMetric('TTFB', ttfb, this.getRating(ttfb, 600, 1500))
        analytics.timing('TTFB', ttfb, 'Navigation')

        // DOM Content Loaded
        const domContentLoaded = navigation.domContentLoadedEventEnd - navigation.domContentLoadedEventStart
        this.recordMetric('DOM Content Loaded', domContentLoaded, this.getRating(domContentLoaded, 1500, 2500))

        // Page Load Time
        const loadTime = navigation.loadEventEnd - navigation.loadEventStart
        this.recordMetric('Page Load', loadTime, this.getRating(loadTime, 2000, 4000))
        analytics.timing('Page Load', loadTime, 'Navigation')

        // DOM Interactive
        const domInteractive = navigation.domInteractive - navigation.fetchStart
        this.recordMetric('DOM Interactive', domInteractive, this.getRating(domInteractive, 1500, 3000))
      }, 0)
    })
  }

  /**
   * Observe Resource Timing
   */
  private observeResourceTiming() {
    this.observeMetric('resource', (entry: PerformanceResourceTiming) => {
      const duration = entry.duration

      // Track slow resources
      if (duration > 1000) {
        errorTracking.capturePerformance(
          `Slow Resource: ${entry.name}`,
          duration,
          1000,
          {
            extra: {
              type: entry.initiatorType,
              size: entry.transferSize,
              cached: entry.transferSize === 0
            }
          }
        )
      }

      // Track large resources
      if (entry.transferSize > 500000) { // 500KB
        errorTracking.captureMessage(
          `Large Resource: ${entry.name} (${Math.round(entry.transferSize / 1024)}KB)`,
          'low',
          {
            extra: {
              type: entry.initiatorType,
              size: entry.transferSize,
              duration
            }
          }
        )
      }
    })
  }

  /**
   * Observe Long Tasks
   */
  private observeLongTasks() {
    try {
      const observer = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
          const duration = entry.duration
          
          // Report long tasks (> 50ms)
          if (duration > 50) {
            errorTracking.capturePerformance(
              'Long Task',
              duration,
              50,
              {
                extra: {
                  name: entry.name,
                  startTime: entry.startTime
                }
              }
            )
          }
        }
      })

      observer.observe({ entryTypes: ['longtask'] })
    } catch (error) {
      // Long Tasks API not supported
    }
  }

  /**
   * Generic metric observer
   */
  private observeMetric(type: string, callback: (entry: any) => void) {
    try {
      const observer = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
          callback(entry)
        }
      })

      observer.observe({ type, buffered: true })
    } catch (error) {
      // Metric not supported
    }
  }

  /**
   * Record a performance metric
   */
  private recordMetric(name: string, value: number, rating: PerformanceMetric['rating']) {
    const metric: PerformanceMetric = {
      name,
      value: Math.round(value),
      rating,
      timestamp: new Date()
    }

    this.metrics.push(metric)

    // Log in development
    if (import.meta.env.DEV) {
      console.log(`📊 ${name}: ${metric.value}ms (${rating})`)
    }

    // Warn on poor performance
    if (rating === 'poor') {
      errorTracking.captureMessage(
        `Poor performance: ${name} = ${metric.value}ms`,
        'low',
        { extra: { metric: name, value: metric.value, rating } }
      )
    }
  }

  /**
   * Get performance rating
   */
  private getRating(value: number, goodThreshold: number, poorThreshold: number): PerformanceMetric['rating'] {
    if (value <= goodThreshold) return 'good'
    if (value <= poorThreshold) return 'needs-improvement'
    return 'poor'
  }

  /**
   * Measure custom timing
   */
  measure(name: string, startMark?: string, endMark?: string) {
    try {
      if (startMark && endMark) {
        performance.measure(name, startMark, endMark)
      } else {
        performance.measure(name)
      }

      const measures = performance.getEntriesByName(name, 'measure')
      if (measures.length > 0) {
        const duration = measures[measures.length - 1].duration
        analytics.timing(name, duration, 'Custom')
        return duration
      }
    } catch (error) {
      console.error('Performance measurement failed:', error)
    }
    return 0
  }

  /**
   * Mark a performance point
   */
  mark(name: string) {
    try {
      performance.mark(name)
    } catch (error) {
      console.error('Performance mark failed:', error)
    }
  }

  /**
   * Clear marks and measures
   */
  clearMarks(name?: string) {
    performance.clearMarks(name)
  }

  clearMeasures(name?: string) {
    performance.clearMeasures(name)
  }

  /**
   * Get all metrics
   */
  getMetrics(): PerformanceMetric[] {
    return [...this.metrics]
  }

  /**
   * Get metrics by name
   */
  getMetricsByName(name: string): PerformanceMetric[] {
    return this.metrics.filter(m => m.name === name)
  }

  /**
   * Get average metric value
   */
  getAverageMetric(name: string): number {
    const metrics = this.getMetricsByName(name)
    if (metrics.length === 0) return 0
    
    const sum = metrics.reduce((acc, m) => acc + m.value, 0)
    return Math.round(sum / metrics.length)
  }

  /**
   * Get performance summary
   */
  getSummary() {
    const summary: Record<string, any> = {}

    // Group metrics by name
    const metricNames = [...new Set(this.metrics.map(m => m.name))]
    
    metricNames.forEach(name => {
      const metrics = this.getMetricsByName(name)
      const latest = metrics[metrics.length - 1]
      
      summary[name] = {
        value: latest?.value || 0,
        rating: latest?.rating || 'good',
        average: this.getAverageMetric(name),
        count: metrics.length
      }
    })

    return summary
  }

  /**
   * Clear all metrics
   */
  clearMetrics() {
    this.metrics = []
  }

  /**
   * Disconnect observers
   */
  disconnect() {
    if (this.observer) {
      this.observer.disconnect()
      this.observer = null
    }
  }
}

// Export singleton instance
export const performanceMonitoring = new PerformanceMonitoringService()

export default PerformanceMonitoringService
