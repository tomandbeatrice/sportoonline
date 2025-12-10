<template>
  <div class="relative">
    <!-- Trigger Button -->
    <button 
      @click="toggleDropdown"
      class="relative p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition group"
    >
      <span class="text-xl group-hover:scale-110 transition-transform block">🔔</span>
      <span 
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown -->
    <Transition name="dropdown">
      <div 
        v-if="isOpen"
        class="absolute right-0 top-12 w-[400px] bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden flex flex-col max-h-[600px]"
      >
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-200 flex-shrink-0">
          <div class="flex items-center gap-2">
            <h3 class="font-bold text-slate-900">Bildirimler</h3>
            <span v-if="highPriorityCount > 0" class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded-full">
              {{ highPriorityCount }} Önemli
            </span>
          </div>
          <div class="flex gap-2">
            <button 
              v-if="unreadCount > 0"
              @click="store.markAllAsRead"
              class="text-xs text-indigo-600 hover:underline"
            >
              Tümünü okundu işaretle
            </button>
            <button 
              @click="openSettings"
              class="p-1 text-slate-400 hover:text-slate-600"
            >
              ⚙️
            </button>
          </div>
        </div>

        <!-- Smart Filters -->
        <div class="flex border-b border-slate-200 bg-white flex-shrink-0 overflow-x-auto">
          <button 
            v-for="filter in filters"
            :key="filter.id"
            @click="activeFilter = filter.id"
            class="px-4 py-2.5 text-sm font-medium transition whitespace-nowrap"
            :class="activeFilter === filter.id ? 'text-indigo-600 border-b-2 border-indigo-600 bg-indigo-50/50' : 'text-slate-500 hover:bg-slate-50'"
          >
            {{ filter.name }}
          </button>
        </div>

        <!-- Notification List -->
        <div class="overflow-y-auto flex-1 bg-slate-50/50">
          <div v-if="filteredNotifications.length === 0" class="py-12 text-center">
            <span class="text-4xl mb-3 block opacity-20">🔕</span>
            <p class="text-slate-500">Bildirim bulunmuyor</p>
          </div>
          
          <div 
            v-for="notification in filteredNotifications"
            :key="notification.id"
            class="group relative border-b border-slate-100 last:border-0 bg-white hover:bg-slate-50 transition"
            :class="{ 'bg-indigo-50/30': !notification.read }"
          >
            <!-- Priority Stripe -->
            <div 
              class="absolute left-0 top-0 bottom-0 w-1"
              :class="{
                'bg-red-500': notification.priority === 'high',
                'bg-orange-400': notification.priority === 'medium',
                'bg-slate-200': notification.priority === 'low'
              }"
            ></div>

            <div class="p-4 pl-5" @click="handleNotificationClick(notification)">
              <div class="flex gap-3">
                <!-- Icon -->
                <div 
                  class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-lg shadow-sm"
                  :class="getNotificationIconClass(notification.type)"
                >
                  {{ getNotificationIcon(notification.type) }}
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between gap-2 mb-1">
                    <div class="flex items-center gap-2">
                      <span 
                        v-if="notification.priority === 'high'"
                        class="w-2 h-2 rounded-full bg-red-500 animate-pulse"
                        title="Yüksek Öncelik"
                      ></span>
                      <p class="text-sm font-medium text-slate-900" :class="{ 'font-bold': !notification.read }">
                        {{ notification.title }}
                      </p>
                    </div>
                    <span class="flex-shrink-0 text-xs text-slate-400">{{ formatTime(notification.time) }}</span>
                  </div>
                  
                  <p class="text-sm text-slate-600 mb-3 leading-relaxed">{{ notification.message }}</p>

                  <!-- Smart Actions -->
                  <div v-if="notification.actions && notification.actions.length > 0" class="flex gap-2 mt-2">
                    <button 
                      v-for="action in notification.actions"
                      :key="action.action"
                      @click.stop="store.handleAction(notification.id, action.action)"
                      class="px-3 py-1.5 text-xs font-medium rounded-lg transition flex items-center gap-1"
                      :class="getActionClass(action.variant)"
                    >
                      {{ action.label }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Hover Actions -->
            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition flex gap-1">
              <button 
                v-if="!notification.read"
                @click.stop="store.markAsRead(notification.id)"
                class="p-1.5 bg-white text-slate-400 hover:text-indigo-600 rounded-lg shadow-sm border border-slate-100"
                title="Okundu işaretle"
              >
                ✓
              </button>
              <button 
                @click.stop="store.removeNotification(notification.id)"
                class="p-1.5 bg-white text-slate-400 hover:text-red-600 rounded-lg shadow-sm border border-slate-100"
                title="Sil"
              >
                ✕
              </button>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 text-center flex-shrink-0">
          <router-link 
            to="/notifications"
            class="text-sm text-indigo-600 hover:underline font-medium flex items-center justify-center gap-1"
            @click="isOpen = false"
          >
            Tüm bildirimleri yönet
            <span>→</span>
          </router-link>
        </div>
      </div>
    </Transition>

    <!-- Backdrop -->
    <div v-if="isOpen" @click="isOpen = false" class="fixed inset-0 z-40"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useSmartNotificationStore, type SmartNotification } from '@/stores/smartNotificationStore'
