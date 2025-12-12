<template>
  <div class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-5xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Siparişlerim</h1>
        <p class="text-slate-500">Tüm siparişlerinizi buradan takip edebilirsiniz</p>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
          <div class="flex-1 min-w-[200px]">
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Sipariş no veya ürün ara..." 
                class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-sm"
              >
            </div>
          </div>
          <select 
            v-model="statusFilter"
            class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
          >
            <option value="">Tüm Durumlar</option>
            <option value="pending">Beklemede</option>
            <option value="processing">Hazırlanıyor</option>
            <option value="shipped">Kargoda</option>
            <option value="delivered">Teslim Edildi</option>
            <option value="cancelled">İptal</option>
          </select>
          <select 
            v-model="dateFilter"
            class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
          >
            <option value="">Tüm Tarihler</option>
            <option value="week">Son 7 Gün</option>
            <option value="month">Son 30 Gün</option>
            <option value="3months">Son 3 Ay</option>
          </select>
        </div>
      </div>

      <!-- Orders -->
      <div class="space-y-4">
        <div 
          v-for="order in filteredOrders" 
          :key="order.id"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-lg transition"
        >
          <!-- Order Header -->
          <div class="flex items-center justify-between p-5 border-b border-slate-100">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <span class="text-2xl">📦</span>
              </div>
              <div>
                <div class="flex items-center gap-2">
                  <h3 class="font-bold text-slate-900">Sipariş #{{ order.id }}</h3>
                  <span 
                    class="px-2 py-0.5 rounded-full text-xs font-medium"
                    :class="getStatusClass(order.status)"
                  >
                    {{ getStatusLabel(order.status) }}
                  </span>
                </div>
                <p class="text-sm text-slate-500">{{ order.date }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="font-bold text-lg text-slate-900">{{ formatPrice(order.total) }}</p>
              <p class="text-xs text-slate-500">{{ order.itemCount }} ürün</p>
            </div>
          </div>

          <!-- Order Items Preview -->
          <div class="p-5">
            <div class="flex gap-3 overflow-x-auto pb-2">
              <div 
                v-for="item in order.items.slice(0, 4)" 
                :key="item.id"
                class="flex-shrink-0 w-16 h-16 bg-slate-100 rounded-lg overflow-hidden"
              >
                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
              </div>
              <div 
                v-if="order.items.length > 4"
                class="flex-shrink-0 w-16 h-16 bg-slate-100 rounded-lg flex items-center justify-center text-sm font-medium text-slate-600"
              >
                +{{ order.items.length - 4 }}
              </div>
            </div>
          </div>

          <!-- Order Actions -->
          <div class="flex items-center justify-between px-5 py-3 bg-slate-50 border-t border-slate-100">
            <div class="flex items-center gap-4 text-sm text-slate-600">
              <span v-if="order.trackingCode">📍 Kargo: {{ order.trackingCode }}</span>
              <span v-if="order.estimatedDelivery">🚚 Tahmini: {{ order.estimatedDelivery }}</span>
            </div>
            <div class="flex gap-2">
              <router-link 
                :to="`/orders/${order.id}/track`"
                v-if="order.status === 'shipped'"
                class="px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg"
              >
                📍 Takip Et
              </router-link>
              <router-link 
                :to="`/orders/${order.id}`"
                class="px-4 py-2 text-sm font-medium bg-green-600 text-white rounded-lg hover:bg-green-700"
              >
                Detayları Gör
              </router-link>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredOrders.length === 0" class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
          <div class="text-5xl mb-4">📦</div>
          <h3 class="font-bold text-slate-900 mb-2">Sipariş Bulunamadı</h3>
          <p class="text-slate-500 mb-4">Filtrelere uygun sipariş bulunamadı veya henüz siparişiniz yok.</p>
          <router-link to="/products" class="inline-flex px-6 py-2.5 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700">
            Alışverişe Başla
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const searchQuery = ref('')
const statusFilter = ref('')
const dateFilter = ref('')

const orders = ref([
  { 
    id: 1001, 
    date: '5 Aralık 2025, 14:30', 
    status: 'delivered', 
    total: 2450, 
    itemCount: 3,
    trackingCode: 'TR123456789',
    estimatedDelivery: null,
    items: [
      { id: 1, name: 'Spor Ayakkabı', image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=100' },
      { id: 2, name: 'Eşofman', image: 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=100' },
      { id: 3, name: 'Spor Çorap', image: 'https://images.unsplash.com/photo-1586350977771-b3b0abd50c82?w=100' },
    ]
  },
  { 
    id: 1002, 
    date: '3 Aralık 2025, 09:15', 
    status: 'shipped', 
    total: 1890, 
    itemCount: 2,
    trackingCode: 'TR987654321',
    estimatedDelivery: '7 Aralık 2025',
    items: [
      { id: 4, name: 'Yoga Mat', image: 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=100' },
      { id: 5, name: 'Dumbbell Set', image: 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=100' },
    ]
  },
  { 
    id: 1003, 
    date: '1 Aralık 2025, 16:45', 
    status: 'processing', 
    total: 3200, 
    itemCount: 4,
    trackingCode: null,
    estimatedDelivery: '8 Aralık 2025',
    items: [
      { id: 6, name: 'Fitness Bandı', image: 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=100' },
      { id: 7, name: 'Protein', image: 'https://images.unsplash.com/photo-1593095948071-474c5cc2989d?w=100' },
      { id: 8, name: 'Shaker', image: 'https://images.unsplash.com/photo-1594381898411-846e7d193883?w=100' },
      { id: 9, name: 'Spor Havlu', image: 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=100' },
    ]
  },
])

const filteredOrders = computed(() => {
  let result = [...orders.value]

  // safe defaults
  const status = statusFilter.value || ''
  const query = (searchQuery.value || '').toLowerCase()

  if (status) {
    result = result.filter(o => o.status === status)
  }

  if (query) {
    result = result.filter(o =>
      o.id.toString().includes(query) ||
      o.items.some(item => item.name.toLowerCase().includes(query))
    )
  }

  return result
})

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