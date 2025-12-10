<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Yemek Siparişleri</h1>
        <p class="text-gray-600">Tüm yemek siparişlerini takip edin</p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Bugün Toplam</div>
        <div class="text-2xl font-bold text-gray-900">{{ stats.todays_orders || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Beklemede</div>
        <div class="text-2xl font-bold text-yellow-600">{{ stats.pending || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Hazırlanıyor</div>
        <div class="text-2xl font-bold text-blue-600">{{ stats.preparing || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Yolda</div>
        <div class="text-2xl font-bold text-purple-600">{{ stats.out_for_delivery || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Bugünkü Gelir</div>
        <div class="text-2xl font-bold text-green-600">₺{{ stats.revenue_today?.toLocaleString() || 0 }}</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Sipariş ID veya müşteri..."
          class="border rounded-lg px-4 py-2"
        />
        <select v-model="filters.status" class="border rounded-lg px-4 py-2">
          <option value="">Tüm Durumlar</option>
          <option value="pending">Beklemede</option>
          <option value="confirmed">Onaylandı</option>
          <option value="preparing">Hazırlanıyor</option>
          <option value="ready">Hazır</option>
          <option value="out_for_delivery">Yolda</option>
          <option value="delivered">Teslim Edildi</option>
          <option value="cancelled">İptal</option>
        </select>
        <select v-model="filters.restaurant_id" class="border rounded-lg px-4 py-2">
          <option value="">Tüm Restoranlar</option>
          <option v-for="r in restaurants" :key="r.id" :value="r.id">{{ r.name }}</option>
        </select>
        <input
          v-model="filters.date"
          type="date"
          class="border rounded-lg px-4 py-2"
        />
        <button @click="fetchOrders" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">
          🔍 Filtrele
        </button>
      </div>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
      <div
        v-for="order in orders"
        :key="order.id"
        class="bg-white rounded-lg shadow p-4"
      >
        <div class="flex justify-between items-start">
          <div class="flex items-start space-x-4">
            <!-- Order Status Indicator -->
            <div :class="getStatusIndicatorClass(order.status)" class="w-3 h-3 rounded-full mt-2"></div>
            
            <div>
              <div class="flex items-center space-x-3">
                <span class="font-bold text-lg">#{{ order.id }}</span>
                <span
                  :class="getStatusClass(order.status)"
                  class="px-2 py-1 text-xs rounded-full"
                >
                  {{ getStatusLabel(order.status) }}
                </span>
              </div>
              <div class="text-sm text-gray-500 mt-1">
                {{ order.restaurant?.name }} • {{ formatTime(order.created_at) }}
              </div>
            </div>
          </div>
          
          <div class="text-right">
            <div class="font-bold text-lg">₺{{ order.total?.toLocaleString() }}</div>
            <div class="text-sm text-gray-500">{{ order.payment_method }}</div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="mt-4 pt-4 border-t">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <h4 class="font-medium text-sm text-gray-700 mb-2">Ürünler</h4>
              <ul class="text-sm space-y-1">
                <li v-for="item in order.items" :key="item.id">
                  {{ item.quantity }}x {{ item.name }} - ₺{{ item.price }}
                </li>
              </ul>
            </div>
            <div>
              <h4 class="font-medium text-sm text-gray-700 mb-2">Teslimat</h4>
              <div class="text-sm">
                <p class="font-medium">{{ order.user?.name }}</p>
                <p class="text-gray-500">{{ order.delivery_address }}</p>
                <p class="text-gray-500">{{ order.user?.phone }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-4 pt-4 border-t flex justify-between items-center">
          <div class="flex space-x-2">
            <select
              v-model="order.status"
              @change="updateOrderStatus(order)"
              class="border rounded px-3 py-1 text-sm"
            >
              <option value="pending">Beklemede</option>
              <option value="confirmed">Onaylandı</option>
              <option value="preparing">Hazırlanıyor</option>
              <option value="ready">Hazır</option>
              <option value="out_for_delivery">Yolda</option>
              <option value="delivered">Teslim Edildi</option>
              <option value="cancelled">İptal</option>
            </select>
            <button
              v-if="!order.driver_id && order.status === 'ready'"
              @click="assignDriver(order)"
              class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm"
            >
              Kurye Ata
            </button>
          </div>
          <div class="flex space-x-2">
            <span v-if="order.driver" class="text-sm text-gray-600">
              🚴 {{ order.driver.name }}
            </span>
            <button @click="printOrder(order)" class="text-gray-600 text-sm">
              🖨️ Yazdır
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center space-x-2">
      <button
        @click="changePage(pagination.current_page - 1)"
        :disabled="pagination.current_page === 1"
        class="px-4 py-2 border rounded-lg disabled:opacity-50"
      >
        Önceki
      </button>
      <span class="px-4 py-2">{{ pagination.current_page }} / {{ pagination.last_page }}</span>
      <button
        @click="changePage(pagination.current_page + 1)"
        :disabled="pagination.current_page === pagination.last_page"
        class="px-4 py-2 border rounded-lg disabled:opacity-50"
      >
        Sonraki
      </button>
    </div>

    <!-- Driver Assignment Modal -->
    <div v-if="showDriverModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold mb-4">Kurye Ata</h3>
        <select v-model="selectedDriverId" class="w-full border rounded-lg px-4 py-2 mb-4">
          <option value="">Kurye seçin...</option>
          <option v-for="driver in availableDrivers" :key="driver.id" :value="driver.id">
            {{ driver.name }} - {{ driver.phone }}
          </option>
        </select>
        <div class="flex justify-end space-x-3">
          <button @click="showDriverModal = false" class="px-4 py-2 border rounded-lg">
            İptal
          </button>
          <button @click="confirmAssignDriver" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
            Ata
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const orders = ref([])
const restaurants = ref([])
const stats = ref({})
const availableDrivers = ref([])
const showDriverModal = ref(false)
const selectedOrderId = ref(null)
const selectedDriverId = ref('')

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0
})

const filters = reactive({
  search: '',
  status: '',
  restaurant_id: '',
  date: ''
})

const fetchOrders = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.status) params.append('status', filters.status)
    if (filters.restaurant_id) params.append('restaurant_id', filters.restaurant_id)
    if (filters.date) params.append('date', filters.date)
    params.append('page', pagination.value.current_page)

    const response = await axios.get(`/api/admin/restaurants/orders?${params}`)
    orders.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total
    }
  } catch (error) {
    console.error('Error fetching orders:', error)
  }
}

const fetchRestaurants = async () => {
  try {
    const response = await axios.get('/api/admin/restaurants?per_page=100')
    restaurants.value = response.data.data
  } catch (error) {
    console.error('Error fetching restaurants:', error)
  }
}

const fetchStats = async () => {
  try {
    const response = await axios.get('/api/admin/restaurants/stats')
    stats.value = response.data
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

const fetchDrivers = async () => {
  try {
    const response = await axios.get('/api/admin/transport/drivers?status=active')
    availableDrivers.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching drivers:', error)
  }
}

const updateOrderStatus = async (order) => {
  try {
    await axios.put(`/api/admin/restaurants/orders/${order.id}/status`, {
      status: order.status
    })
    fetchStats()
  } catch (error) {
    console.error('Error updating status:', error)
    fetchOrders()
  }
}

const assignDriver = (order) => {
  selectedOrderId.value = order.id
  showDriverModal.value = true
  fetchDrivers()
}

const confirmAssignDriver = async () => {
  if (!selectedDriverId.value) return
  
  try {
    await axios.post(`/api/admin/restaurants/orders/${selectedOrderId.value}/assign-driver`, {
      driver_id: selectedDriverId.value
    })
    showDriverModal.value = false
    selectedDriverId.value = ''
    fetchOrders()
  } catch (error) {
    console.error('Error assigning driver:', error)
  }
}

const printOrder = (order) => {
  console.log('Print order:', order.id)
}

const changePage = (page) => {
  pagination.value.current_page = page
  fetchOrders()
}

const formatTime = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleString('tr-TR', {
    hour: '2-digit',
    minute: '2-digit',
    day: '2-digit',
    month: '2-digit'
  })
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    preparing: 'bg-orange-100 text-orange-800',
    ready: 'bg-purple-100 text-purple-800',
    out_for_delivery: 'bg-indigo-100 text-indigo-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusIndicatorClass = (status) => {
  const classes = {
    pending: 'bg-yellow-500',
    confirmed: 'bg-blue-500',
    preparing: 'bg-orange-500 animate-pulse',
    ready: 'bg-purple-500',
    out_for_delivery: 'bg-indigo-500 animate-pulse',
    delivered: 'bg-green-500',
    cancelled: 'bg-red-500'
  }
  return classes[status] || 'bg-gray-500'
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Beklemede',
    confirmed: 'Onaylandı',
    preparing: 'Hazırlanıyor',
    ready: 'Hazır',
    out_for_delivery: 'Yolda',
    delivered: 'Teslim Edildi',
    cancelled: 'İptal'
  }
  return labels[status] || status
}

onMounted(() => {
  fetchOrders()
  fetchRestaurants()
  fetchStats()
})
</script>
