import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
// @ts-ignore - JS module without type definitions
import api from '@/services/api'

interface NotificationPreferences {
  push_enabled: boolean
  email_enabled: boolean
  sms_enabled: boolean
  topics: {
    orders: boolean
    campaigns: boolean
    promotions: boolean
    news: boolean
  }
}

export function usePushNotification() {
  const isSupported = ref(false)
  const isSubscribed = ref(false)
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const permission = ref<NotificationPermission>('default')
  const preferences = ref<NotificationPreferences | null>(null)

  const authStore = useAuthStore()

  // Check if push notifications are supported
  const checkSupport = () => {
    isSupported.value = 'Notification' in window && 'serviceWorker' in navigator && 'PushManager' in window
    if ('Notification' in window) {
      permission.value = Notification.permission
    }
    return isSupported.value
  }

  // Request notification permission
  const requestPermission = async (): Promise<boolean> => {
    if (!isSupported.value) {
      error.value = 'Push bildirimleri bu tarayıcıda desteklenmiyor'
      return false
    }

    try {
      const result = await Notification.requestPermission()
      permission.value = result
      return result === 'granted'
    } catch (err: any) {
      error.value = err.message || 'İzin alınamadı'
      return false
    }
  }

  // Subscribe to push notifications
  const subscribe = async (): Promise<boolean> => {
    if (!isSupported.value || permission.value !== 'granted') {
      const granted = await requestPermission()
      if (!granted) return false
    }

    isLoading.value = true
    error.value = null

    try {
      // Get VAPID public key from server
      const vapidResponse = await api.get('/api/push-notifications/vapid-key')
      const vapidPublicKey = vapidResponse.data.vapid_public_key

      if (!vapidPublicKey) {
        throw new Error('VAPID key not configured')
      }

      // Register service worker if not already registered
      const registration = await navigator.serviceWorker.ready

      // Subscribe to push
      const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(vapidPublicKey) as BufferSource,
      })

      // Get subscription JSON
      const subscriptionJSON = subscription.toJSON()

      // Send to server
      await api.post('/api/push-notifications/register', {
        token: JSON.stringify(subscriptionJSON),
        platform: 'web',
      })

      isSubscribed.value = true
      return true
    } catch (err: any) {
      error.value = err.message || 'Abone olunamadı'
      return false
    } finally {
      isLoading.value = false
    }
  }

  // Unsubscribe from push notifications
  const unsubscribe = async (): Promise<boolean> => {
    isLoading.value = true
    error.value = null

    try {
      const registration = await navigator.serviceWorker.ready
      const subscription = await registration.pushManager.getSubscription()

      if (subscription) {
        // Unsubscribe locally
        await subscription.unsubscribe()

        // Remove from server
        await api.post('/api/push-notifications/remove', {
          token: JSON.stringify(subscription.toJSON()),
        })
      }

      isSubscribed.value = false
      return true
    } catch (err: any) {
      error.value = err.message || 'Abonelik iptal edilemedi'
      return false
    } finally {
      isLoading.value = false
    }
  }

  // Subscribe to a topic
  const subscribeToTopic = async (topic: string): Promise<boolean> => {
    try {
      await api.post('/api/push-notifications/subscribe-topic', { topic })
      return true
    } catch (err: any) {
      error.value = err.message || 'Topic aboneliği başarısız'
      return false
    }
  }

  // Unsubscribe from a topic
  const unsubscribeFromTopic = async (topic: string): Promise<boolean> => {
    try {
      await api.post('/api/push-notifications/unsubscribe-topic', { topic })
      return true
    } catch (err: any) {
      error.value = err.message || 'Topic aboneliği iptal edilemedi'
      return false
    }
  }

  // Get notification preferences
  const getPreferences = async (): Promise<NotificationPreferences | null> => {
    try {
      const response = await api.get('/api/push-notifications/preferences')
      preferences.value = response.data.preferences
      return preferences.value
    } catch (err: any) {
      error.value = err.message || 'Tercihler alınamadı'
      return null
    }
  }

  // Update notification preferences
  const updatePreferences = async (newPreferences: Partial<NotificationPreferences>): Promise<boolean> => {
    try {
      const response = await api.put('/api/push-notifications/preferences', newPreferences)
      preferences.value = response.data.preferences
      return true
    } catch (err: any) {
      error.value = err.message || 'Tercihler güncellenemedi'
      return false
    }
  }

  // Check if already subscribed
  const checkSubscription = async (): Promise<boolean> => {
    if (!isSupported.value) return false

    try {
      const registration = await navigator.serviceWorker.ready
      const subscription = await registration.pushManager.getSubscription()
      isSubscribed.value = !!subscription
      return isSubscribed.value
    } catch {
      return false
    }
  }

  // Show a local notification (for testing)
  const showLocalNotification = (title: string, options?: NotificationOptions) => {
    if (permission.value !== 'granted') {
      console.warn('Notification permission not granted')
      return
    }

    new Notification(title, {
      icon: '/favicon.ico',
      badge: '/favicon.ico',
      ...options,
    })
  }

  // Helper function to convert VAPID key
  const urlBase64ToUint8Array = (base64String: string): Uint8Array => {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/')
    const rawData = window.atob(base64)
    const outputArray = new Uint8Array(rawData.length)

    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i)
    }

    return outputArray
  }

  // Initialize on mount
  onMounted(() => {
    checkSupport()
    if (isSupported.value && authStore.isAuthenticated) {
      checkSubscription()
    }
  })

  return {
    isSupported,
    isSubscribed,
    isLoading,
    error,
    permission,
    preferences,
    checkSupport,
    requestPermission,
    subscribe,
    unsubscribe,
    subscribeToTopic,
    unsubscribeFromTopic,
    getPreferences,
    updatePreferences,
    checkSubscription,
    showLocalNotification,
  }
}
