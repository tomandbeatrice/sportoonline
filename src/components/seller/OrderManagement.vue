<template>
  <div class="order-management p-6">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Sipariş Yönetimi</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="text-sm text-gray-600">Bekleyen</div>
        <div class="text-2xl font-bold text-yellow-600">{{ stats.pending || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="text-sm text-gray-600">Hazırlanıyor</div>
        <div class="text-2xl font-bold text-blue-600">{{ stats.processing || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="text-sm text-gray-600">Kargoda</div>
        <div class="text-2xl font-bold text-purple-600">{{ stats.shipped || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="text-sm text-gray-600">Tamamlanan</div>
        <div class="text-2xl font-bold text-green-600">{{ stats.delivered || 0 }}</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Sipariş No veya Müşteri Ara..."
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        />
        <select
          v-model="filters.status"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Tüm Durumlar</option>
          <option value="pending">Bekleyen</option>
          <option value="processing">Hazırlanıyor</option>
          <option value="shipped">Kargoda</option>
          <option value="delivered">Teslim Edildi</option>
          <option value="cancelled">İptal Edildi</option>
        </select>
        <select
          v-model="filters.payment"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Ödeme Durumu</option>
          <option value="paid">Ödendi</option>
          <option value="pending">Ödeme Bekliyor</option>
        </select>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sipariş No</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tutar</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ödeme</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-semibold text-gray-900">#{{ order.id }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ order.name || order.user?.name }}</div>
                <div class="text-sm text-gray-500">{{ order.phone }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(order.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-semibold text-gray-900">₺{{ formatMoney(order.total) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': order.payment_status === 'paid',
                    'bg-yellow-100 text-yellow-800': order.payment_status === 'pending'
                  }"
                >
                  {{ order.payment_status === 'paid' ? 'Ödendi' : 'Bekliyor' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <select
                  :value="order.status"
                  @change="updateOrderStatus(order, $event.target.value)"
                  class="text-xs font-semibold rounded-full px-3 py-1 border-0"
                  :class="getStatusClass(order.status)"
                >
                  <option value="pending">Bekleyen</option>
                  <option value="processing">Hazırlanıyor</option>
                  <option value="shipped">Kargoda</option>
                  <option value="delivered">Teslim Edildi</option>
                  <option value="cancelled">İptal Edildi</option>
                </select>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="viewOrderDetails(order)"
                  class="text-blue-600 hover:text-blue-900 mr-3"
                >
                  Detay
                </button>
                <button
                  v-if="order.status === 'processing'"
                  @click="openShippingModal(order)"
                  class="text-green-600 hover:text-green-900"
                >
                  Kargo Ekle
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="filteredOrders.length === 0" class="text-center py-12 text-gray-500">
        Sipariş bulunamadı
      </div>
    </div>

    <!-- Shipping Modal -->
    <div v-if="showShippingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Kargo Bilgileri</h2>

        <form @submit.prevent="addShipping" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kargo Firması *</label>
            <select
              v-model="shippingForm.company"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Seçiniz</option>
              <option value="Aras Kargo">Aras Kargo</option>
              <option value="Yurtiçi Kargo">Yurtiçi Kargo</option>
              <option value="MNG Kargo">MNG Kargo</option>
              <option value="PTT Kargo">PTT Kargo</option>
              <option value="Sürat Kargo">Sürat Kargo</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Takip Numarası *</label>
            <input
              v-model="shippingForm.tracking_number"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div class="flex gap-3 pt-4 border-t">
            <button
              type="button"
              @click="closeShippingModal"
              class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold"
            >
              İptal
            </button>
            <button
              type="submit"
              :disabled="savingShipping"
              class="flex-1 px-6 py-2 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 text-white rounded-lg font-semibold"
            >
              {{ savingShipping ? 'Kaydediliyor...' : 'Kaydet' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="selectedOrder" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold text-gray-900">Sipariş #{{ selectedOrder.id }}</h2>
          <button @click="selectedOrder = null" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4">
          <!-- Customer Info -->
          <div class="border-b pb-4">
            <h3 class="font-semibold text-gray-900 mb-2">Müşteri Bilgileri</h3>
            <p class="text-sm text-gray-700">{{ selectedOrder.name }}</p>
            <p class="text-sm text-gray-700">{{ selectedOrder.phone }}</p>
            <p class="text-sm text-gray-700">{{ selectedOrder.email }}</p>
          </div>

          <!-- Shipping Address -->
          <div class="border-b pb-4">
            <h3 class="font-semibold text-gray-900 mb-2">Teslimat Adresi</h3>
            <p class="text-sm text-gray-700">{{ selectedOrder.shipping_address }}</p>
          </div>

          <!-- Order Items -->
          <div>
            <h3 class="font-semibold text-gray-900 mb-2">Ürünler</h3>
            <div class="space-y-2">
              <div
                v-for="item in selectedOrder.items"
                :key="item.id"
                class="flex justify-between text-sm"
              >
                <span>{{ item.product_name }} x {{ item.quantity }}</span>
                <span class="font-semibold">₺{{ formatMoney(item.total_price) }}</span>
              </div>
            </div>
            <div class="mt-4 pt-4 border-t flex justify-between font-bold">
              <span>Toplam</span>
              <span>₺{{ formatMoney(selectedOrder.total) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const orders = ref([])
const selectedOrder = ref(null)
const showShippingModal = ref(false)
const currentOrder = ref(null)
const savingShipping = ref(false)

const stats = ref({
  pending: 0,
  processing: 0,
  shipped: 0,
  delivered: 0
})

const filters = ref({
  search: '',
  status: '',
  payment: ''
})

const shippingForm = ref({
  company: '',
  tracking_number: ''
})

const filteredOrders = computed(() => {
  let result = orders.value

  if (filters.value.search) {
    result = result.filter(o =>
      o.id.toString().includes(filters.value.search) ||
      o.name?.toLowerCase().includes(filters.value.search.toLowerCase())
    )
  }

  if (filters.value.status) {
    result = result.filter(o => o.status === filters.value.status)
  }

  if (filters.value.payment) {
    result = result.filter(o => o.payment_status === filters.value.payment)
  }

  return result
})

const loadOrders = async () => {
  try {
    const { data } = await axios.get('/api/seller/orders')
    orders.value = data.orders || data
    
    // Calculate stats
    stats.value = {
      pending: orders.value.filter(o => o.status === 'pending').length,
      processing: orders.value.filter(o => o.status === 'processing').length,
      shipped: orders.value.filter(o => o.status === 'shipped').length,
      delivered: orders.value.filter(o => o.status === 'delivered').length
    }
  } catch (error) {
    console.error('Failed to load orders:', error)
  }
}

const updateOrderStatus = async (order, newStatus) => {
  try {
    await axios.put(`/api/orders/${order.id}/status`, { status: newStatus })
    order.status = newStatus
    await loadOrders()
  } catch (error) {
    console.error('Failed to update order status:', error)
    alert('Sipariş durumu güncellenemedi')
  }
}

const viewOrderDetails = async (order) => {
  try {
    const { data } = await axios.get(`/api/orders/${order.id}`)
    selectedOrder.value = data.order || data
  } catch (error) {
    console.error('Failed to load order details:', error)
  }
}

const openShippingModal = (order) => {
  currentOrder.value = order
  shippingForm.value = {
    company: '',
    tracking_number: ''
  }
  showShippingModal.value = true
}

const closeShippingModal = () => {
  showShippingModal.value = false
  currentOrder.value = null
}

const addShipping = async () => {
  savingShipping.value = true
  try {
    await axios.put(`/api/orders/${currentOrder.value.id}/ship`, {
      shipping_company: shippingForm.value.company,
      shipping_tracking_number: shippingForm.value.tracking_number,
      status: 'shipped'
    })
    await loadOrders()
    closeShippingModal()
  } catch (error) {
    console.error('Failed to add shipping:', error)
    alert('Kargo bilgileri eklenemedi')
  } finally {
    savingShipping.value = false
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

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatMoney = (amount) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

onMounted(() => {
  loadOrders()
})
</script>
