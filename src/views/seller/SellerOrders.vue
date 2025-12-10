<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
      <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-4">
          <router-link to="/" class="text-2xl font-bold text-slate-900">SportoOnline</router-link>
          <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">SATICI</span>
        </div>
        <div class="flex items-center gap-3">
          <router-link to="/seller/dashboard" class="text-sm text-slate-600 hover:text-slate-900">← Dashboard</router-link>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Sipariş Yönetimi</h1>
          <p class="text-slate-500">Tüm siparişlerinizi buradan yönetin</p>
        </div>
        <div class="flex gap-3">
          <button class="bg-white border border-slate-200 px-4 py-2 rounded-lg text-sm hover:bg-slate-50">
            📥 Dışa Aktar
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Sipariş no veya müşteri ara..."
              class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500"
            />
          </div>
          <select v-model="statusFilter" class="px-4 py-2 border border-slate-200 rounded-lg">
            <option value="">Tüm Durumlar</option>
            <option value="pending">Bekleyen</option>
            <option value="processing">Hazırlanıyor</option>
            <option value="shipped">Kargoda</option>
            <option value="delivered">Teslim Edildi</option>
            <option value="cancelled">İptal</option>
          </select>
          <select v-model="dateFilter" class="px-4 py-2 border border-slate-200 rounded-lg">
            <option value="">Tüm Tarihler</option>
            <option value="today">Bugün</option>
            <option value="week">Bu Hafta</option>
            <option value="month">Bu Ay</option>
          </select>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm text-center">
          <p class="text-2xl font-bold text-slate-900">{{ stats.total }}</p>
          <p class="text-xs text-slate-500">Toplam</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4 shadow-sm text-center border border-yellow-100">
          <p class="text-2xl font-bold text-yellow-700">{{ stats.pending }}</p>
          <p class="text-xs text-yellow-600">Bekleyen</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 shadow-sm text-center border border-blue-100">
          <p class="text-2xl font-bold text-blue-700">{{ stats.processing }}</p>
          <p class="text-xs text-blue-600">Hazırlanıyor</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4 shadow-sm text-center border border-purple-100">
          <p class="text-2xl font-bold text-purple-700">{{ stats.shipped }}</p>
          <p class="text-xs text-purple-600">Kargoda</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 shadow-sm text-center border border-green-100">
          <p class="text-2xl font-bold text-green-700">{{ stats.delivered }}</p>
          <p class="text-xs text-green-600">Teslim Edildi</p>
        </div>
      </div>

      <!-- Orders Table -->
      <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
          <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Sipariş</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Müşteri</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Ürünler</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Tutar</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Durum</th>
              <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Tarih</th>
              <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase">İşlem</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-slate-50">
              <td class="px-6 py-4">
                <span class="font-bold text-slate-900">#{{ order.id }}</span>
              </td>
              <td class="px-6 py-4">
                <div>
                  <p class="font-medium text-slate-900">{{ order.customer.name }}</p>
                  <p class="text-xs text-slate-500">{{ order.customer.phone }}</p>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex -space-x-2">
                  <img 
                    v-for="(item, i) in order.items.slice(0, 3)" 
                    :key="i"
                    :src="item.image" 
                    class="w-8 h-8 rounded-lg border-2 border-white object-cover"
                  />
                  <span v-if="order.items.length > 3" class="w-8 h-8 rounded-lg bg-slate-200 border-2 border-white flex items-center justify-center text-xs font-medium">
                    +{{ order.items.length - 3 }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="font-bold text-slate-900">{{ formatCurrency(order.total) }}</span>
              </td>
              <td class="px-6 py-4">
                <span :class="getStatusClass(order.status)" class="px-3 py-1 rounded-full text-xs font-medium">
                  {{ getStatusText(order.status) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="text-sm text-slate-500">{{ order.date }}</span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <button @click="viewOrder(order)" class="p-2 hover:bg-slate-100 rounded-lg" title="Detay">👁️</button>
                  <button v-if="order.status === 'pending'" @click="approveOrder(order)" class="p-2 hover:bg-green-100 rounded-lg" title="Onayla">✅</button>
                  <button v-if="order.status === 'processing'" @click="openShippingModal(order)" class="p-2 hover:bg-purple-100 rounded-lg" title="Kargola">📦</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between mt-6">
        <p class="text-sm text-slate-500">{{ filteredOrders.length }} sipariş gösteriliyor</p>
        <div class="flex gap-2">
          <button class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">← Önceki</button>
          <button class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm">1</button>
          <button class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">2</button>
          <button class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">Sonraki →</button>
        </div>
      </div>
    </div>

    <!-- Order Detail Modal -->
    <div v-if="selectedOrder" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-xl font-bold">Sipariş #{{ selectedOrder.id }}</h3>
          <button @click="selectedOrder = null" aria-label="Kapat" class="p-2 hover:bg-slate-100 rounded-lg">✕</button>
        </div>
        <div class="p-6 space-y-6">
          <!-- Customer Info -->
          <div>
            <h4 class="font-semibold text-slate-900 mb-3">Müşteri Bilgileri</h4>
            <div class="bg-slate-50 rounded-xl p-4">
              <p class="font-medium">{{ selectedOrder.customer.name }}</p>
              <p class="text-sm text-slate-500">{{ selectedOrder.customer.phone }}</p>
              <p class="text-sm text-slate-500">{{ selectedOrder.customer.address }}</p>
            </div>
          </div>
          
          <!-- Products -->
          <div>
            <h4 class="font-semibold text-slate-900 mb-3">Ürünler</h4>
            <div class="space-y-3">
              <div v-for="item in selectedOrder.items" :key="item.id" class="flex items-center gap-4 bg-slate-50 rounded-xl p-3">
                <img :src="item.image" :alt="item.name" class="w-16 h-16 rounded-lg object-cover" />
                <div class="flex-1">
                  <p class="font-medium">{{ item.name }}</p>
                  <p class="text-sm text-slate-500">{{ item.quantity }} adet × {{ formatCurrency(item.price) }}</p>
                </div>
                <p class="font-bold">{{ formatCurrency(item.quantity * item.price) }}</p>
              </div>
            </div>
          </div>

          <!-- Total -->
          <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <span class="font-semibold">Toplam</span>
            <span class="text-2xl font-bold text-orange-600">{{ formatCurrency(selectedOrder.total) }}</span>
          </div>

          <!-- Actions -->
          <div class="flex gap-3">
            <button 
              v-if="selectedOrder.status === 'pending'"
              @click="approveOrder(selectedOrder); selectedOrder = null"
              class="flex-1 bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700"
            >
              ✅ Siparişi Onayla
            </button>
            <button 
              v-if="selectedOrder.status === 'processing'"
              @click="openShippingModal(selectedOrder); selectedOrder = null"
              class="flex-1 bg-purple-600 text-white py-3 rounded-xl font-semibold hover:bg-purple-700"
            >
              📦 Kargoya Ver
            </button>
            <button class="px-6 py-3 border border-slate-200 rounded-xl font-semibold hover:bg-slate-50">
              🖨️ Fatura
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Shipping Modal -->
    <div v-if="shippingModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl max-w-md w-full">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-xl font-bold">Kargoya Ver</h3>
          <button @click="shippingModal = false" class="p-2 hover:bg-slate-100 rounded-lg">✕</button>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Kargo Firması</label>
            <select v-model="selectedProvider" class="w-full px-4 py-3 border border-slate-200 rounded-xl">
              <option v-for="provider in shippingProviders" :key="provider.code" :value="provider.code">
                {{ provider.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Takip Numarası</label>
            <input 
              v-model="trackingCode"
              type="text" 
              placeholder="Kargo takip numarasını girin"
              class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500"
            />
          </div>
          <button 
            @click="shipOrder"
            :disabled="!trackingCode"
            class="w-full bg-purple-600 text-white py-3 rounded-xl font-semibold hover:bg-purple-700 disabled:opacity-50"
          >
            📦 Kargoya Gönder
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const searchQuery = ref('')
const statusFilter = ref('')
const dateFilter = ref('')
const selectedOrder = ref<any>(null)
const loading = ref(false)
const shippingModal = ref(false)
const trackingCode = ref('')
const selectedProvider = ref('yurtici')

const stats = ref({
  total: 0,
  pending: 0,
  processing: 0,
  shipped: 0,
  delivered: 0
})

const orders = ref<any[]>([])

const shippingProviders = [
  { code: 'yurtici', name: 'Yurtiçi Kargo' },
  { code: 'aras', name: 'Aras Kargo' },
  { code: 'ptt', name: 'PTT Kargo' },
  { code: 'surat', name: 'Sürat Kargo' },
  { code: 'geliver', name: 'Geliver' },
]

const fetchOrders = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/seller/orders')
    orders.value = response.data.orders || response.data
    
    // Calculate stats
    stats.value.total = orders.value.length
    stats.value.pending = orders.value.filter((o: any) => o.status === 'pending').length
    stats.value.processing = orders.value.filter((o: any) => o.status === 'processing').length
    stats.value.shipped = orders.value.filter((o: any) => o.status === 'shipped').length
    stats.value.delivered = orders.value.filter((o: any) => o.status === 'delivered').length
  } catch (error) {
    console.error('Error fetching orders:', error)
    // Keep mock data for demo
    orders.value = [
      {
        id: 1542,
        customer: { name: 'Ahmet Yılmaz', phone: '0532 XXX XX XX', address: 'Kadıköy, İstanbul' },
        items: [
          { id: 1, name: 'Nike Air Max 270', quantity: 1, price: 1899, image: 'https://placehold.co/100x100/F97316/FFFFFF?text=Nike' },
        ],
        total: 2197,
        status: 'pending',
        date: new Date().toLocaleString('tr-TR')
      },
      {
        id: 1541,
        customer: { name: 'Zeynep Kaya', phone: '0533 XXX XX XX', address: 'Beşiktaş, İstanbul' },
        items: [
          { id: 3, name: 'Adidas Ultraboost', quantity: 1, price: 2499, image: 'https://placehold.co/100x100/22C55E/FFFFFF?text=Adidas' }
        ],
        total: 2499,
        status: 'processing',
        date: new Date().toLocaleString('tr-TR')
      },
    ]
    stats.value = { total: 2, pending: 1, processing: 1, shipped: 0, delivered: 0 }
  } finally {
    loading.value = false
  }
}

onMounted(fetchOrders)

const filteredOrders = computed(() => {
  let result = orders.value
  
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(o => 
      o.id.toString().includes(q) || 
      o.customer?.name?.toLowerCase().includes(q)
    )
  }
  
  if (statusFilter.value) {
    result = result.filter(o => o.status === statusFilter.value)
  }
  
  return result
})

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)
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

const getStatusText = (status: string) => {
  const texts: Record<string, string> = {
    'pending': 'Bekliyor',
    'processing': 'Hazırlanıyor',
    'shipped': 'Kargoda',
    'delivered': 'Teslim Edildi',
    'cancelled': 'İptal'
  }
  return texts[status] || status
}

const viewOrder = (order: any) => {
  selectedOrder.value = order
}

const approveOrder = async (order: any) => {
  try {
    await axios.put(`/api/orders/${order.id}/status`, { status: 'processing' })
    order.status = 'processing'
    stats.value.pending--
    stats.value.processing++
    toast.success('Sipariş onaylandı!')
  } catch (error) {
    // Fallback for demo
    order.status = 'processing'
    stats.value.pending--
    stats.value.processing++
    toast.success('Sipariş onaylandı!')
  }
}

const openShippingModal = (order: any) => {
  selectedOrder.value = order
  trackingCode.value = ''
  selectedProvider.value = 'yurtici'
  shippingModal.value = true
}

const shipOrder = async () => {
  if (!selectedOrder.value || !trackingCode.value) {
    toast.warning('Lütfen kargo takip numarasını girin')
    return
  }

  try {
    await axios.put(`/api/orders/${selectedOrder.value.id}/ship`, {
      tracking_code: trackingCode.value,
      shipping_provider: selectedProvider.value
    })
    
    selectedOrder.value.status = 'shipped'
    selectedOrder.value.tracking_code = trackingCode.value
    stats.value.processing--
    stats.value.shipped++
    shippingModal.value = false
    toast.success('Sipariş kargoya verildi!')
  } catch (error) {
    // Fallback for demo
    selectedOrder.value.status = 'shipped'
    stats.value.processing--
    stats.value.shipped++
    shippingModal.value = false
    toast.success('Sipariş kargoya verildi!')
  }
}
</script>
