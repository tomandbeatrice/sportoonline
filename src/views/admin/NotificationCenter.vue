<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import NotificationAI from '@/components/admin/notification/NotificationAI.vue'

// --- Types ---
interface Notification {
  id: number
  type: 'email' | 'sms' | 'push' | 'system' | 'security'
  recipient: string
  subject: string
  content: string
  status: 'sent' | 'pending' | 'failed' | 'processing'
  created_at: string
  attempts: number
}

// --- State ---
const activeFilter = ref('all')
const selectedNotification = ref<Notification | null>(null)
const notifications = ref<Notification[]>([])

import axios from 'axios'

// Fetch notifications from API
const fetchNotifications = async () => {
  try {
    const { data } = await axios.get('/api/admin/notifications')
    notifications.value = (data.notifications || data || []).map((n: any) => ({
      id: n.id,
      type: n.type || 'system',
      recipient: n.recipient || n.data?.recipient || 'User',
      subject: n.subject || n.data?.subject || 'Bildirim',
      content: n.content || n.data?.message || '',
      status: n.status || (n.read_at ? 'sent' : 'pending'),
      created_at: n.created_at_human || n.created_at,
      attempts: n.attempts || 0
    }))
    if (notifications.value.length > 0) {
      selectedNotification.value = notifications.value[0]
    }
  } catch (error) {
    console.error('Failed to fetch notifications:', error)
    // Fallback demo data
    notifications.value = [
      { id: 1, type: 'email', recipient: 'user@example.com', subject: 'Bildirim', content: 'Demo bildirim', status: 'sent', created_at: 'Şimdi', attempts: 1 }
    ]
  }
}

// --- Computed ---
const filteredNotifications = computed(() => {
  if (activeFilter.value === 'all') return notifications.value
  return notifications.value.filter(n => n.type === activeFilter.value)
})

const stats = computed(() => ({
  total: notifications.value.length,
  failed: notifications.value.filter(n => n.status === 'failed').length,
  pending: notifications.value.filter(n => n.status === 'pending').length
}))

// --- Methods ---
const loadNotifications = () => {
  fetchNotifications()
}

const selectNotification = (notification: Notification) => {
  selectedNotification.value = notification
}

const getStatusBadgeClass = (status: string) => {
  switch (status) {
    case 'sent': return 'bg-emerald-100 text-emerald-700 border-emerald-200'
    case 'pending': return 'bg-amber-100 text-amber-700 border-amber-200'
    case 'failed': return 'bg-red-100 text-red-700 border-red-200'
    case 'processing': return 'bg-blue-100 text-blue-700 border-blue-200'
    default: return 'bg-gray-100 text-gray-700'
  }
}

const getTypeIcon = (type: string) => {
  const map: Record<string, string> = {
    email: '📧',
    sms: '📱',
    push: '🔔',
    system: '⚙️',
    security: '🛡️'
  }
  return map[type] || '📝'
}

onMounted(() => {
  loadNotifications()
})
</script>