import { storeToRefs } from 'pinia'

const store = useSmartNotificationStore()
const { groupedNotifications, unreadCount, highPriorityCount } = storeToRefs(store)

const isOpen = ref(false)
const activeFilter = ref('all')

const filters = [
  { id: 'all', name: 'Tümü' },
  { id: 'high', name: '⚠️ Önemli' },
  { id: 'orders', name: '📦 Sipariş' },
  { id: 'system', name: '⚙️ Sistem' }
]

const filteredNotifications = computed(() => {
  if (activeFilter.value === 'all') return groupedNotifications.value
  if (activeFilter.value === 'high') return groupedNotifications.value.filter(n => n.priority === 'high')
  if (activeFilter.value === 'orders') return groupedNotifications.value.filter(n => n.type === 'order')
  if (activeFilter.value === 'system') return groupedNotifications.value.filter(n => n.type === 'system' || n.type === 'security')
  return groupedNotifications.value
})

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const handleNotificationClick = (notification: SmartNotification) => {
  store.markAsRead(notification.id)
  // Navigate if needed
}

const openSettings = () => {
  // Open settings
}

const getNotificationIcon = (type: string) => {
  const icons: Record<string, string> = {
    order: '📦',
    alert: '⚠️',
    promotion: '🎉',
    system: '⚙️',
    security: '🛡️'
  }
  return icons[type] || '🔔'
}

const getNotificationIconClass = (type: string) => {
  const classes: Record<string, string> = {
    order: 'bg-blue-50 text-blue-600',
    alert: 'bg-orange-50 text-orange-600',
    promotion: 'bg-purple-50 text-purple-600',
    system: 'bg-slate-100 text-slate-600',
    security: 'bg-red-50 text-red-600'
  }
  return classes[type] || 'bg-slate-100 text-slate-600'
}

const getActionClass = (variant: string = 'secondary') => {
  const classes: Record<string, string> = {
    primary: 'bg-indigo-600 text-white hover:bg-indigo-700',
    secondary: 'bg-slate-100 text-slate-700 hover:bg-slate-200',
    danger: 'bg-red-50 text-red-600 hover:bg-red-100',
    success: 'bg-green-50 text-green-600 hover:bg-green-100'
  }
  return classes[variant]
}

const formatTime = (date: Date) => {
  const diff = Date.now() - new Date(date).getTime()
  const minutes = Math.floor(diff / 60000)
  if (minutes < 1) return 'Şimdi'
  if (minutes < 60) return `${minutes} dk`
  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `${hours} sa`
  const days = Math.floor(hours / 24)
  return `${days} gün`
}

// Keyboard shortcut
const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === 'Escape') isOpen.value = false
}

onMounted(() => document.addEventListener('keydown', handleKeydown))
onUnmounted(() => document.removeEventListener('keydown', handleKeydown))
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.98);
}
</style>
