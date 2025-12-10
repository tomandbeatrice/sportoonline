<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Rezervasyon Yönetimi</h1>
        <p class="text-gray-600">Tüm otel rezervasyonlarını yönetin</p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Toplam</div>
        <div class="text-2xl font-bold text-gray-900">{{ stats.total || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Beklemede</div>
        <div class="text-2xl font-bold text-yellow-600">{{ stats.pending || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Onaylandı</div>
        <div class="text-2xl font-bold text-blue-600">{{ stats.confirmed || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Check-in</div>
        <div class="text-2xl font-bold text-green-600">{{ stats.checked_in || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Bugünkü Gelir</div>
        <div class="text-2xl font-bold text-purple-600">₺{{ stats.today_revenue?.toLocaleString() || 0 }}</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Müşteri veya otel ara..."
          class="border rounded-lg px-4 py-2"
        />
        <select v-model="filters.status" class="border rounded-lg px-4 py-2">
          <option value="">Tüm Durumlar</option>
          <option value="pending">Beklemede</option>
          <option value="confirmed">Onaylandı</option>
          <option value="checked_in">Check-in</option>
          <option value="checked_out">Check-out</option>
          <option value="cancelled">İptal</option>
        </select>
        <input
          v-model="filters.date_from"
          type="date"
          class="border rounded-lg px-4 py-2"
          placeholder="Başlangıç"
        />
        <input
          v-model="filters.date_to"
          type="date"
          class="border rounded-lg px-4 py-2"
          placeholder="Bitiş"
        />
        <button @click="fetchReservations" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">
          🔍 Filtrele
        </button>
      </div>
    </div>

    <!-- Reservations Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Otel / Oda</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarihler</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Misafir</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="reservation in reservations" :key="reservation.id" class="hover:bg-gray-50">
            <td class="px-4 py-3 text-sm">#{{ reservation.id }}</td>
            <td class="px-4 py-3">
              <div class="font-medium">{{ reservation.hotel?.name }}</div>
              <div class="text-sm text-gray-500">{{ reservation.room?.name }}</div>
            </td>
            <td class="px-4 py-3">
              <div class="text-sm">{{ reservation.user?.name }}</div>
              <div class="text-xs text-gray-500">{{ reservation.user?.email }}</div>
            </td>
            <td class="px-4 py-3 text-sm">
              <div>{{ formatDate(reservation.check_in) }}</div>
              <div class="text-gray-500">{{ formatDate(reservation.check_out) }}</div>
            </td>
            <td class="px-4 py-3 text-sm">
              {{ reservation.guests }} kişi
            </td>
            <td class="px-4 py-3">
              <select
                v-model="reservation.status"
                @change="updateStatus(reservation)"
                :class="getStatusClass(reservation.status)"
                class="px-2 py-1 text-xs rounded border-0"
              >
                <option value="pending">Beklemede</option>
                <option value="confirmed">Onaylandı</option>
                <option value="checked_in">Check-in</option>
                <option value="checked_out">Check-out</option>
                <option value="cancelled">İptal</option>
                <option value="no_show">Gelmedi</option>
              </select>
            </td>
            <td class="px-4 py-3 text-sm text-right font-medium">
              ₺{{ reservation.total_price?.toLocaleString() }}
            </td>
            <td class="px-4 py-3 text-right space-x-2">
              <button @click="viewDetails(reservation)" class="text-blue-600 text-sm">
                Detay
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="bg-gray-50 px-6 py-3 flex justify-between items-center">
        <span class="text-sm text-gray-500">
          Toplam {{ pagination.total }} rezervasyon
        </span>
        <div class="flex space-x-2">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1 border rounded disabled:opacity-50"
          >
            Önceki
          </button>
          <span class="px-3 py-1">{{ pagination.current_page }} / {{ pagination.last_page }}</span>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1 border rounded disabled:opacity-50"
          >
            Sonraki
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const reservations = ref([])
const stats = ref({})
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0
})

const filters = reactive({
  search: '',
  status: '',
  date_from: '',
  date_to: ''
})

const fetchReservations = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.status) params.append('status', filters.status)
    if (filters.date_from) params.append('date_from', filters.date_from)
    if (filters.date_to) params.append('date_to', filters.date_to)
    params.append('page', pagination.value.current_page)

    const response = await axios.get(`/api/admin/hotels/reservations?${params}`)
    reservations.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total
    }
  } catch (error) {
    console.error('Error fetching reservations:', error)
  }
}

const fetchStats = async () => {
  try {
    const response = await axios.get('/api/admin/hotels/reservations/stats')
    stats.value = response.data
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

const updateStatus = async (reservation) => {
  try {
    await axios.put(`/api/admin/hotels/reservations/${reservation.id}/status`, {
      status: reservation.status
    })
  } catch (error) {
    console.error('Error updating status:', error)
    fetchReservations() // Revert on error
  }
}

const viewDetails = (reservation) => {
  console.log('View reservation:', reservation)
}

const changePage = (page) => {
  pagination.value.current_page = page
  fetchReservations()
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR')
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    checked_in: 'bg-green-100 text-green-800',
    checked_out: 'bg-gray-100 text-gray-800',
    cancelled: 'bg-red-100 text-red-800',
    no_show: 'bg-orange-100 text-orange-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

onMounted(() => {
  fetchReservations()
  fetchStats()
})
</script>
