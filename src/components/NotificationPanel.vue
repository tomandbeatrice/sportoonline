<template>
  <div class="relative">
    <!-- Notification Bell -->
    <button
      @click="togglePanel"
      class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg transition-colors"
      :aria-label="`${unreadCount} okunmamış bildirim`"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      
      <!-- Badge -->
      <span
        v-if="unreadCount > 0"
        class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Notification Panel -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 z-50 mt-2 w-96 max-w-sm bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5"
        @click.stop
      >
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900">Bildirimler</h3>
          <div class="flex gap-2">
            <button
              v-if="unreadCount > 0"
              @click="handleMarkAllAsRead"
              class="text-sm text-blue-600 hover:text-blue-700"
            >
              Tümünü Okundu İşaretle
            </button>
            <button
              @click="handleClearAll"
              class="text-sm text-gray-500 hover:text-gray-700"
            >
              Temizle
            </button>
          </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
          <div v-if="notifications.length === 0" class="p-8 text-center text-gray-500">
            <svg class="mx-auto w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p>Henüz bildirim yok</p>
          </div>

          <div
            v-for="notification in notifications"
            :key="notification.id"
            class="relative border-b border-gray-100 hover:bg-gray-50 transition-colors"
            :class="{ 'bg-blue-50': !notification.read }"
          >
            <div class="p-4">
              <div class="flex items-start gap-3">
                <!-- Icon -->
                <div
                  class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                  :class="getIconClass(notification.type)"
                >
                  <component :is="getIcon(notification.type)" class="w-5 h-5" />
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
                  <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
                  <p class="text-xs text-gray-400 mt-1">{{ formatTime(notification.timestamp) }}</p>

                  <!-- Action Button -->
                  <router-link
                    v-if="notification.action"
                    :to="notification.action.url"
                    @click="handleNotificationClick(notification)"
                    class="inline-block mt-2 text-sm text-blue-600 hover:text-blue-700 font-medium"
                  >
                    {{ notification.action.label }} →
                  </router-link>
                </div>

                <!-- Actions -->
                <div class="flex-shrink-0 flex items-center gap-2">
                  <button
                    v-if="!notification.read"
                    @click="handleMarkAsRead(notification.id)"
                    class="text-blue-600 hover:text-blue-700"
                    title="Okundu işaretle"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </button>
                  <button
                    @click="handleDelete(notification.id)"
                    class="text-gray-400 hover:text-red-600"
                    title="Sil"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="p-3 border-t border-gray-200 text-center">
          <router-link
            to="/notifications"
            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
            @click="isOpen = false"
          >
            Tüm Bildirimleri Görüntüle
          </router-link>
        </div>
      </div>
    </Transition>

    <!-- Click Outside Overlay -->
    <div
      v-if="isOpen"
      class="fixed inset-0 z-40"
      @click="isOpen = false"
    ></div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, h } from 'vue'
import { useNotifications } from '@/composables'

const {
  notifications,
  unreadCount,
  init,
  markAsRead,
  markAllAsRead,
  deleteNotification,
  clearAll
} = useNotifications()

const isOpen = ref(false)

// Get user ID from localStorage
const user = JSON.parse(localStorage.getItem('user') || '{}')

onMounted(() => {
  if (user.id) {
    init(user.id)
  }
})

function togglePanel() {
  isOpen.value = !isOpen.value
}

async function handleMarkAsRead(notificationId: string) {
  await markAsRead(notificationId)
}

async function handleMarkAllAsRead() {
  await markAllAsRead()
}

async function handleDelete(notificationId: string) {
  await deleteNotification(notificationId)
}

async function handleClearAll() {
  if (confirm('Tüm bildirimleri silmek istediğinizden emin misiniz?')) {
    await clearAll()
  }
}

function handleNotificationClick(notification: any) {
  if (!notification.read) {
    markAsRead(notification.id)
  }
  isOpen.value = false
}

function getIconClass(type: string) {
  const classes = {
    info: 'bg-blue-100 text-blue-600',
    success: 'bg-green-100 text-green-600',
    warning: 'bg-yellow-100 text-yellow-600',
    error: 'bg-red-100 text-red-600'
  }
  return classes[type as keyof typeof classes] || classes.info
}

function getIcon(type: string) {
  const icons = {
    info: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
    ]),
    success: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' })
    ]),
    warning: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' })
    ]),
    error: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' })
    ])
  }
  return icons[type as keyof typeof icons] || icons.info
}

function formatTime(timestamp: Date) {
  const now = new Date()
  const diff = now.getTime() - new Date(timestamp).getTime()
  const seconds = Math.floor(diff / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  if (seconds < 60) return 'Az önce'
  if (minutes < 60) return `${minutes} dakika önce`
  if (hours < 24) return `${hours} saat önce`
  if (days < 7) return `${days} gün önce`

  return new Date(timestamp).toLocaleDateString('tr-TR', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
