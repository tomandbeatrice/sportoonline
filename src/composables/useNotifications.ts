import { ref, onMounted, onUnmounted } from 'vue'
import { useToast, POSITION } from 'vue-toastification'

// Echo will be initialized after npm install completes
let Echo: any = null

export interface Notification {
  id: string
  type: 'order' | 'payment' | 'status' | 'system'
  title: string
  message: string
  data?: any
  read: boolean
  timestamp: string
}

export function useNotifications(userId?: number, role?: string) {
  const toast = useToast()
  const notifications = ref<Notification[]>([])
  const unreadCount = ref(0)

  const addNotification = (notification: Notification) => {
    notifications.value.unshift(notification)
    unreadCount.value++
    
    // Show toast
    const toastType = notification.type === 'order' ? 'success' : 
                      notification.type === 'payment' ? 'info' :
                      notification.type === 'status' ? 'warning' : 'info'
    
    toast[toastType](notification.message, {
      timeout: 5000,
      position: POSITION.TOP_RIGHT
    })
  }

  const markAsRead = (id: string) => {
    const notification = notifications.value.find(n => n.id === id)
    if (notification && !notification.read) {
      notification.read = true
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    }
  }

  const markAllAsRead = () => {
    notifications.value.forEach(n => n.read = true)
    unreadCount.value = 0
  }

  const clearAll = () => {
    notifications.value = []
    unreadCount.value = 0
  }

  onMounted(async () => {
    if (!userId || !role) {
      console.warn('⚠️ Notifications: userId or role not provided')
      return
    }

    // Try to load Echo (will fail gracefully if not installed yet)
    try {
      const echoModule = await import('@/bootstrap/echo' as any)
      Echo = echoModule.default
      
      console.log(`🔔 Setting up notifications for ${role} #${userId}`)

      // Admin channel
      if (role === 'admin') {
        Echo.private('admin')
          .listen('OrderCreated', (e: any) => {
            addNotification({
              id: `order-${e.id}-${Date.now()}`,
              type: 'order',
              title: 'Yeni Sipariş',
              message: e.message,
              data: e,
              read: false,
              timestamp: e.created_at
            })
          })
          .listen('PaymentReceived', (e: any) => {
            addNotification({
              id: `payment-${e.order_id}-${Date.now()}`,
              type: 'payment',
              title: 'Ödeme Alındı',
              message: e.message,
              data: e,
              read: false,
              timestamp: e.timestamp
            })
          })
          .listen('OrderStatusChanged', (e: any) => {
            addNotification({
              id: `status-${e.id}-${Date.now()}`,
              type: 'status',
              title: 'Sipariş Durumu',
              message: e.message,
              data: e,
              read: false,
              timestamp: e.updated_at
            })
          })
      }

      // Seller channel
      if (role === 'seller') {
        Echo.private(`seller.${userId}`)
          .listen('OrderCreated', (e: any) => {
            addNotification({
              id: `order-${e.id}-${Date.now()}`,
              type: 'order',
              title: 'Yeni Sipariş',
              message: e.message,
              data: e,
              read: false,
              timestamp: e.created_at
            })
          })
          .listen('PaymentReceived', (e: any) => {
            addNotification({
              id: `payment-${e.order_id}-${Date.now()}`,
              type: 'payment',
              title: 'Ödeme Alındı',
              message: e.message,
              data: e,
              read: false,
              timestamp: e.timestamp
            })
          })
      }

      // Buyer channel
      if (role === 'buyer') {
        Echo.private(`user.${userId}`)
          .listen('OrderStatusChanged', (e: any) => {
            addNotification({
              id: `status-${e.id}-${Date.now()}`,
              type: 'status',
              title: 'Sipariş Durumu Güncellendi',
              message: e.message,
              data: e,
              read: false,
              timestamp: e.updated_at
            })
          })
      }

      // Public orders channel
      Echo.channel('orders')
        .listen('OrderCreated', (e: any) => {
          console.log('📦 Public order created:', e)
        })
    } catch (error) {
      console.warn('⚠️ Echo not loaded yet (install in progress)', error)
    }
  })

  onUnmounted(() => {
    if (Echo) {
      Echo.leaveChannel('admin')
      Echo.leaveChannel(`seller.${userId}`)
      Echo.leaveChannel(`user.${userId}`)
      Echo.leaveChannel('orders')
    }
  })

  return {
    notifications,
    unreadCount,
    addNotification,
    markAsRead,
    markAllAsRead,
    clearAll
  }
}
