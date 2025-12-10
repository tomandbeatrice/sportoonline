<template>
  <div class="p-6">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Rezervasyonlar</h1>
        <p class="text-sm text-slate-500 mt-1">Otel rezervasyonlarını yönetin</p>
      </div>
      <div class="flex gap-2">
        <select class="px-4 py-2 border border-slate-200 rounded-lg text-sm">
          <option>Tüm Rezervasyonlar</option>
          <option>Onay Bekleyen</option>
          <option>Onaylı</option>
          <option>İptal</option>
        </select>
        <input 
          type="date" 
          class="px-4 py-2 border border-slate-200 rounded-lg text-sm"
        />
      </div>
    </div>

    <!-- Reservations Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Rezervasyon No</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Otel</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Müşteri</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tarih</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Misafir</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tutar</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Durum</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">İşlemler</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="reservation in reservations" :key="reservation.id" class="hover:bg-slate-50">
            <td class="px-6 py-4">
              <div class="font-semibold text-slate-900">#{{ reservation.reservation_no }}</div>
              <div class="text-xs text-slate-500">{{ reservation.created_at }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="font-medium text-slate-900">{{ reservation.hotel }}</div>
              <div class="text-xs text-slate-500">{{ reservation.room_type }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-900">{{ reservation.customer }}</div>
              <div class="text-xs text-slate-500">{{ reservation.phone }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-slate-600">{{ reservation.check_in }}</div>
              <div class="text-xs text-slate-500">{{ reservation.nights }} gece</div>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ reservation.guests }} kişi</td>
            <td class="px-6 py-4 font-semibold text-slate-900">{{ formatCurrency(reservation.total) }}</td>
            <td class="px-6 py-4">
              <span 
                class="px-2 py-1 text-xs font-semibold rounded-full"
                :class="getStatusClass(reservation.status)"
              >
                {{ getStatusText(reservation.status) }}
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

const reservations = ref([
  {
    id: 1,
    reservation_no: 'RZV2024001',
    hotel: 'Grand Hotel Istanbul',
    room_type: 'Deluxe Oda',
    customer: 'Ali Veli',
    phone: '0532 123 4567',
    check_in: '15.12.2024',
    nights: 3,
    guests: 2,
    total: 4500.00,
    status: 'confirmed',
    created_at: '08.12.2024'
  },
  {
    id: 2,
    reservation_no: 'RZV2024002',
    hotel: 'Seaside Resort',
    room_type: 'Suit',
    customer: 'Zeynep Öz',
    phone: '0543 987 6543',
    check_in: '20.12.2024',
    nights: 5,
    guests: 4,
    total: 8750.00,
    status: 'pending',
    created_at: '09.12.2024'
  },
  {
    id: 3,
    reservation_no: 'RZV2024003',
    hotel: 'Mountain Lodge',
    room_type: 'Standart Oda',
    customer: 'Can Kara',
    phone: '0505 456 7890',
    check_in: '18.12.2024',
    nights: 2,
    guests: 2,
    total: 2200.00,
    status: 'confirmed',
    created_at: '10.12.2024'
  },
])

const formatCurrency = (amount: number) => `₺${amount.toFixed(2)}`

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    'pending': 'bg-yellow-100 text-yellow-700',
    'confirmed': 'bg-green-100 text-green-700',
    'cancelled': 'bg-red-100 text-red-700',
    'completed': 'bg-blue-100 text-blue-700'
  }
  return classes[status] || 'bg-slate-100 text-slate-600'
}

const getStatusText = (status: string) => {
  const texts: Record<string, string> = {
    'pending': 'Onay Bekliyor',
    'confirmed': 'Onaylandı',
    'cancelled': 'İptal',
    'completed': 'Tamamlandı'
  }
  return texts[status] || status
}
</script>
