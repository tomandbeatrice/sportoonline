import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export interface NotificationAction {
  label: string
  action: string
  variant?: 'primary' | 'secondary' | 'danger' | 'success'
}

export interface SmartNotification {
  id: string
  type: 'order' | 'system' | 'alert' | 'promotion' | 'security'
  priority: 'high' | 'medium' | 'low'
  title: string
  message: string
  time: Date
  read: boolean
  group?: string
  actions?: NotificationAction[]
  metadata?: any
}

export const useSmartNotificationStore = defineStore('smartNotification', () => {
  const notifications = ref<SmartNotification[]>([])
  const isLoading = ref(false)

  // Fetch notifications from API
  const fetchNotifications = async () => {
    isLoading.value = true
    try {
      const { data } = await axios.get('/api/notifications')
      notifications.value = (data.data || data || []).map((n: any) => ({
        id: String(n.id),
        type: n.type || 'system',
        priority: n.priority || 'medium',
        title: n.title || n.data?.title || 'Bildirim',
        message: n.message || n.data?.message || '',
        time: new Date(n.created_at || n.time),
        read: !!n.read_at,
        metadata: n.data,
        actions: n.actions || []
      }))
    } catch (error) {
      console.error('Failed to fetch notifications:', error)
      // Keep demo data for development
      if (notifications.value.length === 0) {
        notifications.value = [
          {
            id: '1',
            type: 'alert',
            priority: 'high',
            title: 'Stok Uyarısı',
            message: 'Bazı ürünler kritik seviyenin altına düştü.',
            time: new Date(),
            read: false,
            actions: [
              { label: 'Stok Ekle', action: 'restock', variant: 'primary' },
              { label: 'Yoksay', action: 'dismiss', variant: 'secondary' }
            ]
          }
        ]
      }
    } finally {
      isLoading.value = false
    }
  }

  // Fetch unread notifications only
  const fetchUnread = async () => {
    try {
      const { data } = await axios.get('/api/notifications/unread')
      return data.count || (data.data || []).length
    } catch {
      return 0
    }
  }

  const unreadCount = computed(() => notifications.value.filter(n => !n.read).length)
  
  const highPriorityCount = computed(() => notifications.value.filter(n => !n.read && n.priority === 'high').length)

  const groupedNotifications = computed(() => {
    // Simple grouping logic could go here
    return notifications.value.sort((a, b) => {
      // Sort by priority first (High > Medium > Low)
      const priorityWeight = { high: 3, medium: 2, low: 1 }
      if (priorityWeight[a.priority] !== priorityWeight[b.priority]) {
        return priorityWeight[b.priority] - priorityWeight[a.priority]
      }
      // Then by time
      return b.time.getTime() - a.time.getTime()
    })
  })

  const addNotification = (notification: Partial<SmartNotification>) => {
    notifications.value.unshift({
      id: Math.random().toString(36).substring(7),
      time: new Date(),
      read: false,
      priority: 'medium',
      type: 'system',
      title: 'Bildirim',
      message: '',
      ...notification
    } as SmartNotification)
  }

  const markAsRead = async (id: string) => {
    const notification = notifications.value.find(n => n.id === id)
    if (notification) {
      notification.read = true
      try {
        await axios.post(`/api/notifications/${id}/read`)
      } catch (error) {
        console.error('Failed to mark as read:', error)
      }
    }
  }

  const markAllAsRead = async () => {
    notifications.value.forEach(n => n.read = true)
    try {
      await axios.post('/api/notifications/mark-all-read')
    } catch (error) {
      console.error('Failed to mark all as read:', error)
    }
  }

  const removeNotification = async (id: string) => {
    notifications.value = notifications.value.filter(n => n.id !== id)
    try {
      await axios.delete(`/api/notifications/${id}`)
    } catch (error) {
      console.error('Failed to delete notification:', error)
    }
  }

  const handleAction = (id: string, action: string) => {
    console.log(`Action ${action} triggered for notification ${id}`)
    // Implement action logic here (e.g., router push, api call)
    markAsRead(id)
    
    if (action === 'dismiss') {
      removeNotification(id)
    }
  }

  return {
    notifications,
    unreadCount,
    highPriorityCount,
    groupedNotifications,
    isLoading,
    fetchNotifications,
    fetchUnread,
    addNotification,
    markAsRead,
    markAllAsRead,
    removeNotification,
    handleAction
  }
})
