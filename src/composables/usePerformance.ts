import { ref, onMounted, onUnmounted } from 'vue'

interface PerformanceMetrics {
  fcp?: number // First Contentful Paint
  lcp?: number // Largest Contentful Paint
  fid?: number // First Input Delay
  cls?: number // Cumulative Layout Shift
  ttfb?: number // Time to First Byte
}

export function usePerformance() {
  const metrics = ref<PerformanceMetrics>({})

  const measurePageLoad = () => {
    if ('PerformanceObserver' in window) {
      // Measure FCP
      const fcpObserver = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
          if (entry.name === 'first-contentful-paint') {
            metrics.value.fcp = entry.startTime
          }
        }
      })
      fcpObserver.observe({ entryTypes: ['paint'] })

      // Measure LCP
      const lcpObserver = new PerformanceObserver((list) => {
        const entries = list.getEntries()
        const lastEntry = entries[entries.length - 1] as any
        metrics.value.lcp = lastEntry.renderTime || lastEntry.loadTime
      })
      lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] })

      // Measure FID
      const fidObserver = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
          metrics.value.fid = (entry as any).processingStart - entry.startTime
        }
      })
      fidObserver.observe({ entryTypes: ['first-input'] })

      // Measure CLS
      let clsValue = 0
      const clsObserver = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
          if (!(entry as any).hadRecentInput) {
            clsValue += (entry as any).value
            metrics.value.cls = clsValue
          }
        }
      })
      clsObserver.observe({ entryTypes: ['layout-shift'] })
    }

    // Measure TTFB
    if (performance.timing) {
      const ttfb = performance.timing.responseStart - performance.timing.requestStart
      metrics.value.ttfb = ttfb
    }
  }

  const logMetrics = () => {
    console.log('Performance Metrics:', metrics.value)
  }

  const sendToAnalytics = () => {
    // In production, send metrics to analytics service
    // Example: analytics.track('performance_metrics', metrics.value)
    if (import.meta.env.PROD) {
      // Send to backend or analytics
      console.log('Sending metrics to analytics:', metrics.value)
    }
  }

  onMounted(() => {
    measurePageLoad()
    
    // Log metrics after page is fully loaded
    window.addEventListener('load', () => {
      setTimeout(() => {
        logMetrics()
        sendToAnalytics()
      }, 2000)
    })
  })

  return {
    metrics,
    logMetrics,
    sendToAnalytics
  }
}

// Custom timing utility
export function useComponentTiming(componentName: string) {
  const startTime = performance.now()

  const measureRenderTime = () => {
    const endTime = performance.now()
    const renderTime = endTime - startTime
    console.log(`${componentName} render time: ${renderTime.toFixed(2)}ms`)
    return renderTime
  }

  onMounted(() => {
    measureRenderTime()
  })

  return { measureRenderTime }
}
