<template>
  <div class="p-6">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Yemek Siparişleri</h1>
        <p class="text-sm text-slate-500 mt-1">Restoran siparişlerini yönetin</p>
      </div>
      <div class="flex gap-2">
        <select class="px-4 py-2 border border-slate-200 rounded-lg text-sm">
          <option>Tüm Durumlar</option>
          <option>Beklemede</option>
          <option>Hazırlanıyor</option>
          <option>Yolda</option>
          <option>Teslim Edildi</option>
        </select>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Sipariş No</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Restoran</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Müşteri</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Ürünler</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tutar</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="order in orders" :key="order.id" class="hover:bg-slate-50">
            <td class="px-6 py-4">
              <div class="font-semibold text-slate-900">#{{ order.order_no }}</div>
              <div class="text-xs text-slate-500">{{ order.created_at }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="font-medium text-slate-900">{{ order.restaurant }}</div>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ order.customer }}</td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-600">{{ order.items.join(', ') }}</div>
              <div class="text-xs text-slate-400">{{ order.item_count }} ürün</div>
            </td>
            <td class="px-6 py-4 font-semibold text-slate-900">{{ formatCurrency(order.total) }}</td>
            <td class="px-6 py-4">
              <span 
                class="px-2 py-1 text-xs font-semibold rounded-full"
                :class="getStatusClass(order.status)"
              >
                {{ getStatusText(order.status) }}
              </span>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Detay</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const orders = ref([
  {
    id: 1,
    order_no: 'YMK2024001',
    restaurant: 'Pizza House',
    customer: 'Ahmet Yılmaz',
    items: ['Margarita Pizza', 'Kola'],
    item_count: 2,
    total: 185.50,
    status: 'preparing',
    created_at: '10.12.2024 14:30'
  },
  {
    id: 2,
    order_no: 'YMK2024002',
    restaurant: 'Burger King',
    customer: 'Ayşe Demir',
    items: ['Whopper Menü', 'Patates'],
    item_count: 2,
    total: 220.00,
    status: 'on_way',
    created_at: '10.12.2024 14:25'
  },
  {
    id: 3,
    order_no: 'YMK2024003',
    restaurant: 'Sushi Express',
    customer: 'Mehmet Kaya',
    items: ['Maki Set', 'Miso Çorbası'],
    item_count: 2,
    total: 350.00,
    status: 'delivered',
    created_at: '10.12.2024 13:45'
  },
])

const formatCurrency = (amount: number) => `₺${amount.toFixed(2)}`

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    'pending': 'bg-yellow-100 text-yellow-700',
    'preparing': 'bg-blue-100 text-blue-700',
    'on_way': 'bg-purple-100 text-purple-700',
    'delivered': 'bg-green-100 text-green-700',
    'cancelled': 'bg-red-100 text-red-700'
  }
  return classes[status] || 'bg-slate-100 text-slate-600'
}

const getStatusText = (status: string) => {
  const texts: Record<string, string> = {
    'pending': 'Beklemede',
    'preparing': 'Hazırlanıyor',
    'on_way': 'Yolda',
    'delivered': 'Teslim Edildi',
    'cancelled': 'İptal'
  }
  return texts[status] || status
}
</script>
