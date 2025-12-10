<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Transfer Yönetimi</h1>
        <p class="text-gray-600">Araçlar, sürücüler ve transferleri yönetin</p>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow">
      <div class="border-b">
        <nav class="flex -mb-px">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'px-6 py-4 text-sm font-medium border-b-2 transition-colors',
              activeTab === tab.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.icon }} {{ tab.name }}
          </button>
        </nav>
      </div>

      <!-- Vehicles Tab -->
      <div v-if="activeTab === 'vehicles'" class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Araçlar</h3>
          <button @click="openVehicleModal" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            + Yeni Araç
          </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div v-for="vehicle in vehicles" :key="vehicle.id" class="bg-gray-50 rounded-lg p-4">
            <div class="flex justify-between items-start">
              <div>
                <h4 class="font-semibold">{{ vehicle.brand }} {{ vehicle.model }}</h4>
                <p class="text-sm text-gray-500">{{ vehicle.plate_number }}</p>
              </div>
              <span
                :class="getVehicleStatusClass(vehicle.status)"
                class="px-2 py-1 text-xs rounded-full"
              >
                {{ vehicle.status }}
              </span>
            </div>
            <div class="mt-3 flex justify-between items-center">
              <span class="text-sm text-gray-600">{{ vehicle.capacity }} kişilik</span>
              <div class="space-x-2">
                <button @click="editVehicle(vehicle)" class="text-blue-600 text-sm">Düzenle</button>
                <button @click="deleteVehicle(vehicle)" class="text-red-600 text-sm">Sil</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Drivers Tab -->
      <div v-if="activeTab === 'drivers'" class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Sürücüler</h3>
          <button @click="openDriverModal" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            + Yeni Sürücü
          </button>
        </div>
        <table class="min-w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sürücü</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ehliyet</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Puan</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="driver in drivers" :key="driver.id">
              <td class="px-4 py-3">
                <div class="flex items-center">
                  <img
                    :src="driver.photo || '/images/default-avatar.png'"
                    class="w-8 h-8 rounded-full"
                  />
                  <span class="ml-2">{{ driver.name }}</span>
                </div>
              </td>
              <td class="px-4 py-3 text-sm">{{ driver.phone }}</td>
              <td class="px-4 py-3 text-sm">{{ driver.license_type }}</td>
              <td class="px-4 py-3">
                <span
                  :class="getDriverStatusClass(driver.status)"
                  class="px-2 py-1 text-xs rounded-full"
                >
                  {{ getDriverStatusLabel(driver.status) }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm">
                <span class="text-yellow-500">★</span> {{ driver.rating?.toFixed(1) || '-' }}
              </td>
              <td class="px-4 py-3 text-right space-x-2">
                <button @click="editDriver(driver)" class="text-blue-600 text-sm">Düzenle</button>
                <button @click="deleteDriver(driver)" class="text-red-600 text-sm">Sil</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Transfers Tab -->
      <div v-if="activeTab === 'transfers'" class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Transfer Kayıtları</h3>
        </div>
        <table class="min-w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rota</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sürücü</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tutar</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="transfer in transfers" :key="transfer.id">
              <td class="px-4 py-3 text-sm">#{{ transfer.id }}</td>
              <td class="px-4 py-3 text-sm">
                {{ transfer.pickup_location }} → {{ transfer.dropoff_location }}
              </td>
              <td class="px-4 py-3 text-sm">{{ transfer.user?.name || '-' }}</td>
              <td class="px-4 py-3 text-sm">{{ transfer.driver?.name || 'Atanmadı' }}</td>
              <td class="px-4 py-3 text-sm">{{ formatDate(transfer.pickup_date) }}</td>
              <td class="px-4 py-3">
                <span
                  :class="getTransferStatusClass(transfer.status)"
                  class="px-2 py-1 text-xs rounded-full"
                >
                  {{ getTransferStatusLabel(transfer.status) }}
                </span>
              </td>
              <td class="px-4 py-3 text-sm text-right font-medium">
                ₺{{ transfer.total_price?.toLocaleString() }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Routes Tab -->
      <div v-if="activeTab === 'routes'" class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Rotalar</h3>
          <button @click="openRouteModal" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            + Yeni Rota
          </button>
        </div>
        <table class="min-w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rota</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesafe</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Süre</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fiyat</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="route in routes" :key="route.id">
              <td class="px-4 py-3">
                <div class="font-medium">{{ route.origin }} → {{ route.destination }}</div>
              </td>
              <td class="px-4 py-3 text-sm">{{ route.distance }} km</td>
              <td class="px-4 py-3 text-sm">{{ route.estimated_duration }} dk</td>
              <td class="px-4 py-3 text-sm font-medium">₺{{ route.base_price }}</td>
              <td class="px-4 py-3">
                <span
                  :class="route.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                  class="px-2 py-1 text-xs rounded-full"
                >
                  {{ route.is_active ? 'Aktif' : 'Pasif' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right space-x-2">
                <button @click="editRoute(route)" class="text-blue-600 text-sm">Düzenle</button>
                <button @click="deleteRoute(route)" class="text-red-600 text-sm">Sil</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Vehicle Modal -->
    <div v-if="showVehicleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold">{{ vehicleForm.id ? 'Araç Düzenle' : 'Yeni Araç' }}</h2>
        </div>
        <form @submit.prevent="saveVehicle" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Marka</label>
              <input v-model="vehicleForm.brand" type="text" required class="w-full border rounded-lg px-3 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Model</label>
              <input v-model="vehicleForm.model" type="text" required class="w-full border rounded-lg px-3 py-2" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Plaka</label>
              <input v-model="vehicleForm.plate_number" type="text" required class="w-full border rounded-lg px-3 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Kapasite</label>
              <input v-model.number="vehicleForm.capacity" type="number" min="1" class="w-full border rounded-lg px-3 py-2" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Durum</label>
            <select v-model="vehicleForm.status" class="w-full border rounded-lg px-3 py-2">
              <option value="active">Aktif</option>
              <option value="maintenance">Bakımda</option>
              <option value="inactive">Pasif</option>
            </select>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="showVehicleModal = false" class="px-4 py-2 border rounded-lg">
              İptal
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
              Kaydet
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Driver Modal -->
    <div v-if="showDriverModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold">{{ driverForm.id ? 'Sürücü Düzenle' : 'Yeni Sürücü' }}</h2>
        </div>
        <form @submit.prevent="saveDriver" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Ad Soyad</label>
            <input v-model="driverForm.name" type="text" required class="w-full border rounded-lg px-3 py-2" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Telefon</label>
              <input v-model="driverForm.phone" type="text" required class="w-full border rounded-lg px-3 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">E-posta</label>
              <input v-model="driverForm.email" type="email" class="w-full border rounded-lg px-3 py-2" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Ehliyet Türü</label>
              <select v-model="driverForm.license_type" class="w-full border rounded-lg px-3 py-2">
                <option value="B">B Sınıfı</option>
                <option value="C">C Sınıfı</option>
                <option value="D">D Sınıfı</option>
                <option value="E">E Sınıfı</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Durum</label>
              <select v-model="driverForm.status" class="w-full border rounded-lg px-3 py-2">
                <option value="active">Aktif</option>
                <option value="busy">Meşgul</option>
                <option value="offline">Çevrimdışı</option>
              </select>
            </div>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="showDriverModal = false" class="px-4 py-2 border rounded-lg">
              İptal
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
              Kaydet
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

const activeTab = ref('vehicles')
const tabs = [
  { id: 'vehicles', name: 'Araçlar', icon: '🚗' },
  { id: 'drivers', name: 'Sürücüler', icon: '👨‍✈️' },
  { id: 'transfers', name: 'Transferler', icon: '🚐' },
  { id: 'routes', name: 'Rotalar', icon: '🗺️' }
]

const vehicles = ref([])
const drivers = ref([])
const transfers = ref([])
const routes = ref([])

const showVehicleModal = ref(false)
const showDriverModal = ref(false)

const vehicleForm = reactive({
  id: null,
  brand: '',
  model: '',
  plate_number: '',
  capacity: 4,
  status: 'active'
})

const driverForm = reactive({
  id: null,
  name: '',
  phone: '',
  email: '',
  license_type: 'B',
  status: 'active'
})

const fetchVehicles = async () => {
  try {
    const response = await axios.get('/api/admin/transport/vehicles')
    vehicles.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching vehicles:', error)
  }
}

const fetchDrivers = async () => {
  try {
    const response = await axios.get('/api/admin/transport/drivers')
    drivers.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching drivers:', error)
  }
}

const fetchTransfers = async () => {
  try {
    const response = await axios.get('/api/admin/transport/transfers')
    transfers.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching transfers:', error)
  }
}

const fetchRoutes = async () => {
  try {
    const response = await axios.get('/api/admin/transport/routes')
    routes.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching routes:', error)
  }
}

const openVehicleModal = () => {
  Object.assign(vehicleForm, {
    id: null,
    brand: '',
    model: '',
    plate_number: '',
    capacity: 4,
    status: 'active'
  })
  showVehicleModal.value = true
}

const editVehicle = (vehicle) => {
  Object.assign(vehicleForm, vehicle)
  showVehicleModal.value = true
}

const saveVehicle = async () => {
  try {
    if (vehicleForm.id) {
      await axios.put(`/api/admin/transport/vehicles/${vehicleForm.id}`, vehicleForm)
    } else {
      await axios.post('/api/admin/transport/vehicles', vehicleForm)
    }
    showVehicleModal.value = false
    fetchVehicles()
  } catch (error) {
    console.error('Error saving vehicle:', error)
  }
}

const deleteVehicle = async (vehicle) => {
  if (!confirm('Araç silinecek. Emin misiniz?')) return
  try {
    await axios.delete(`/api/admin/transport/vehicles/${vehicle.id}`)
    fetchVehicles()
  } catch (error) {
    console.error('Error deleting vehicle:', error)
  }
}

const openDriverModal = () => {
  Object.assign(driverForm, {
    id: null,
    name: '',
    phone: '',
    email: '',
    license_type: 'B',
    status: 'active'
  })
  showDriverModal.value = true
}

const editDriver = (driver) => {
  Object.assign(driverForm, driver)
  showDriverModal.value = true
}

const saveDriver = async () => {
  try {
    if (driverForm.id) {
      await axios.put(`/api/admin/transport/drivers/${driverForm.id}`, driverForm)
    } else {
      await axios.post('/api/admin/transport/drivers', driverForm)
    }
    showDriverModal.value = false
    fetchDrivers()
  } catch (error) {
    console.error('Error saving driver:', error)
  }
}

const deleteDriver = async (driver) => {
  if (!confirm('Sürücü silinecek. Emin misiniz?')) return
  try {
    await axios.delete(`/api/admin/transport/drivers/${driver.id}`)
    fetchDrivers()
  } catch (error) {
    console.error('Error deleting driver:', error)
  }
}

const openRouteModal = () => {
  console.log('Open route modal')
}

const editRoute = (route) => {
  console.log('Edit route:', route)
}

const deleteRoute = async (route) => {
  if (!confirm('Rota silinecek. Emin misiniz?')) return
  try {
    await axios.delete(`/api/admin/transport/routes/${route.id}`)
    fetchRoutes()
  } catch (error) {
    console.error('Error deleting route:', error)
  }
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR')
}

const getVehicleStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    maintenance: 'bg-yellow-100 text-yellow-800',
    inactive: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getDriverStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    busy: 'bg-yellow-100 text-yellow-800',
    offline: 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getDriverStatusLabel = (status) => {
  const labels = {
    active: 'Aktif',
    busy: 'Meşgul',
    offline: 'Çevrimdışı'
  }
  return labels[status] || status
}

const getTransferStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-purple-100 text-purple-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getTransferStatusLabel = (status) => {
  const labels = {
    pending: 'Beklemede',
    confirmed: 'Onaylandı',
    in_progress: 'Devam Ediyor',
    completed: 'Tamamlandı',
    cancelled: 'İptal'
  }
  return labels[status] || status
}

onMounted(() => {
  fetchVehicles()
  fetchDrivers()
  fetchTransfers()
  fetchRoutes()
})
</script>
