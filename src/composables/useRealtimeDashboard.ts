import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

export interface DashboardMetrics {
  total_orders: number
  today_revenue: number
  active_campaigns: number
  pending_exports: number
  conversion_rate: number
  avg_order_value: number
}

export function useRealtimeDashboard() {
  const metrics = ref<DashboardMetrics>({
    total_orders: 0,
    today_revenue: 0,
    active_campaigns: 0,
    pending_exports: 0,
    conversion_rate: 0,
    avg_order_value: 0,
  })

  const loading = ref(false)
  const error = ref<string | null>(null)
  let intervalId: number | null = null

  const fetchMetrics = async () => {
    try {
      loading.value = true
      const { data } = await axios.get<DashboardMetrics>('/api/dashboard/realtime-metrics')
      metrics.value = data
      error.value = null
    } catch (err) {
      error.value = 'Metrikler yüklenemedi'
      console.error('Dashboard metrics error:', err)
    } finally {
      loading.value = false
    }
  }

  const startPolling = (intervalMs = 30000) => {
    fetchMetrics()
    intervalId = window.setInterval(fetchMetrics, intervalMs)
  }

  const stopPolling = () => {
    if (intervalId) {
      clearInterval(intervalId)
      intervalId = null
    }
  }

  onMounted(() => startPolling())
  onUnmounted(() => stopPolling())

  return {
    metrics,
    loading,
    error,
    fetchMetrics,
    startPolling,
    stopPolling,
  }
}
