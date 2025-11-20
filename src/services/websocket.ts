/**
 * WebSocket Service
 * Real-time communication with Laravel Echo and Pusher
 */

// @ts-ignore - Types will be installed
import Echo from 'laravel-echo'
// @ts-ignore - Types will be installed
import Pusher from 'pusher-js'

// @ts-ignore
window.Pusher = Pusher

interface WebSocketConfig {
  broadcaster: 'pusher' | 'socket.io'
  key: string
  cluster?: string
  wsHost?: string
  wsPort?: number
  wssPort?: number
  forceTLS?: boolean
  encrypted?: boolean
  disableStats?: boolean
  enabledTransports?: string[]
  authEndpoint?: string
  auth?: {
    headers: Record<string, string>
  }
}

class WebSocketService {
  private echo: any = null
  private isConnected = false
  private reconnectAttempts = 0
  private maxReconnectAttempts = 5
  private reconnectDelay = 1000

  /**
   * Initialize WebSocket connection
   */
  init(config?: Partial<WebSocketConfig>) {
    if (this.echo) {
      console.log('WebSocket already initialized')
      return this.echo
    }

    const defaultConfig: WebSocketConfig = {
      broadcaster: 'pusher',
      key: import.meta.env.VITE_PUSHER_APP_KEY || 'local-key',
      cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
      wsHost: import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
      wsPort: Number(import.meta.env.VITE_PUSHER_PORT) || 6001,
      wssPort: Number(import.meta.env.VITE_PUSHER_PORT) || 6001,
      forceTLS: import.meta.env.VITE_PUSHER_SCHEME === 'https',
      encrypted: true,
      disableStats: true,
      enabledTransports: ['ws', 'wss'],
      authEndpoint: '/api/broadcasting/auth',
      auth: {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token') || ''}`
        }
      }
    }

    const finalConfig = { ...defaultConfig, ...config }

    try {
      this.echo = new Echo(finalConfig as any)
      this.isConnected = true
      this.reconnectAttempts = 0
      console.log('✅ WebSocket connected', finalConfig)
    } catch (error) {
      console.error('❌ WebSocket connection failed:', error)
      this.handleReconnect(finalConfig)
    }

    return this.echo
  }

  /**
   * Handle reconnection with exponential backoff
   */
  private handleReconnect(config: WebSocketConfig) {
    if (this.reconnectAttempts >= this.maxReconnectAttempts) {
      console.error('❌ Max reconnection attempts reached')
      return
    }

    this.reconnectAttempts++
    const delay = this.reconnectDelay * Math.pow(2, this.reconnectAttempts - 1)

    console.log(`🔄 Reconnecting in ${delay}ms (attempt ${this.reconnectAttempts}/${this.maxReconnectAttempts})`)

    setTimeout(() => {
      this.echo = null
      this.init(config)
    }, delay)
  }

  /**
   * Join a channel
   */
  channel(channelName: string) {
    if (!this.echo) {
      throw new Error('WebSocket not initialized. Call init() first.')
    }
    return this.echo.channel(channelName)
  }

  /**
   * Join a private channel
   */
  private(channelName: string) {
    if (!this.echo) {
      throw new Error('WebSocket not initialized. Call init() first.')
    }
    return this.echo.private(channelName)
  }

  /**
   * Join a presence channel
   */
  join(channelName: string) {
    if (!this.echo) {
      throw new Error('WebSocket not initialized. Call init() first.')
    }
    return this.echo.join(channelName)
  }

  /**
   * Leave a channel
   */
  leave(channelName: string) {
    if (!this.echo) return
    this.echo.leave(channelName)
  }

  /**
   * Disconnect WebSocket
   */
  disconnect() {
    if (this.echo) {
      this.echo.disconnect()
      this.echo = null
      this.isConnected = false
      console.log('🔌 WebSocket disconnected')
    }
  }

  /**
   * Check if WebSocket is connected
   */
  get connected() {
    return this.isConnected
  }

  /**
   * Get Echo instance
   */
  getEcho() {
    return this.echo
  }

  /**
   * Update auth headers (useful after login/logout)
   */
  updateAuthHeaders(token?: string) {
    if (!this.echo) return

    const authToken = token || localStorage.getItem('token')
    if (authToken) {
      // @ts-ignore
      this.echo.connector.pusher.config.auth = {
        headers: {
          Authorization: `Bearer ${authToken}`
        }
      }
    }
  }

  /**
   * Subscribe to user notifications
   */
  subscribeToUserNotifications(userId: number, callback: (notification: any) => void) {
    return this.private(`user.${userId}`)
      .listen('.notification', callback)
  }

  /**
   * Subscribe to order updates
   */
  subscribeToOrderUpdates(orderId: number, callback: (order: any) => void) {
    return this.private(`order.${orderId}`)
      .listen('.OrderStatusUpdated', callback)
  }

  /**
   * Subscribe to chat messages
   */
  subscribeToChatRoom(roomId: string, callbacks: {
    onMessage?: (message: any) => void
    onUserJoined?: (user: any) => void
    onUserLeft?: (user: any) => void
    onTyping?: (user: any) => void
  }) {
    const channel = this.join(`chat.${roomId}`)

    if (callbacks.onMessage) {
      channel.listen('.MessageSent', callbacks.onMessage)
    }

    if (callbacks.onUserJoined) {
      channel.here((users: any) => {
        console.log('Users in chat:', users)
      }).joining((user: any) => {
        if (callbacks.onUserJoined) {
          callbacks.onUserJoined(user)
        }
      })
    }

    if (callbacks.onUserLeft) {
      channel.leaving((user: any) => {
        if (callbacks.onUserLeft) {
          callbacks.onUserLeft(user)
        }
      })
    }

    if (callbacks.onTyping) {
      channel.listenForWhisper('typing', callbacks.onTyping)
    }

    return channel
  }

  /**
   * Whisper to presence channel (e.g., typing indicator)
   */
  whisper(channelName: string, eventName: string, data: any) {
    if (!this.echo) return
    const channel = this.echo.join(channelName)
    channel.whisper(eventName, data)
  }
}

// Export singleton instance
export const websocket = new WebSocketService()

// Export class for testing
export default WebSocketService
