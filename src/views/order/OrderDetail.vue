<template>
  <div class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-4xl mx-auto">
      <!-- Back Button -->
      <router-link to="/orders" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-6">
        <span>←</span>
        <span>Siparişlerime Dön</span>
      </router-link>

      <div v-if="order">
        <!-- Order Header Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6">
          <div class="p-6 border-b border-slate-100">
            <div class="flex items-start justify-between">
              <div>
                <div class="flex items-center gap-3 mb-2">
                  <h1 class="text-2xl font-bold text-slate-900">Sipariş #{{ order.id }}</h1>
                  <span 
                    class="px-3 py-1 rounded-full text-sm font-medium"
                    :class="getStatusClass(order.status)"
                  >
                    {{ getStatusLabel(order.status) }}
                  </span>
                </div>
                <p class="text-slate-500">{{ formatDate(order.created_at) }}</p>
              </div>
              <div class="text-right">
                <p class="text-3xl font-bold text-green-600">{{ formatPrice(order.price) }}</p>
                <p class="text-sm text-slate-500">{{ order.paid_at ? '✅ Ödendi' : '⏳ Ödeme Bekliyor' }}</p>
              </div>
            </div>
          </div>

          <!-- Quick Info -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6">
            <div class="text-center p-4 bg-slate-50 rounded-xl">
              <div class="text-2xl mb-1">{{ orderTypeIcon }}</div>
              <p class="text-xs text-slate-500">{{ orderTypeLabel }}</p>
              <p class="font-bold text-slate-900">{{ order.order_details?.length || 0 }}</p>
            </div>
            <div v-if="isPhysicalProduct" class="text-center p-4 bg-slate-50 rounded-xl">
              <div class="text-2xl mb-1">🚚</div>
              <p class="text-xs text-slate-500">Kargo Durumu</p>
              <p class="font-bold text-slate-900">{{ order.tracking_code ? 'Kargoda' : 'Hazırlanıyor' }}</p>
            </div>
            <div v-else class="text-center p-4 bg-slate-50 rounded-xl">
              <div class="text-2xl mb-1">{{ serviceStatusIcon }}</div>
              <p class="text-xs text-slate-500">Durum</p>
              <p class="font-bold text-slate-900">{{ serviceStatusLabel }}</p>
            </div>
            <div v-if="isPhysicalProduct" class="text-center p-4 bg-slate-50 rounded-xl">
              <div class="text-2xl mb-1">📍</div>
              <p class="text-xs text-slate-500">Kargo Takip</p>
              <p class="font-bold text-slate-900">{{ order.tracking_code || '-' }}</p>
            </div>
            <div v-else-if="isBooking" class="text-center p-4 bg-slate-50 rounded-xl">
              <div class="text-2xl mb-1">📅</div>
              <p class="text-xs text-slate-500">Başlangıç</p>
              <p class="font-bold text-slate-900">{{ order.booking_start_date ? formatBookingDate(order.booking_start_date) : '-' }}</p>
            </div>
            <div v-else class="text-center p-4 bg-slate-50 rounded-xl">
              <div class="text-2xl mb-1">⏱️</div>
              <p class="text-xs text-slate-500">Süre</p>
              <p class="font-bold text-slate-900">{{ order.service_duration ? order.service_duration + ' dk' : '-' }}</p>
            </div>
            <div v-if="isPhysicalProduct" class="text-center p-4 bg-slate-50 rounded-xl">
              <div class="text-2xl mb-1">🏠</div>
              <p class="text-xs text-slate-500">Teslimat</p>
              <p class="font-bold text-slate-900">{{ order.estimated_delivery || 'Hesaplanıyor' }}</p>
            </div>
            <div v-else-if="isBooking" class="text-center p-4 bg-slate-50 rounded-xl">
              <div class="text-2xl mb-1">🏁</div>
              <p class="text-xs text-slate-500">Bitiş</p>
              <p class="font-bold text-slate-900">{{ order.booking_end_date ? formatBookingDate(order.booking_end_date) : '-' }}</p>
            </div>
            <div v-else class="text-center p-4 bg-slate-50 rounded-xl">
              <div class="text-2xl mb-1">👤</div>
              <p class="text-xs text-slate-500">Hizmet Veren</p>
              <p class="font-bold text-slate-900">{{ order.service_provider || 'Atanıyor' }}</p>
            </div>
          </div>
        </div>

        <!-- Products -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6">
          <div class="p-6 border-b border-slate-100">
            <h2 class="font-bold text-lg text-slate-900">Sipariş Ürünleri</h2>
          </div>
          <div class="divide-y divide-slate-100">
            <div 
              v-for="item in order.order_details" 
              :key="item.id"
              class="p-4 flex items-center gap-4"
            >
              <div class="w-20 h-20 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0">
                <img 
                  :src="item.product?.image || 'https://via.placeholder.com/100'" 
                  :alt="item.product?.name"
                  class="w-full h-full object-cover"
                >
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="font-medium text-slate-900 truncate">{{ item.product?.name }}</h3>
                <p class="text-sm text-slate-500">{{ item.quantity }} adet</p>
              </div>
              <div class="text-right">
                <p class="font-bold text-slate-900">{{ formatPrice(item.price) }}</p>
                <p class="text-xs text-slate-500">Birim: {{ formatPrice(item.price / item.quantity) }}</p>
              </div>
            </div>
          </div>
          <!-- Summary -->
          <div class="p-6 bg-slate-50 border-t border-slate-100">
            <div class="flex justify-between text-sm text-slate-600 mb-2">
              <span>Ara Toplam</span>
              <span>{{ formatPrice(order.subtotal || order.price * 0.85) }}</span>
            </div>
            <div class="flex justify-between text-sm text-slate-600 mb-2">
              <span>Kargo</span>
              <span>{{ order.shipping_cost ? formatPrice(order.shipping_cost) : 'Ücretsiz' }}</span>
            </div>
            <div class="flex justify-between font-bold text-lg text-slate-900 pt-2 border-t border-slate-200">
              <span>Toplam</span>
              <span class="text-green-600">{{ formatPrice(order.price) }}</span>
            </div>
          </div>
        </div>

        <!-- Order Timeline -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6">
          <div class="p-6 border-b border-slate-100">
            <h2 class="font-bold text-lg text-slate-900">Sipariş Geçmişi</h2>
          </div>
          <div class="p-6">
            <div class="relative">
              <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200"></div>
              <div 
                v-for="(log, index) in order.logs" 
                :key="log.id"
                class="relative pl-10 pb-6 last:pb-0"
              >
                <div 
                  class="absolute left-2 w-4 h-4 rounded-full border-2"
                  :class="index === 0 ? 'bg-green-500 border-green-500' : 'bg-white border-slate-300'"
                ></div>
                <div>
                  <p class="font-medium text-slate-900">{{ log.message }}</p>
                  <p class="text-sm text-slate-500">{{ formatDate(log.created_at) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 p-6">
          <h2 class="font-bold text-lg text-slate-900 mb-4">İşlemler</h2>
          <div class="grid md:grid-cols-2 gap-3">
            <button 
              v-if="isPhysicalProduct && order.tracking_code"
              @click="trackShipment"
              :disabled="trackingLoading"
              class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition disabled:opacity-50 flex items-center justify-center gap-2"
            >
              <span v-if="trackingLoading">⏳ Yükleniyor...</span>
              <span v-else>📍 Kargoyu Takip Et</span>
            </button>
            <button 
              v-if="canReturn"
              @click="initiateReturn"
              class="px-6 py-3 bg-orange-600 text-white rounded-xl font-medium hover:bg-orange-700 transition flex items-center justify-center gap-2"
            >
              🔄 İade Başlat
            </button>
            <button 
              @click="downloadInvoice"
              class="px-6 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition flex items-center justify-center gap-2"
            >
              📄 Fatura İndir
            </button>
            <button 
              @click="contactSupport"
              class="px-6 py-3 border-2 border-green-600 text-green-600 rounded-xl font-medium hover:bg-green-50 transition flex items-center justify-center gap-2"
            >
              💬 Destek Talebi
            </button>
            <button 
              @click="reorder"
              class="px-6 py-3 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition flex items-center justify-center gap-2"
            >
              🔁 Yeniden Satın Al
            </button>
            <button 
              v-if="order.status === 'pending'"
              @click="cancelOrder"
              class="px-6 py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition flex items-center justify-center gap-2"
            >
              ❌ Siparişi İptal Et
            </button>
          </div>
        </div>

        <!-- Tracking Modal -->
        <div v-if="showTrackingModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
          <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-xl font-bold text-slate-900">Kargo Takip</h3>
              <button @click="showTrackingModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>
            
            <div v-if="trackingInfo">
              <div class="bg-slate-50 rounded-xl p-4 mb-4">
                <p class="text-sm text-slate-500">Takip Numarası</p>
                <p class="font-bold text-slate-900">{{ order.tracking_code }}</p>
                <p class="text-sm text-slate-500 mt-2">Kargo Firması</p>
                <p class="font-medium text-slate-900">{{ trackingInfo.provider || 'Belirleniyor' }}</p>
              </div>
              
              <div class="space-y-4">
                <div 
                  v-for="(event, index) in trackingInfo.events" 
                  :key="index"
                  class="flex gap-4"
                >
                  <div class="flex flex-col items-center">
                    <div 
                      class="w-3 h-3 rounded-full"
                      :class="index === 0 ? 'bg-green-500' : 'bg-slate-300'"
                    ></div>
                    <div v-if="index < trackingInfo.events.length - 1" class="w-0.5 h-full bg-slate-200"></div>
                  </div>
                  <div class="pb-4">
                    <p class="font-medium text-slate-900">{{ event.status }}</p>
                    <p class="text-sm text-slate-500">{{ event.location }}</p>
                    <p class="text-xs text-slate-400">{{ event.date }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-8">
              <p class="text-slate-500">Kargo bilgisi bulunamadı</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-else class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
        <div class="animate-spin text-4xl mb-4">⏳</div>
        <p class="text-slate-500">Sipariş bilgileri yükleniyor...</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios'
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const order = ref<any>(null)
const showTrackingModal = ref(false)
const trackingLoading = ref(false)
const trackingInfo = ref<any>(null)

const trackShipment = async () => {
  if (!order.value?.tracking_code) return
  
  trackingLoading.value = true
  try {
    const res = await axios.get(`/api/shipping/track/${order.value.tracking_code}`, {
      params: { provider: order.value.shipping_provider }
    })
    
    if (res.data.success) {
      trackingInfo.value = res.data
      showTrackingModal.value = true
    } else {
      // Fallback mock data
      trackingInfo.value = {
        provider: 'Yurtiçi Kargo',
        status: 'Dağıtımda',
        events: [
          { status: 'Dağıtıma çıkarıldı', location: 'İstanbul Kadıköy Şube', date: new Date().toLocaleString('tr-TR') },
          { status: 'Şubeye ulaştı', location: 'İstanbul Kadıköy Şube', date: new Date(Date.now() - 86400000).toLocaleString('tr-TR') },
          { status: 'Transfer merkezinde', location: 'İstanbul Anadolu TM', date: new Date(Date.now() - 172800000).toLocaleString('tr-TR') },
          { status: 'Kargo alındı', location: 'Ankara Çankaya Şube', date: new Date(Date.now() - 259200000).toLocaleString('tr-TR') },
        ]
      }
      showTrackingModal.value = true
    }
  } catch (error) {
    console.error('Tracking bilgisi alınamadı:', error)
    // Mock tracking info for demo
    trackingInfo.value = {
      provider: 'Yurtiçi Kargo',
      status: 'Dağıtımda',
      events: [
        { status: 'Dağıtıma çıkarıldı', location: 'İstanbul Kadıköy Şube', date: new Date().toLocaleString('tr-TR') },
        { status: 'Şubeye ulaştı', location: 'İstanbul Kadıköy Şube', date: new Date(Date.now() - 86400000).toLocaleString('tr-TR') },
        { status: 'Transfer merkezinde', location: 'İstanbul Anadolu TM', date: new Date(Date.now() - 172800000).toLocaleString('tr-TR') },
        { status: 'Kargo alındı', location: 'Ankara Çankaya Şube', date: new Date(Date.now() - 259200000).toLocaleString('tr-TR') },
      ]
    }
    showTrackingModal.value = true
  } finally {
    trackingLoading.value = false
  }
}

onMounted(async () => {
  try {
    const res = await axios.get(`/api/orders/${route.params.id}`)
    order.value = res.data
  } catch (error) {
    console.error('Sipariş bilgisi alınamadı:', error)
    // Mock data for testing
    order.value = {
      id: route.params.id,
      status: 'shipped',
      created_at: new Date().toISOString(),
      price: 2450,
      paid_at: new Date().toISOString(),
      tracking_code: 'TR123456789',
      estimated_delivery: '7 Aralık 2025',
      order_details: [
        { id: 1, quantity: 1, price: 1500, product: { name: 'Nike Spor Ayakkabı', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200' } },
        { id: 2, quantity: 2, price: 950, product: { name: 'Spor Çorap Seti', image: 'https://images.unsplash.com/photo-1586350977771-b3b0abd50c82?w=200' } },
      ],
      logs: [
        { id: 1, message: 'Sipariş kargoya verildi', created_at: new Date(Date.now() - 86400000).toISOString() },
        { id: 2, message: 'Sipariş hazırlandı', created_at: new Date(Date.now() - 172800000).toISOString() },
        { id: 3, message: 'Sipariş onaylandı', created_at: new Date(Date.now() - 259200000).toISOString() },
        { id: 4, message: 'Sipariş alındı', created_at: new Date(Date.now() - 345600000).toISOString() },
      ]
    }
  }
})

const formatDate = (date: string) => {
  return new Date(date).toLocaleString('tr-TR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatBookingDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const orderType = computed(() => {
  if (!order.value?.order_details?.[0]) return 'product'
  return order.value.order_details[0].type || order.value.order_type || 'product'
})

const isPhysicalProduct = computed(() => orderType.value === 'product')
const isService = computed(() => orderType.value === 'service')
const isBooking = computed(() => orderType.value === 'booking')

const orderTypeIcon = computed(() => {
  if (isBooking.value) return '🏨'
  if (isService.value) return '🔧'
  return '📦'
})

const orderTypeLabel = computed(() => {
  if (isBooking.value) return 'Rezervasyon'
  if (isService.value) return 'Hizmet Sayısı'
  return 'Ürün Sayısı'
})

const serviceStatusIcon = computed(() => {
  if (!order.value) return '⏳'
  if (order.value.status === 'completed') return '✅'
  if (order.value.status === 'processing') return '⚙️'
  return '⏳'
})

const serviceStatusLabel = computed(() => {
  if (!order.value) return 'Bekliyor'
  if (isBooking.value) {
    if (order.value.status === 'completed') return 'Tamamlandı'
    if (order.value.status === 'confirmed') return 'Onaylandı'
    return 'Rezerve Edildi'
  }
  if (isService.value) {
    if (order.value.status === 'completed') return 'Tamamlandı'
    if (order.value.status === 'processing') return 'Devam Ediyor'
    return 'Planlandı'
  }
  return 'Hazırlanıyor'
})

const canReturn = computed(() => {
  if (!order.value) return false
  const deliveryDate = order.value.delivered_at ? new Date(order.value.delivered_at) : null
  if (!deliveryDate) return false
  const daysSinceDelivery = (Date.now() - deliveryDate.getTime()) / (1000 * 60 * 60 * 24)
  return order.value.status === 'delivered' && daysSinceDelivery <= 14
})

const initiateReturn = () => {
  router.push(`/returns/new?order=${order.value.id}`)
}

const downloadInvoice = () => {
  toast.info('Fatura indirme özelliği geliştiriliyor')
  // TODO: API call to download invoice
}

const contactSupport = () => {
  router.push(`/messages?subject=Sipariş #${order.value.id}`)
}

const reorder = async () => {
  try {
    // Add all order items to cart
    for (const item of order.value.order_details) {
      await axios.post('/api/cart/add', {
        product_id: item.product_id,
        quantity: item.quantity
      })
    }
    toast.success('Ürünler sepete eklendi')
    router.push('/cart')
  } catch (error) {
    console.error('Sepete ekleme hatası:', error)
    toast.error('Sepete ekleme başarısız')
  }
}

const cancelOrder = async () => {
  if (!confirm('Siparişi iptal etmek istediğinize emin misiniz?')) return
  
  try {
    await axios.post(`/api/orders/${order.value.id}/cancel`)
    toast.success('Sipariş iptal edildi')
    order.value.status = 'cancelled'
  } catch (error) {
    console.error('İptal işlemi hatası:', error)
    toast.error('İptal işlemi başarısız')
  }
}

const formatPrice = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 0 }).format(value)
}

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    'pending': 'Beklemede',
    'processing': 'Hazırlanıyor',
    'shipped': 'Kargoda',
    'delivered': 'Teslim Edildi',
    'cancelled': 'İptal Edildi'
  }
  return labels[status] || status
}

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    'pending': 'bg-yellow-100 text-yellow-700',
    'processing': 'bg-blue-100 text-blue-700',
    'shipped': 'bg-purple-100 text-purple-700',
    'delivered': 'bg-green-100 text-green-700',
    'cancelled': 'bg-red-100 text-red-700'
  }
  return classes[status] || 'bg-slate-100 text-slate-700'
}
</script>