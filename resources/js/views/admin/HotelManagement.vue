<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Otel Yönetimi</h1>
        <p class="text-gray-600">Tüm otelleri ve odaları yönetin</p>
      </div>
      <button
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center"
      >
        <span class="mr-2">+</span>
        Yeni Otel
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Toplam Otel</div>
        <div class="text-2xl font-bold text-gray-900">{{ stats.total_hotels || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Aktif Oteller</div>
        <div class="text-2xl font-bold text-green-600">{{ stats.active_hotels || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Toplam Oda</div>
        <div class="text-2xl font-bold text-blue-600">{{ stats.total_rooms || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Aktif Rezervasyon</div>
        <div class="text-2xl font-bold text-purple-600">{{ stats.active_reservations || 0 }}</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Otel ara..."
          class="border rounded-lg px-4 py-2"
        />
        <select v-model="filters.status" class="border rounded-lg px-4 py-2">
          <option value="">Tüm Durumlar</option>
          <option value="active">Aktif</option>
          <option value="pending">Beklemede</option>
          <option value="inactive">Pasif</option>
        </select>
        <select v-model="filters.city" class="border rounded-lg px-4 py-2">
          <option value="">Tüm Şehirler</option>
          <option value="Istanbul">İstanbul</option>
          <option value="Antalya">Antalya</option>
          <option value="Bodrum">Bodrum</option>
          <option value="Cappadocia">Kapadokya</option>
        </select>
        <select v-model="filters.stars" class="border rounded-lg px-4 py-2">
          <option value="">Tüm Yıldızlar</option>
          <option value="5">5 Yıldız</option>
          <option value="4">4 Yıldız</option>
          <option value="3">3 Yıldız</option>
        </select>
        <button @click="fetchHotels" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">
          🔍 Filtrele
        </button>
      </div>
    </div>

    <!-- Hotels Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="hotel in hotels" :key="hotel.id" class="bg-white rounded-lg shadow overflow-hidden">
        <img
          :src="hotel.images?.[0] || '/images/default-hotel.jpg'"
          :alt="hotel.name"
          class="w-full h-48 object-cover"
        />
        <div class="p-4">
          <div class="flex justify-between items-start">
            <h3 class="font-bold text-lg text-gray-900">{{ hotel.name }}</h3>
            <span
              :class="getStatusClass(hotel.status)"
              class="px-2 py-1 text-xs rounded-full"
            >
              {{ getStatusLabel(hotel.status) }}
            </span>
          </div>
          <p class="text-sm text-gray-500 mt-1">📍 {{ hotel.city }}</p>
          <div class="flex items-center mt-2">
            <span v-for="n in hotel.stars" :key="n" class="text-yellow-400">★</span>
            <span v-for="n in (5 - hotel.stars)" :key="n + hotel.stars" class="text-gray-300">★</span>
            <span class="ml-2 text-sm text-gray-600">({{ hotel.rating?.toFixed(1) || '-' }})</span>
          </div>
          <div class="flex justify-between items-center mt-4 pt-4 border-t">
            <div>
              <span class="text-sm text-gray-500">{{ hotel.rooms_count || 0 }} Oda</span>
            </div>
            <div class="flex space-x-2">
              <button @click="viewRooms(hotel)" class="text-blue-600 hover:text-blue-800 text-sm">
                Odalar
              </button>
              <button @click="editHotel(hotel)" class="text-green-600 hover:text-green-800 text-sm">
                Düzenle
              </button>
              <button @click="deleteHotel(hotel)" class="text-red-600 hover:text-red-800 text-sm">
                Sil
              </button>
            </div>
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

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold">{{ isEditing ? 'Otel Düzenle' : 'Yeni Otel' }}</h2>
        </div>
        <form @submit.prevent="saveHotel" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Otel Adı</label>
              <input v-model="form.name" type="text" required class="w-full border rounded-lg px-4 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
              <input v-model="form.slug" type="text" class="w-full border rounded-lg px-4 py-2" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
            <textarea v-model="form.description" rows="3" class="w-full border rounded-lg px-4 py-2"></textarea>
          </div>
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
              <input v-model="form.phone" type="text" required class="w-full border rounded-lg px-4 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
              <input v-model="form.email" type="email" required class="w-full border rounded-lg px-4 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Yıldız</label>
              <select v-model="form.stars" class="w-full border rounded-lg px-4 py-2">
                <option value="5">5 Yıldız</option>
                <option value="4">4 Yıldız</option>
                <option value="3">3 Yıldız</option>
                <option value="2">2 Yıldız</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
            <input v-model="form.address" type="text" class="w-full border rounded-lg px-4 py-2" />
          </div>
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Şehir</label>
              <input v-model="form.city" type="text" class="w-full border rounded-lg px-4 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
              <input v-model="form.check_in_time" type="time" class="w-full border rounded-lg px-4 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
              <input v-model="form.check_out_time" type="time" class="w-full border rounded-lg px-4 py-2" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
              <select v-model="form.status" class="w-full border rounded-lg px-4 py-2">
                <option value="active">Aktif</option>
                <option value="pending">Beklemede</option>
                <option value="inactive">Pasif</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Özellikler</label>
            <div class="grid grid-cols-4 gap-2">
              <label v-for="amenity in availableAmenities" :key="amenity" class="flex items-center">
                <input
                  type="checkbox"
                  :value="amenity"
                  v-model="form.amenities"
                  class="mr-2"
                />
                {{ amenity }}
              </label>
            </div>
          </div>
          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
              İptal
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              {{ isEditing ? 'Güncelle' : 'Oluştur' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const hotels = ref([])
const stats = ref({})
const showModal = ref(false)
const isEditing = ref(false)
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0
})

const filters = reactive({
  search: '',
  status: '',
  city: '',
  stars: ''
})

const availableAmenities = [
  'WiFi', 'Havuz', 'Spa', 'Fitness', 'Restaurant', 'Bar', 'Otopark', 'Klima'
]

const form = reactive({
  id: null,
  name: '',
  slug: '',
  description: '',
  phone: '',
  email: '',
  address: '',
  city: '',
  stars: 4,
  check_in_time: '14:00',
  check_out_time: '12:00',
  status: 'active',
  amenities: []
})

const fetchHotels = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.status) params.append('status', filters.status)
    if (filters.city) params.append('city', filters.city)
    if (filters.stars) params.append('stars', filters.stars)
    params.append('page', pagination.value.current_page)

    const response = await axios.get(`/api/admin/hotels?${params}`)
    hotels.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total
    }
  } catch (error) {
    console.error('Error fetching hotels:', error)
  }
}

const fetchStats = async () => {
  try {
    const response = await axios.get('/api/admin/hotels/stats')
    stats.value = response.data
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

const openCreateModal = () => {
  isEditing.value = false
  Object.assign(form, {
    id: null,
    name: '',
    slug: '',
    description: '',
    phone: '',
    email: '',
    address: '',
    city: '',
    stars: 4,
    check_in_time: '14:00',
    check_out_time: '12:00',
    status: 'active',
    amenities: []
  })
  showModal.value = true
}

const editHotel = (hotel) => {
  isEditing.value = true
  Object.assign(form, {
    ...hotel,
    amenities: hotel.amenities || []
  })
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveHotel = async () => {
  try {
    if (isEditing.value) {
      await axios.put(`/api/admin/hotels/${form.id}`, form)
    } else {
      await axios.post('/api/admin/hotels', form)
    }
    closeModal()
    fetchHotels()
    fetchStats()
  } catch (error) {
    console.error('Error saving hotel:', error)
    alert('Kayıt sırasında bir hata oluştu')
  }
}

const deleteHotel = async (hotel) => {
  if (!confirm(`${hotel.name} silinecek. Emin misiniz?`)) return
  
  try {
    await axios.delete(`/api/admin/hotels/${hotel.id}`)
    fetchHotels()
    fetchStats()
  } catch (error) {
    console.error('Error deleting hotel:', error)
    alert('Silme sırasında bir hata oluştu')
  }
}

const viewRooms = (hotel) => {
  // Navigate to rooms page
  console.log('View rooms for hotel:', hotel.id)
}

const changePage = (page) => {
  pagination.value.current_page = page
  fetchHotels()
}

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800',
    inactive: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    active: 'Aktif',
    pending: 'Beklemede',
    inactive: 'Pasif'
  }
  return labels[status] || status
}

onMounted(() => {
  fetchHotels()
  fetchStats()
})
</script>
