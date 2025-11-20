<template>
  <div class="fixed bottom-6 right-6 flex flex-col gap-3 z-50">
    <transition-group name="slide-up">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        :class="[
          'min-w-[320px] max-w-md rounded-2xl border p-4 shadow-2xl backdrop-blur-sm',
          notificationStyles[notification.type]
        ]"
      >
        <div class="flex items-start gap-3">
          <div class="flex-shrink-0 mt-0.5">
            <component :is="notificationIcons[notification.type]" class="h-5 w-5" />
          </div>
          <div class="flex-1 min-w-0">
            <p v-if="notification.title" class="font-semibold text-sm mb-1">
              {{ notification.title }}
            </p>
            <p class="text-sm opacity-90">{{ notification.message }}</p>
          </div>
          <button
            @click="removeNotification(notification.id)"
            class="flex-shrink-0 rounded-lg p-1 hover:bg-black/10 transition"
          >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div
          v-if="!notification.persist"
          class="absolute bottom-0 left-0 h-1 bg-current opacity-30 transition-all"
          :style="{ width: `${notification.progress}%` }"
        />
      </div>
    </transition-group>
  </div>
</template>

<script setup lang="ts">
import { ref, h } from 'vue'

type NotificationType = 'success' | 'error' | 'warning' | 'info'

interface Notification {
  id: number
  type: NotificationType
  message: string
  title?: string
  duration?: number
  persist?: boolean
  progress: number
}

const notifications = ref<Notification[]>([])
let notificationId = 0

const notificationStyles = {
  success: 'bg-emerald-50/95 border-emerald-200 text-emerald-900',
  error: 'bg-red-50/95 border-red-200 text-red-900',
  warning: 'bg-orange-50/95 border-orange-200 text-orange-900',
  info: 'bg-blue-50/95 border-blue-200 text-blue-900'
}

const notificationIcons = {
  success: () => h('svg', { class: 'text-emerald-600', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, 
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M5 13l4 4L19 7' })),
  error: () => h('svg', { class: 'text-red-600', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, 
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M6 18L18 6M6 6l12 12' })),
  warning: () => h('svg', { class: 'text-orange-600', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, 
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' })),
  info: () => h('svg', { class: 'text-blue-600', fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, 
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }))
}

const addNotification = (
  type: NotificationType,
  message: string,
  options: { title?: string; duration?: number; persist?: boolean } = {}
) => {
  const id = notificationId++
  const duration = options.duration || 3000
  const notification: Notification = {
    id,
    type,
    message,
    title: options.title,
    duration,
    persist: options.persist,
    progress: 100
  }

  notifications.value.push(notification)

  if (!options.persist) {
    const startTime = Date.now()
    const interval = setInterval(() => {
      const elapsed = Date.now() - startTime
      notification.progress = Math.max(0, 100 - (elapsed / duration) * 100)
      
      if (elapsed >= duration) {
        clearInterval(interval)
        removeNotification(id)
      }
    }, 16)
  }
}

const removeNotification = (id: number) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value.splice(index, 1)
  }
}

defineExpose({
  success: (message: string, options?: any) => addNotification('success', message, options),
  error: (message: string, options?: any) => addNotification('error', message, options),
  warning: (message: string, options?: any) => addNotification('warning', message, options),
  info: (message: string, options?: any) => addNotification('info', message, options)
})
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.slide-up-leave-to {
  opacity: 0;
  transform: translateX(100px);
}
</style>
