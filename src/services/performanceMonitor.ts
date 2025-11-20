import { ref } from 'vue'

export interface PerformanceMetric {
  name: string
  duration: number
  timestamp: number
}

class PerformanceMonitor {
  private metrics: PerformanceMetric[] = []
  private observers: ((metrics: PerformanceMetric[]) => void)[] = []

  trackApiCall(endpoint: string, duration: number) {
    this.addMetric({
      name: `API: ${endpoint}`,
      duration,
      timestamp: Date.now(),
    })
  }

  trackComponentRender(componentName: string, duration: number) {
    this.addMetric({
      name: `Render: ${componentName}`,
      duration,
      timestamp: Date.now(),
    })
  }

  trackPageLoad(pageName: string, duration: number) {
    this.addMetric({
      name: `Page: ${pageName}`,
      duration,
      timestamp: Date.now(),
    })
  }

  private addMetric(metric: PerformanceMetric) {
    this.metrics.push(metric)
    
    // Keep only last 100 metrics
    if (this.metrics.length > 100) {
      this.metrics.shift()
    }

    this.notifyObservers()
  }

  subscribe(callback: (metrics: PerformanceMetric[]) => void) {
    this.observers.push(callback)
    return () => {
      this.observers = this.observers.filter(obs => obs !== callback)
    }
  }

  private notifyObservers() {
    this.observers.forEach(observer => observer([...this.metrics]))
  }

  getMetrics() {
    return [...this.metrics]
  }

  getAverageDuration(filter?: string) {
    const filtered = filter
      ? this.metrics.filter(m => m.name.includes(filter))
      : this.metrics

    if (filtered.length === 0) return 0

    const sum = filtered.reduce((acc, m) => acc + m.duration, 0)
    return Math.round(sum / filtered.length)
  }

  clear() {
    this.metrics = []
    this.notifyObservers()
  }
}

export const performanceMonitor = new PerformanceMonitor()

export function usePerformanceTracking() {
  const metrics = ref<PerformanceMetric[]>([])

  const startTracking = () => {
    const unsubscribe = performanceMonitor.subscribe(newMetrics => {
      metrics.value = newMetrics
    })

    return unsubscribe
  }

  const trackOperation = async <T>(
    name: string,
    operation: () => Promise<T>
  ): Promise<T> => {
    const start = performance.now()
    try {
      return await operation()
    } finally {
      const duration = performance.now() - start
      performanceMonitor.trackApiCall(name, duration)
    }
  }

  return {
    metrics,
    startTracking,
    trackOperation,
    getAverageDuration: performanceMonitor.getAverageDuration.bind(performanceMonitor),
    clear: performanceMonitor.clear.bind(performanceMonitor),
  }
}