<template>
  <div class="h-[calc(100vh-4rem)] flex flex-col bg-slate-50 overflow-hidden">
    <!-- Top Bar -->
    <div class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
          🔔 Bildirim Merkezi
          <span class="text-xs font-normal bg-red-100 text-red-700 px-2 py-0.5 rounded-full border border-red-200" v-if="stats.failed > 0">
            {{ stats.failed }} Hata
          </span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">Sistem bildirimlerini ve iletişim kuyruğunu yönetin</p>
      </div>
      
      <div class="flex gap-3">
        <button class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-bold transition">
          ⚙️ Ayarlar
        </button>
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm shadow-indigo-200 flex items-center gap-2">
          <span>✉️</span> Yeni Bildirim
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-1 overflow-hidden">
      
      <!-- Left Panel: List -->
      <div class="w-1/3 min-w-[400px] bg-white border-r border-slate-200 flex flex-col">
        <!-- Filters -->
        <div class="p-4 border-b border-slate-100 flex gap-2 overflow-x-auto no-scrollbar">
          <button 
            v-for="filter in ['all', 'email', 'sms', 'push', 'security']" 
            :key="filter"
            @click="activeFilter = filter"
            class="px-3 py-1.5 rounded-lg text-xs font-bold capitalize whitespace-nowrap transition-colors"
            :class="activeFilter === filter ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
          >
            {{ filter === 'all' ? 'Tümü' : filter }}
          </button>
        </div>

        <!-- Notification List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-2">
          <div 
            v-for="notif in filteredNotifications" 
            :key="notif.id"
            @click="selectNotification(notif)"
            class="group p-3 rounded-xl border cursor-pointer transition-all duration-200 hover:shadow-md"
            :class="selectedNotification?.id === notif.id ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200' : 'bg-white border-slate-100 hover:border-indigo-100'"
          >
            <div class="flex gap-3">
              <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-lg shadow-sm shrink-0">
                {{ getTypeIcon(notif.type) }}
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start mb-1">
                  <h3 class="font-bold text-slate-800 text-sm truncate pr-2" :class="selectedNotification?.id === notif.id ? 'text-indigo-700' : ''">
                    {{ notif.subject }}
                  </h3>
                  <span class="text-[10px] text-slate-400 whitespace-nowrap">{{ notif.created_at }}</span>
                </div>
                <p class="text-xs text-slate-500 truncate mb-2">{{ notif.content }}</p>
                <div class="flex items-center justify-between">
                  <span class="text-[10px] font-mono text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100 truncate max-w-[150px]">
                    {{ notif.recipient }}
                  </span>
                  <span 
                    class="text-[10px] px-1.5 py-0.5 rounded-full border font-medium"
                    :class="getStatusBadgeClass(notif.status)"
                  >
                    {{ notif.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Details & AI -->
      <div class="flex-1 bg-slate-50 overflow-y-auto p-6" v-if="selectedNotification">
        <div class="max-w-4xl mx-auto space-y-6">
          
          <!-- Detail Card -->
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex justify-between items-start mb-6">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl border border-indigo-100">
                  {{ getTypeIcon(selectedNotification.type) }}
                </div>
                <div>
                  <h2 class="text-xl font-bold text-slate-800">{{ selectedNotification.subject }}</h2>
                  <div class="flex items-center gap-2 text-sm text-slate-500 mt-1">
                    <span>Alıcı: <span class="font-mono text-slate-700">{{ selectedNotification.recipient }}</span></span>
                    <span>•</span>
                    <span>{{ selectedNotification.created_at }}</span>
                  </div>
                </div>
              </div>
              <div class="text-right">
                <span 
                  class="inline-block px-3 py-1 rounded-full text-xs font-bold border uppercase tracking-wide"
                  :class="getStatusBadgeClass(selectedNotification.status)"
                >
                  {{ selectedNotification.status }}
                </span>
              </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 text-sm text-slate-700 leading-relaxed font-mono whitespace-pre-wrap">
              {{ selectedNotification.content }}
            </div>

            <div class="mt-4 flex gap-2 justify-end">
              <button class="text-xs font-bold text-slate-500 hover:text-slate-700 px-3 py-2 bg-slate-100 rounded hover:bg-slate-200 transition">
                Logları Görüntüle
              </button>
              <button class="text-xs font-bold text-white px-3 py-2 bg-indigo-600 rounded hover:bg-indigo-700 transition shadow-sm">
                Yeniden Gönder
              </button>
            </div>
          </div>

          <!-- AI Analysis Section -->
          <div class="grid grid-cols-3 gap-6">
            <!-- Left: AI Component -->
            <div class="col-span-1">
              <NotificationAI 
                :notification-id="selectedNotification.id"
                :type="selectedNotification.type"
                :subject="selectedNotification.subject"
                :content="selectedNotification.content"
                :status="selectedNotification.status"
              />
            </div>

            <!-- Right: Delivery Stats (Mock) -->
            <div class="col-span-2 space-y-6">
              <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                  📡 İletim Analizi
                </h3>
                <div class="space-y-4">
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Sunucu Yanıt Süresi</span>
                    <span class="font-mono font-bold text-slate-700">124ms</span>
                  </div>
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Deneme Sayısı</span>
                    <span class="font-mono font-bold text-slate-700">{{ selectedNotification.attempts + 1 }} / 3</span>
                  </div>
                  <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Gateway</span>
                    <span class="font-mono font-bold text-slate-700">AWS SES</span>
                  </div>
                  <div class="h-px bg-slate-100 my-2"></div>
                  <div class="flex items-center gap-2 text-xs text-emerald-600 font-bold bg-emerald-50 p-2 rounded border border-emerald-100">
                    <span>✅</span>
                    <span>SMTP Bağlantısı Başarılı</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="flex-1 flex items-center justify-center bg-slate-50 text-slate-400">
        <div class="text-center">
          <div class="text-4xl mb-2">👈</div>
          <p>Detayları görmek için bir bildirim seçin</p>
        </div>
      </div>

    </div>
  </div>
</template>
