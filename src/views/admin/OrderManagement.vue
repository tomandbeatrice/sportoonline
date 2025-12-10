<template>
  <div class="order-management h-[calc(100vh-100px)] flex flex-col">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6 shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
          <span>📦</span> Sipariş Yönetimi
        </h1>
        <p class="text-slate-500">Yapay zeka destekli sipariş işleme ve risk analizi</p>
      </div>
      <div class="flex gap-3">
        <button class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition">
          🔄 Yenile
        </button>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
          📊 Rapor Al
        </button>
      </div>
    </div>

    <div class="flex gap-6 flex-1 min-h-0">
      <!-- Main Content (List) -->
      <div 
        class="flex flex-col transition-all duration-300 ease-in-out"
        :class="selectedOrder ? 'w-3/5' : 'w-full'"
      >
        <!-- Stats -->
        <div class="grid grid-cols-4 gap-4 mb-4 shrink-0">
          <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-bold">Bekleyen</div>
            <div class="text-2xl font-bold text-orange-600 mt-1">12</div>
          </div>
          <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-bold">İşlenen</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">45</div>
          </div>
          <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-bold">Kargoda</div>
            <div class="text-2xl font-bold text-indigo-600 mt-1">89</div>
          </div>
          <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-xs text-slate-500 uppercase font-bold">İade Talebi</div>
            <div class="text-2xl font-bold text-red-600 mt-1">3</div>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-4 grid grid-cols-4 gap-4 shrink-0">
          <div class="col-span-2">
            <input 
              type="text" 
              placeholder="Sipariş no, müşteri adı..."
              class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
            />
          </div>
          <div class="col-span-2 flex gap-2">
            <select class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
              <option value="">Durum: Tümü</option>
              <option value="pending">Bekliyor</option>
              <option value="processing">İşleniyor</option>
            </select>
          </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex-1 flex flex-col">
          <div class="overflow-y-auto flex-1">
            <table class="w-full text-left text-sm">
              <thead class="bg-slate-50 border-b border-slate-200 sticky top-0 z-10">
                <tr>
                  <th class="px-6 py-4 font-semibold text-slate-700">Sipariş No</th>
                  <th class="px-6 py-4 font-semibold text-slate-700">Müşteri</th>
                  <th class="px-6 py-4 font-semibold text-slate-700">Tutar</th>
                  <th class="px-6 py-4 font-semibold text-slate-700">Durum</th>
                  <th class="px-6 py-4 font-semibold text-slate-700">Risk</th>
                  <th class="px-6 py-4 font-semibold text-slate-700 text-right">Tarih</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr 
                  v-for="order in orders" 
                  :key="order.id" 
                  class="hover:bg-slate-50 transition cursor-pointer"
                  :class="{ 'bg-indigo-50 hover:bg-indigo-50': selectedOrder?.id === order.id }"
                  @click="selectOrder(order)"
                >
                  <td class="px-6 py-4 font-medium text-indigo-600">#{{ order.orderNumber }}</td>
                  <td class="px-6 py-4">
                    <div class="font-medium text-slate-900">{{ order.customerName }}</div>
                    <div class="text-xs text-slate-500">{{ order.city }}</div>
                  </td>
                  <td class="px-6 py-4 font-medium text-slate-900">{{ formatCurrency(order.totalAmount) }}</td>
                  <td class="px-6 py-4">
                    <span 
                      class="px-2 py-1 rounded-full text-xs font-medium"
                      :class="{
                        'bg-orange-100 text-orange-700': order.status === 'pending',
                        'bg-blue-100 text-blue-700': order.status === 'processing',
                        'bg-emerald-100 text-emerald-700': order.status === 'shipped'
                      }"
                    >
                      {{ getStatusLabel(order.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-1">
                      <div class="w-2 h-2 rounded-full" :class="order.riskScore > 50 ? 'bg-red-500' : 'bg-emerald-500'"></div>
                      <span class="text-xs text-slate-500">%{{ order.riskScore }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-right text-slate-500">{{ order.date }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Side Panel (AI Tools) -->
      <div 
        v-if="selectedOrder" 
        class="w-2/5 flex flex-col gap-4 overflow-y-auto pb-4 animate-slide-in"
      >
        <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-slate-200 shadow-sm sticky top-0 z-20">
          <div>
            <h2 class="font-bold text-slate-900">Sipariş Detayı</h2>
            <p class="text-xs text-slate-500">#{{ selectedOrder.orderNumber }}</p>
          </div>
          <button @click="selectedOrder = null" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <!-- Order Items -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
          <h3 class="font-bold text-slate-900 mb-3 text-sm">Ürünler</h3>
          <div class="space-y-3">
            <div v-for="item in selectedOrder.items" :key="item.id" class="flex gap-3">
              <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center text-lg">
                {{ item.image }}
              </div>
              <div>
                <div class="text-sm font-medium text-slate-900">{{ item.name }}</div>
                <div class="text-xs text-slate-500">{{ item.quantity }} adet x {{ formatCurrency(item.price) }}</div>
              </div>
            </div>
          </div>
        </div>

        <OrderRiskAnalysis 
          :order-id="selectedOrder.orderNumber"
          :customer-score="85"
          :total-amount="selectedOrder.totalAmount"
          ip-address="192.168.1.1"
        />

        <SmartLogistics 
          :destination-city="selectedOrder.city"
          :weight="2.5"
        />

        <!-- Actions -->
        <div class="grid grid-cols-2 gap-3">
          <button class="py-3 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-200">
            Onayla & Hazırla
          </button>
          <button class="py-3 bg-white border border-red-200 text-red-600 rounded-xl font-bold hover:bg-red-50 transition">
            İptal Et
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import OrderRiskAnalysis from '@/components/admin/order/OrderRiskAnalysis.vue'
import SmartLogistics from '@/components/admin/order/SmartLogistics.vue'

interface OrderItem {
  id: number
  name: string
  price: number
  quantity: number
  image: string
}

interface Order {
  id: number
  orderNumber: string
  customerName: string
  city: string
  totalAmount: number
  status: string
  date: string
  riskScore: number
  items: OrderItem[]
}

const selectedOrder = ref<Order | null>(null)

const orders = ref<Order[]>([
  { 
    id: 1, 
    orderNumber: 'ORD-2024-001', 
    customerName: 'Ahmet Yılmaz', 
    city: 'İstanbul', 
    totalAmount: 1250.00, 
    status: 'pending', 
    date: '10 dk önce',
    riskScore: 15,
    items: [
      { id: 101, name: 'Nike Air Zoom', price: 1250.00, quantity: 1, image: '👟' }
    ]
  },
  { 
    id: 2, 
    orderNumber: 'ORD-2024-002', 
    customerName: 'Ayşe Demir', 
    city: 'Ankara', 
    totalAmount: 450.50, 
    status: 'processing', 
    date: '1 saat önce',
    riskScore: 85,
    items: [
      { id: 102, name: 'Yoga Matı', price: 450.50, quantity: 1, image: '🧘‍♀️' }
    ]
  },
  { 
    id: 3, 
    orderNumber: 'ORD-2024-003', 
    customerName: 'Mehmet Kaya', 
    city: 'İzmir', 
    totalAmount: 3200.00, 
    status: 'shipped', 
    date: 'Dün',
    riskScore: 5,
    items: [
      { id: 103, name: 'Akıllı Saat', price: 3200.00, quantity: 1, image: '⌚' }
    ]
  }
])

const selectOrder = (order: Order) => {
  selectedOrder.value = selectedOrder.value?.id === order.id ? null : order
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)
}

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    pending: 'Bekliyor',
    processing: 'İşleniyor',
    shipped: 'Kargoda',
    delivered: 'Teslim Edildi',
    cancelled: 'İptal'
  }
  return labels[status] || status
}
</script>

<style scoped>
.animate-slide-in {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateX(20px); }
  to { opacity: 1; transform: translateX(0); }
}
</style>
