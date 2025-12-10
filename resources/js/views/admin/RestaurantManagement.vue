<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Restoran Yönetimi</h1>
        <p class="text-gray-600">Tüm restoranları yönetin</p>
      </div>
      <button
        @click="openCreateModal"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center"
      >
        <span class="mr-2">+</span>
        Yeni Restoran
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Toplam Restoran</div>
        <div class="text-2xl font-bold text-gray-900">{{ stats.total_restaurants || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Aktif</div>
        <div class="text-2xl font-bold text-green-600">{{ stats.active_restaurants || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Bugünkü Siparişler</div>
        <div class="text-2xl font-bold text-blue-600">{{ stats.todays_orders || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-500">Bugünkü Gelir</div>
        <div class="text-2xl font-bold text-purple-600">₺{{ stats.revenue_today?.toLocaleString() || 0 }}</div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Restoran ara..."
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
          <option value="Ankara">Ankara</option>
          <option value="Izmir">İzmir</option>
        </select>
        <button @click="fetchRestaurants" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">
          🔍 Filtrele
        </button>
      </div>
    </div>

    <!-- Restaurants Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Restoran</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şehir</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Puan</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siparişler</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="restaurant in restaurants" :key="restaurant.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center">
                <img
                  :src="restaurant.logo || '/images/default-restaurant.png'"
                  :alt="restaurant.name"
                  class="w-10 h-10 rounded-full object-cover"
                />
                <div class="ml-3">
                  <div class="font-medium text-gray-900">{{ restaurant.name }}</div>
                  <div class="text-sm text-gray-500">{{ restaurant.phone }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ restaurant.city }}</td>
            <td class="px-6 py-4">
              <span
                :class="getStatusClass(restaurant.status)"
                class="px-2 py-1 text-xs rounded-full"
              >
                {{ getStatusLabel(restaurant.status) }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm">
              <span class="text-yellow-500">★</span> {{ restaurant.rating?.toFixed(1) || '-' }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ restaurant.orders_count || 0 }}</td>
            <td class="px-6 py-4 text-right text-sm space-x-2">
              <button @click="viewRestaurant(restaurant)" class="text-blue-600 hover:text-blue-900">
                Görüntüle
              </button>
              <button @click="editRestaurant(restaurant)" class="text-green-600 hover:text-green-900">
                Düzenle
              </button>
              <button @click="deleteRestaurant(restaurant)" class="text-red-600 hover:text-red-900">
                Sil
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="bg-gray-50 px-6 py-3 flex justify-between items-center">
        <span class="text-sm text-gray-500">
          Toplam {{ pagination.total }} restoran
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

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold">{{ isEditing ? 'Restoran Düzenle' : 'Yeni Restoran' }}</h2>
        </div>
        <form @submit.prevent="saveRestaurant" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Restoran Adı</label>
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
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
              <input v-model="form.phone" type="text" class="w-full border rounded-lg px-4 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
              <input v-model="form.email" type="email" class="w-full border rounded-lg px-4 py-2" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
            <input v-model="form.address" type="text" class="w-full border rounded-lg px-4 py-2" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Şehir</label>
              <input v-model="form.city" type="text" class="w-full border rounded-lg px-4 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
              <select v-model="form.status" class="w-full border rounded-lg px-4 py-2">
                <option value="active">Aktif</option>
                <option value="pending">Beklemede</option>
                <option value="inactive">Pasif</option>
              </select>
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

const restaurants = ref([])
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
  city: ''
})

const form = reactive({
  id: null,
  name: '',
  slug: '',
  description: '',
  phone: '',
  email: '',
  address: '',
  city: '',
  status: 'active'
})

const fetchRestaurants = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.search) params.append('search', filters.search)
    if (filters.status) params.append('status', filters.status)
    if (filters.city) params.append('city', filters.city)
    params.append('page', pagination.value.current_page)

    const response = await axios.get(`/api/admin/restaurants?${params}`)
    restaurants.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total
    }
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
    status: 'active'
  })
  showModal.value = true
}

const editRestaurant = (restaurant) => {
  isEditing.value = true
  Object.assign(form, restaurant)
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveRestaurant = async () => {
  try {
    if (isEditing.value) {
      await axios.put(`/api/admin/restaurants/${form.id}`, form)
    } else {
      await axios.post('/api/admin/restaurants', form)
    }
    closeModal()
    fetchRestaurants()
    fetchStats()
  } catch (error) {
    console.error('Error saving restaurant:', error)
    alert('Kayıt sırasında bir hata oluştu')
  }
}

const deleteRestaurant = async (restaurant) => {
  if (!confirm(`${restaurant.name} silinecek. Emin misiniz?`)) return
  
  try {
    await axios.delete(`/api/admin/restaurants/${restaurant.id}`)
    fetchRestaurants()
    fetchStats()
  } catch (error) {
    console.error('Error deleting restaurant:', error)
    alert('Silme sırasında bir hata oluştu')
  }
}

const viewRestaurant = (restaurant) => {
  // Navigate to detail page or open detail modal
  console.log('View restaurant:', restaurant)
}

const changePage = (page) => {
  pagination.value.current_page = page
  fetchRestaurants()
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
  fetchRestaurants()
  fetchStats()
})
</script>
