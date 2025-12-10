<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
            <span class="text-4xl">🔔</span>
            Bildirimler
          </h1>
          <p class="text-slate-500 mt-1">Tüm bildirimlerinizi buradan yönetin</p>
        </div>
        
        <div class="flex gap-3">
          <button 
            v-if="unreadCount > 0"
            @click="markAllAsRead"
            class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium hover:bg-slate-50 transition"
          >
            ✓ Tümünü Okundu İşaretle
          </button>
          <router-link 
            to="/notifications/settings"
            class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition flex items-center gap-2"
          >
            ⚙️ Ayarlar
          </router-link>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-4 border border-slate-200">
          <div class="text-2xl font-bold text-slate-900">{{ notifications.length }}</div>
          <div class="text-sm text-slate-500">Toplam Bildirim</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200">
          <div class="text-2xl font-bold text-indigo-600">{{ unreadCount }}</div>
          <div class="text-sm text-slate-500">Okunmamış</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200">
          <div class="text-2xl font-bold text-green-600">{{ orderNotifications.length }}</div>
          <div class="text-sm text-slate-500">Sipariş Bildirimi</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200">
          <div class="text-2xl font-bold text-orange-600">{{ promoNotifications.length }}</div>
          <div class="text-sm text-slate-500">Kampanya</div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl border border-slate-200 mb-6 p-4">
        <div class="flex flex-wrap gap-3">
          <button 
            v-for="filter in filters"
            :key="filter.id"
            @click="activeFilter = filter.id"
            class="px-4 py-2 rounded-full text-sm font-medium transition"
            :class="activeFilter === filter.id 
              ? 'bg-indigo-600 text-white' 
              : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
          >
            {{ filter.icon }} {{ filter.name }}
            <span v-if="filter.count" class="ml-1 opacity-75">({{ filter.count }})</span>
          </button>
        </div>
      </div>

      <!-- Notification List -->
      <div class="space-y-4">
        <template v-for="(group, date) in groupedNotifications" :key="date">
          <div class="flex items-center gap-3 py-2">
            <div class="h-px flex-1 bg-slate-200"></div>
            <span class="text-sm font-medium text-slate-500">{{ date }}</span>
            <div class="h-px flex-1 bg-slate-200"></div>
          </div>
          
          <TransitionGroup name="list">
            <div
              v-for="notification in group"
              :key="notification.id"
              class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-lg transition-all"
              :class="{ 'border-l-4 border-l-indigo-500': !notification.read }"
            >
              <div class="flex items-start gap-4 p-5">
                <!-- Icon -->
                <div 
                  class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center text-xl"
                  :class="getIconClass(notification.type)"
                >
                  {{ getIcon(notification.type) }}
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between gap-4">
                    <div>
                      <h3 
                        class="font-semibold text-slate-900"
                        :class="{ 'font-bold': !notification.read }"
                      >
                        {{ notification.title }}
                      </h3>
                      <p class="text-slate-600 mt-1">{{ notification.message }}</p>
                    </div>
                    <span class="flex-shrink-0 text-sm text-slate-400">
                      {{ formatTime(notification.timestamp) }}
                    </span>
                  </div>
                  
                  <!-- Actions -->
                  <div class="flex items-center gap-3 mt-4">
                    <button 
                      v-if="notification.data?.action"
                      @click="handleAction(notification)"
                      class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition"
                    >
                      {{ notification.data?.actionText || 'Görüntüle' }}
                    </button>
                    <button 
                      v-if="!notification.read"
                      @click="markAsRead(notification.id)"
                      class="px-3 py-1.5 text-indigo-600 text-sm font-medium hover:underline"
                    >
                      Okundu işaretle
                    </button>
                    <button 
                      @click="deleteNotification(notification.id)"
                      class="px-3 py-1.5 text-slate-400 text-sm hover:text-red-500 transition ml-auto"
                    >
                      🗑️ Sil
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </TransitionGroup>
        </template>
      </div>

      <!-- Empty State -->
      <div v-if="filteredNotifications.length === 0" class="text-center py-16">
        <div class="text-6xl mb-4">🔔</div>
        <h3 class="text-xl font-bold text-slate-900">Bildirim bulunmuyor</h3>
        <p class="text-slate-500 mt-2">Yeni bildirimler geldiğinde burada gösterilecek</p>
      </div>

      <!-- Load More -->
      <div v-if="hasMore" class="text-center mt-8">
        <button 
          @click="loadMore"
          class="px-6 py-3 bg-white border border-slate-200 rounded-xl font-medium text-slate-700 hover:bg-slate-50 transition"
        >
          Daha Fazla Yükle
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useNotifications } from '@/composables/useNotifications'

interface ExtendedNotification {
  id: string
  type: 'order' | 'payment' | 'status' | 'system' | 'promo' | 'message' | 'stock' | 'review'
  title: string
  message: string
  data?: any
  read: boolean
  timestamp: string
}

const router = useRouter()
const { notifications: baseNotifications, unreadCount, markAsRead, markAllAsRead, clearAll } = useNotifications()

