import { ref, onMounted, onUnmounted } from 'vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import type { Transport } from 'pusher-js/types/src/core/config'

// Global Echo instance
let echoInstance: Echo<any> | null = null

interface WebSocketConfig {
  key: string
  cluster: string
  wsHost?: string
  wsPort?: number
  forceTLS?: boolean
  enabledTransports?: Transport[]
}

interface ChannelSubscription {
  channel: string
  events: { [event: string]: (data: any) => void }
}

export function useWebSocket() {
  const isConnected = ref(false)
  const connectionError = ref<string | null>(null)
  const subscriptions = ref<ChannelSubscription[]>([])

  const initializeEcho = (config: WebSocketConfig, authToken?: string) => {
    if (echoInstance) {
      return echoInstance
    }

    // @ts-ignore
    window.Pusher = Pusher

    echoInstance = new Echo({
      broadcaster: 'pusher',
      key: config.key,
      cluster: config.cluster,
      wsHost: config.wsHost || undefined,
      wsPort: config.wsPort || undefined,
      forceTLS: config.forceTLS ?? true,
      enabledTransports: config.enabledTransports || ['ws', 'wss'],
      authEndpoint: '/api/broadcasting/auth',
      auth: {
        headers: {
          Authorization: authToken ? `Bearer ${authToken}` : '',
        },
      },
    })

    // Connection events
    echoInstance.connector.pusher.connection.bind('connected', () => {
      isConnected.value = true
      connectionError.value = null
      console.log('WebSocket connected')
    })

    echoInstance.connector.pusher.connection.bind('disconnected', () => {
      isConnected.value = false
      console.log('WebSocket disconnected')
    })

    echoInstance.connector.pusher.connection.bind('error', (error: any) => {
      connectionError.value = error.message || 'Connection error'
      console.error('WebSocket error:', error)
    })

    return echoInstance
  }

  const getEcho = () => echoInstance

  // Subscribe to a public channel
  const subscribePublic = (channelName: string, events: { [event: string]: (data: any) => void }) => {
    if (!echoInstance) {
      console.error('Echo not initialized')
      return null
    }

    const channel = echoInstance.channel(channelName)

    Object.entries(events).forEach(([eventName, callback]) => {
      channel.listen(`.${eventName}`, callback)
    })

    subscriptions.value.push({ channel: channelName, events })

    return channel
  }

  // Subscribe to a private channel
  const subscribePrivate = (channelName: string, events: { [event: string]: (data: any) => void }) => {
    if (!echoInstance) {
      console.error('Echo not initialized')
      return null
    }

    const channel = echoInstance.private(channelName)

    Object.entries(events).forEach(([eventName, callback]) => {
      channel.listen(`.${eventName}`, callback)
    })

    subscriptions.value.push({ channel: `private-${channelName}`, events })

    return channel
  }

  // Subscribe to a presence channel
  const subscribePresence = (
    channelName: string,
    events: { [event: string]: (data: any) => void },
    presenceCallbacks?: {
      here?: (users: any[]) => void
      joining?: (user: any) => void
      leaving?: (user: any) => void
    }
  ) => {
    if (!echoInstance) {
      console.error('Echo not initialized')
      return null
    }

    const channel = echoInstance.join(channelName)

    Object.entries(events).forEach(([eventName, callback]) => {
      channel.listen(`.${eventName}`, callback)
    })

    if (presenceCallbacks) {
      if (presenceCallbacks.here) channel.here(presenceCallbacks.here)
      if (presenceCallbacks.joining) channel.joining(presenceCallbacks.joining)
      if (presenceCallbacks.leaving) channel.leaving(presenceCallbacks.leaving)
    }

    subscriptions.value.push({ channel: `presence-${channelName}`, events })

    return channel
  }

  // Unsubscribe from a channel
  const unsubscribe = (channelName: string) => {
    if (!echoInstance) return

    echoInstance.leave(channelName)
    subscriptions.value = subscriptions.value.filter(s => s.channel !== channelName)
  }

  // Unsubscribe from all channels
  const unsubscribeAll = () => {
    if (!echoInstance) return

    subscriptions.value.forEach(sub => {
      echoInstance?.leave(sub.channel)
    })
    subscriptions.value = []
  }

  // Disconnect Echo
  const disconnect = () => {
    if (echoInstance) {
      echoInstance.disconnect()
      echoInstance = null
      isConnected.value = false
    }
  }

  return {
    isConnected,
    connectionError,
    subscriptions,
    initializeEcho,
    getEcho,
    subscribePublic,
    subscribePrivate,
    subscribePresence,
    unsubscribe,
    unsubscribeAll,
    disconnect,
  }
}

// Specific channel hooks for common use cases
export function useOrderChannel(userId: number, onStatusUpdate: (data: any) => void) {
  const { subscribePrivate, unsubscribe } = useWebSocket()

  onMounted(() => {
    subscribePrivate(`orders.${userId}`, {
      'order.status.updated': onStatusUpdate,
    })
  })

  onUnmounted(() => {
    unsubscribe(`orders.${userId}`)
  })
}

export function useFoodOrderChannel(userId: number, onStatusUpdate: (data: any) => void) {
  const { subscribePrivate, unsubscribe } = useWebSocket()

  onMounted(() => {
    subscribePrivate(`food-orders.${userId}`, {
      'food.order.status.updated': onStatusUpdate,
    })
  })

  onUnmounted(() => {
    unsubscribe(`food-orders.${userId}`)
  })
}

export function useReservationChannel(userId: number, onStatusUpdate: (data: any) => void) {
  const { subscribePrivate, unsubscribe } = useWebSocket()

  onMounted(() => {
    subscribePrivate(`reservations.${userId}`, {
      'reservation.status.updated': onStatusUpdate,
    })
  })

  onUnmounted(() => {
    unsubscribe(`reservations.${userId}`)
  })
}

export function useTransferChannel(userId: number, onStatusUpdate: (data: any) => void) {
  const { subscribePrivate, unsubscribe } = useWebSocket()

  onMounted(() => {
    subscribePrivate(`transfers.${userId}`, {
      'transfer.status.updated': onStatusUpdate,
    })
  })

  onUnmounted(() => {
    unsubscribe(`transfers.${userId}`)
  })
}

export function useNotificationChannel(userId: number, onNewNotification: (data: any) => void) {
  const { subscribePrivate, unsubscribe } = useWebSocket()

  onMounted(() => {
    subscribePrivate(`notifications.${userId}`, {
      'new.notification': onNewNotification,
    })
  })

  onUnmounted(() => {
    unsubscribe(`notifications.${userId}`)
  })
}

export function useAdminDashboardChannel(onStatsUpdate: (data: any) => void) {
  const { subscribePublic, unsubscribe } = useWebSocket()

  onMounted(() => {
    subscribePublic('admin.dashboard', {
      'dashboard.stats.updated': onStatsUpdate,
    })
  })

  onUnmounted(() => {
    unsubscribe('admin.dashboard')
  })
}
