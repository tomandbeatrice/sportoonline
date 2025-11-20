<template>
  <div class="buyer-orders">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Siparişlerim</h2>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Sipariş No veya Ürün Ara..."
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        />
        <select
          v-model="filters.status"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tüm Durumlar</option>
          <option value="pending">Bekliyor</option>
          <option value="processing">Hazırlanıyor</option>
          <option value="shipped">Kargoda</option>
          <option value="delivered">Teslim Edildi</option>
          <option value="cancelled">İptal Edildi</option>
        </select>
        <select
          v-model="filters.dateRange"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tüm Zamanlar</option>
          <option value="7">Son 7 Gün</option>
          <option value="30">Son 30 Gün</option>
          <option value="90">Son 3 Ay</option>
          <option value="365">Son 1 Yıl</option>
        </select>
        <button
          @click="resetFilters"
          class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium"
        >
          Filtreleri Temizle
        </button>
      </div>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
      <div
        v-for="order in filteredOrders"
        :key="order.id"
        class="bg-white rounded-xl shadow-sm overflow-hidden"
      >
        <div class="p-6">
          <div class="flex flex-wrap items-center justify-between mb-4">
            <div class="flex items-center gap-4">
              <span class="text-lg font-bold text-gray-900">Sipariş #{{ order.id }}</span>
              <span class="px-3 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(order.status)">
                {{ getStatusLabel(order.status) }}
              </span>
            </div>
            <div class="text-right">
              <div class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</div>
              <div class="text-lg font-bold text-gray-900">₺{{ formatMoney(order.total) }}</div>
            </div>
          </div>

          <!-- Order Items -->
          <div class="space-y-3 mb-4">
            <div
              v-for="item in order.items"
              :key="item.id"
              class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg"
            >
              <div class="w-16 h-16 bg-white rounded-lg overflow-hidden flex-shrink-0">
                <img
                  v-if="item.product?.image_url"
                  :src="item.product.image_url"
                  :alt="item.product.name"
                  class="w-full h-full object-cover"
                />
              </div>
              <div class="flex-1">
                <div class="font-medium text-gray-900">{{ item.product?.name }}</div>
                <div class="text-sm text-gray-500">Adet: {{ item.quantity }}</div>
              </div>
              <div class="text-right">
                <div class="font-semibold text-gray-900">₺{{ formatMoney(item.price * item.quantity) }}</div>
              </div>
            </div>
          </div>

          <!-- Shipping Info -->
          <div v-if="order.shipping_tracking_number" class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="font-semibold text-blue-900">Kargo Bilgileri</span>
            </div>
            <div class="text-sm text-blue-800">
              <div>Kargo Firması: {{ order.shipping_company }}</div>
              <div>Takip No: <span class="font-mono font-semibold">{{ order.shipping_tracking_number }}</span></div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-wrap gap-3">
            <button
              @click="viewOrderDetails(order)"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm"
            >
              Detayları Gör
            </button>
            <button
              v-if="order.shipping_tracking_number"
              @click="trackShipment(order.shipping_tracking_number)"
              class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-medium text-sm"
            >
              <span class="inline-flex items-center gap-2"><BadgeIcon name="truck" cls="w-4 h-4 text-gray-700" />Kargo Takip</span>
            </button>
            <button
              @click="downloadInvoice(order.id)"
              class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-medium text-sm"
            >
              <span class="inline-flex items-center gap-2"><BadgeIcon name="file" cls="w-4 h-4 text-gray-700" />Fatura İndir</span>
            </button>
            <button
              v-if="order.status === 'pending' || order.status === 'processing'"
              @click="requestCancellation(order)"
              class="px-4 py-2 border border-red-300 hover:bg-red-50 text-red-600 rounded-lg font-medium text-sm"
            >
              <span class="inline-flex items-center gap-2 text-red-600"><BadgeIcon name="close" cls="w-4 h-4" />İptal Talebi</span>
            </button>
          </div>
        </div>
      </div>

      <div v-if="filteredOrders.length === 0" class="bg-white rounded-xl shadow-sm p-12 text-center">
              <div class="text-6xl mb-4"><BadgeIcon name="box" cls="w-12 h-12" /></div>
        <div class="text-xl font-semibold text-gray-900 mb-2">Sipariş Bulunamadı</div>
        <div class="text-gray-500 mb-6">Filtrelere uygun sipariş bulunmuyor</div>
        <button
          @click="resetFilters"
          class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold"
        >
          Tüm Siparişleri Göster
        </button>
      </div>
    </div>

    <!-- Order Detail Modal -->
    <div v-if="selectedOrder" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b p-6 flex justify-between items-center">
          <h3 class="text-xl font-bold text-gray-900">Sipariş Detayı #{{ selectedOrder.id }}</h3>
          <button @click="selectedOrder = null" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-6 space-y-6">
          <!-- Customer Info -->
          <div>
            <h4 class="font-semibold text-gray-900 mb-3">Müşteri Bilgileri</h4>
            <div class="bg-gray-50 rounded-lg p-4 space-y-1 text-sm">
              <div><span class="text-gray-600">Ad Soyad:</span> {{ selectedOrder.name }}</div>
              <div><span class="text-gray-600">Telefon:</span> {{ selectedOrder.phone }}</div>
              <div><span class="text-gray-600">E-posta:</span> {{ selectedOrder.email }}</div>
            </div>
          </div>

          <!-- Shipping Address -->
          <div>
            <h4 class="font-semibold text-gray-900 mb-3">Teslimat Adresi</h4>
            <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700">
              {{ selectedOrder.shipping_address }}
            </div>
          </div>

          <!-- Order Items -->
          <div>
            <h4 class="font-semibold text-gray-900 mb-3">Sipariş Ürünleri</h4>
            <div class="space-y-3">
              <div
                v-for="item in selectedOrder.items"
                :key="item.id"
                class="flex justify-between items-center p-3 bg-gray-50 rounded-lg"
              >
                <div>
                  <div class="font-medium">{{ item.product?.name }}</div>
                  <div class="text-sm text-gray-500">{{ item.quantity }} adet × ₺{{ formatMoney(item.price) }}</div>
                </div>
                <div class="font-semibold">₺{{ formatMoney(item.price * item.quantity) }}</div>
              </div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="border-t pt-4">
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Ara Toplam:</span>
                <span class="font-medium">₺{{ formatMoney(selectedOrder.total) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Kargo:</span>
                <span class="font-medium">₺{{ formatMoney(selectedOrder.shipping_cost || 0) }}</span>
              </div>
              <div class="flex justify-between text-lg font-bold pt-2 border-t">
                <span>Toplam:</span>
                <span>₺{{ formatMoney(selectedOrder.total + (selectedOrder.shipping_cost || 0)) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'
import axios from 'axios'

const orders = ref([])
const selectedOrder = ref(null)

const filters = ref({
  search: '',
  status: '',
  dateRange: ''
})

const filteredOrders = computed(() => {
  let result = orders.value

  if (filters.value.search) {
    result = result.filter(o =>
      o.id.toString().includes(filters.value.search) ||
      o.items?.some(item => item.product?.name.toLowerCase().includes(filters.value.search.toLowerCase()))
    )
  }

  if (filters.value.status) {
    result = result.filter(o => o.status === filters.value.status)
  }

  if (filters.value.dateRange) {
    const days = parseInt(filters.value.dateRange)
    const cutoffDate = new Date()
    cutoffDate.setDate(cutoffDate.getDate() - days)
    result = result.filter(o => new Date(o.created_at) >= cutoffDate)
  }

  return result
})

const loadOrders = async () => {
  try {
    const { data } = await axios.get('/api/buyer/orders')
    orders.value = data.orders || data
  } catch (error) {
    console.error('Siparişler yüklenemedi:', error)
  }
}

const viewOrderDetails = (order) => {
  selectedOrder.value = order
}

const trackShipment = (trackingNumber) => {
  window.open(`https://gonderitakip.ptt.gov.tr/Track/Verify?q=${trackingNumber}`, '_blank')
}

const downloadInvoice = async (orderId) => {
  try {
    const response = await axios.get(`/api/orders/${orderId}/invoice`, {
      responseType: 'blob'
    })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `fatura-${orderId}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
  } catch (error) {
    console.error('Fatura indirilemedi:', error)
    alert('Fatura indirme işlemi başarısız')
  }
}

const requestCancellation = async (order) => {
  if (!confirm(`Sipariş #${order.id} için iptal talebi göndermek istediğinize emin misiniz?`)) return

  try {
    await axios.post(`/api/orders/${order.id}/cancel-request`)
    alert('İptal talebiniz alınmıştır. En kısa sürede değerlendirilecektir.')
    await loadOrders()
  } catch (error) {
    console.error('İptal talebi gönderilemedi:', error)
    alert('İptal talebi işlemi başarısız')
  }
}

const resetFilters = () => {
  filters.value = {
    search: '',
    status: '',
    dateRange: ''
  }
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Bekliyor',
    processing: 'Hazırlanıyor',
    shipped: 'Kargoda',
    delivered: 'Teslim Edildi',
    cancelled: 'İptal Edildi'
  }
  return labels[status] || status
}

const formatMoney = (amount) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount || 0)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  loadOrders()
})
</script>