// Extended notifications with more types
const notifications = ref<ExtendedNotification[]>([
  { id: '1', type: 'order', title: 'Siparişiniz kargoya verildi', message: 'Sipariş #1234 kargoya teslim edildi. Takip numarası: TR123456789', timestamp: new Date(Date.now() - 300000).toISOString(), read: false, data: { action: '/orders/1234', actionText: 'Siparişi Görüntüle' } },
  { id: '2', type: 'promo', title: '🎉 Özel indirim fırsatı!', message: 'Spor ayakkabılarda %30 indirim başladı. Kaçırmayın!', timestamp: new Date(Date.now() - 3600000).toISOString(), read: false },
  { id: '3', type: 'message', title: 'Satıcıdan yeni mesaj', message: 'SportMax Store: Merhaba, ürününüz hakkında bilgi vermek istiyorum...', timestamp: new Date(Date.now() - 7200000).toISOString(), read: true, data: { action: '/messages/seller123', actionText: 'Mesajı Oku' } },
  { id: '4', type: 'payment', title: 'Ödeme onaylandı', message: 'Sipariş #1233 için 599,00 ₺ tutarındaki ödemeniz alındı', timestamp: new Date(Date.now() - 86400000).toISOString(), read: true },
  { id: '5', type: 'system', title: 'Hesap güvenliği', message: 'Hesabınıza yeni bir cihazdan giriş yapıldı: iPhone, İstanbul', timestamp: new Date(Date.now() - 172800000).toISOString(), read: true },
  { id: '6', type: 'stock', title: 'Stok uyarısı', message: 'Takip ettiğiniz ürün stoğa girdi: Nike Air Max 270 - Beyaz', timestamp: new Date(Date.now() - 1800000).toISOString(), read: false, data: { action: '/products/123', actionText: 'Ürüne Git' } },
  { id: '7', type: 'review', title: 'Değerlendirmeniz bekleniyor', message: 'Son siparişinizi değerlendirir misiniz? Görüşleriniz bizim için önemli.', timestamp: new Date(Date.now() - 432000000).toISOString(), read: true, data: { action: '/reviews/create', actionText: 'Değerlendir' } },
  { id: '8', type: 'status', title: 'Sipariş hazırlanıyor', message: 'Sipariş #1235 satıcı tarafından hazırlanıyor', timestamp: new Date(Date.now() - 18000000).toISOString(), read: true },
])

const activeFilter = ref('all')
const hasMore = ref(true)

const filters = computed(() => [
  { id: 'all', name: 'Tümü', icon: '📋', count: notifications.value.length },
  { id: 'unread', name: 'Okunmamış', icon: '🔴', count: notifications.value.filter(n => !n.read).length },
  { id: 'order', name: 'Siparişler', icon: '📦', count: orderNotifications.value.length },
  { id: 'promo', name: 'Kampanyalar', icon: '🏷️', count: promoNotifications.value.length },
  { id: 'message', name: 'Mesajlar', icon: '💬', count: notifications.value.filter(n => n.type === 'message').length },
  { id: 'system', name: 'Sistem', icon: '⚙️', count: notifications.value.filter(n => n.type === 'system').length },
])

const orderNotifications = computed(() => 
  notifications.value.filter(n => ['order', 'payment', 'status'].includes(n.type))
)

const promoNotifications = computed(() => 
  notifications.value.filter(n => n.type === 'promo')
)

const filteredNotifications = computed(() => {
  if (activeFilter.value === 'all') return notifications.value
  if (activeFilter.value === 'unread') return notifications.value.filter(n => !n.read)
  if (activeFilter.value === 'order') return orderNotifications.value
  if (activeFilter.value === 'promo') return promoNotifications.value
  return notifications.value.filter(n => n.type === activeFilter.value)
})

const groupedNotifications = computed(() => {
  const groups: Record<string, ExtendedNotification[]> = {}
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const yesterday = new Date(today)
  yesterday.setDate(yesterday.getDate() - 1)
  
  filteredNotifications.value.forEach(n => {
    const date = new Date(n.timestamp)
    date.setHours(0, 0, 0, 0)
    
    let key: string
    if (date.getTime() >= today.getTime()) {
      key = 'Bugün'
    } else if (date.getTime() >= yesterday.getTime()) {
      key = 'Dün'
    } else {
      key = date.toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' })
    }
    
    if (!groups[key]) groups[key] = []
    groups[key].push(n)
  })
  
  return groups
})

const getIcon = (type: string) => {
  const icons: Record<string, string> = {
    order: '📦',
    payment: '💳',
    status: '🚚',
    system: '⚙️',
    promo: '🏷️',
    message: '💬',
    stock: '📊',
    review: '⭐'
  }
  return icons[type] || '🔔'
}

const getIconClass = (type: string) => {
  const classes: Record<string, string> = {
    order: 'bg-blue-100 text-blue-600',
    payment: 'bg-emerald-100 text-emerald-600',
    status: 'bg-purple-100 text-purple-600',
    system: 'bg-slate-100 text-slate-600',
    promo: 'bg-orange-100 text-orange-600',
    message: 'bg-green-100 text-green-600',
    stock: 'bg-cyan-100 text-cyan-600',
    review: 'bg-yellow-100 text-yellow-600'
  }
  return classes[type] || 'bg-slate-100 text-slate-600'
}

const formatTime = (timestamp: string) => {
  const date = new Date(timestamp)
  const now = new Date()
  const diff = now.getTime() - date.getTime()
  
  const minutes = Math.floor(diff / 60000)
  if (minutes < 60) return `${minutes} dakika önce`
  
  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `${hours} saat önce`
  
  return date.toLocaleDateString('tr-TR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
}

const deleteNotification = (id: string) => {
  notifications.value = notifications.value.filter(n => n.id !== id)
}

const handleAction = (notification: ExtendedNotification) => {
  if (notification.data?.action) {
    if (!notification.read) {
      notification.read = true
    }
    router.push(notification.data.action)
  }
}

const loadMore = () => {
  // Simulate loading more
  hasMore.value = false
}

onMounted(() => {
  // Merge with base notifications from composable
  // In real app, this would be API calls
})
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}
</style>
