<template>
  <div class="min-h-screen bg-gradient-to-b from-green-50 to-white">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-700 text-white py-12">
      <div class="container mx-auto px-4">
        <div class="flex items-center gap-4 mb-6">
          <span class="text-5xl">🚗</span>
          <div>
            <h1 class="text-3xl font-bold">Ulaşım Hizmetleri</h1>
            <p class="text-green-100">Güvenli ve konforlu yolculuk</p>
          </div>
        </div>
        
        <!-- Quick Booking Form -->
        <div class="bg-white rounded-2xl p-6 shadow-xl max-w-3xl">
          <div class="flex gap-4 mb-6">
            <button
              v-for="tab in serviceTabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'px-6 py-2 rounded-full font-medium transition-all',
                activeTab === tab.id 
                  ? 'bg-green-600 text-white' 
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
            >
              {{ tab.icon }} {{ tab.name }}
            </button>
          </div>

          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-600 text-sm mb-1">Nereden?</label>
              <input
                v-model="bookingForm.pickup"
                type="text"
                placeholder="Alış noktası"
                class="w-full px-4 py-3 border rounded-lg text-gray-800 focus:ring-2 focus:ring-green-500"
              />
            </div>
            <div>
              <label class="block text-gray-600 text-sm mb-1">Nereye?</label>
              <input
                v-model="bookingForm.dropoff"
                type="text"
                placeholder="Varış noktası"
                class="w-full px-4 py-3 border rounded-lg text-gray-800 focus:ring-2 focus:ring-green-500"
              />
            </div>
          </div>

          <div class="grid md:grid-cols-3 gap-4 mt-4">
            <div>
              <label class="block text-gray-600 text-sm mb-1">Tarih</label>
              <input
                v-model="bookingForm.date"
                type="date"
                class="w-full px-4 py-3 border rounded-lg text-gray-800 focus:ring-2 focus:ring-green-500"
              />
            </div>
            <div>
              <label class="block text-gray-600 text-sm mb-1">Saat</label>
              <input
                v-model="bookingForm.time"
                type="time"
                class="w-full px-4 py-3 border rounded-lg text-gray-800 focus:ring-2 focus:ring-green-500"
              />
            </div>
            <div>
              <label class="block text-gray-600 text-sm mb-1">Yolcu</label>
              <select
                v-model="bookingForm.passengers"
                class="w-full px-4 py-3 border rounded-lg text-gray-800 focus:ring-2 focus:ring-green-500"
              >
                <option value="1">1 Yolcu</option>
                <option value="2">2 Yolcu</option>
                <option value="3">3 Yolcu</option>
                <option value="4">4+ Yolcu</option>
              </select>
            </div>
          </div>

          <button
            @click="searchRides"
            class="mt-6 w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-bold text-lg transition-colors"
          >
            🔍 Araç Bul
          </button>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-4 py-8">
      <!-- Service Types -->
      <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Hizmet Türleri</h2>
        <div class="grid md:grid-cols-4 gap-4">
          <div
            v-for="service in serviceTypes"
            :key="service.id"
            class="bg-white rounded-2xl p-6 border-2 border-gray-100 hover:border-green-300 hover:shadow-lg transition-all cursor-pointer"
          >
            <span class="text-4xl mb-4 block">{{ service.icon }}</span>
            <h3 class="font-bold text-gray-800 mb-2">{{ service.name }}</h3>
            <p class="text-sm text-gray-500 mb-3">{{ service.description }}</p>
            <div class="flex justify-between items-center">
              <span class="text-green-600 font-bold">{{ service.startingPrice }}₺'den</span>
              <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">{{ service.eta }}</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Popular Routes -->
      <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Popüler Rotalar</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="route in popularRoutes"
            :key="route.id"
            @click="selectRoute(route)"
            class="bg-white rounded-xl p-4 flex items-center gap-4 hover:shadow-md transition-shadow cursor-pointer border border-gray-100"
          >
            <div class="bg-green-100 p-3 rounded-lg">
              <span class="text-2xl">{{ route.icon }}</span>
            </div>
            <div class="flex-1">
              <h4 class="font-medium text-gray-800">{{ route.from }} → {{ route.to }}</h4>
              <p class="text-sm text-gray-500">{{ route.distance }} • {{ route.duration }}</p>
            </div>
            <div class="text-right">
              <span class="font-bold text-green-600">{{ route.price }}₺</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Vehicle Classes -->
      <section class="mb-10">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Araç Sınıfları</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <div
            v-for="vehicle in vehicleClasses"
            :key="vehicle.id"
            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow"
          >
            <img :src="vehicle.image" :alt="vehicle.name" class="w-full h-40 object-cover" />
            <div class="p-4">
              <div class="flex justify-between items-start mb-2">
                <h3 class="font-bold text-gray-800">{{ vehicle.name }}</h3>
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ vehicle.capacity }}</span>
              </div>
              <p class="text-sm text-gray-500 mb-3">{{ vehicle.description }}</p>
              <div class="flex items-center justify-between">
                <span class="text-green-600 font-bold text-lg">{{ vehicle.pricePerKm }}₺/km</span>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                  Seç
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Benefits -->
      <section class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl p-8 text-white">
        <h2 class="text-2xl font-bold mb-6 text-center">Neden Bizi Tercih Etmelisiniz?</h2>
        <div class="grid md:grid-cols-4 gap-6">
          <div class="text-center">
            <span class="text-4xl mb-3 block">✅</span>
            <h4 class="font-bold mb-1">Güvenli Sürücüler</h4>
            <p class="text-gray-400 text-sm">Tüm sürücülerimiz doğrulanmış ve sigortalıdır</p>
          </div>
          <div class="text-center">
            <span class="text-4xl mb-3 block">💰</span>
            <h4 class="font-bold mb-1">Sabit Fiyat</h4>
            <p class="text-gray-400 text-sm">Sürpriz yok, gördüğünüz fiyatı ödersiniz</p>
          </div>
          <div class="text-center">
            <span class="text-4xl mb-3 block">📱</span>
            <h4 class="font-bold mb-1">Canlı Takip</h4>
            <p class="text-gray-400 text-sm">Aracınızı gerçek zamanlı takip edin</p>
          </div>
          <div class="text-center">
            <span class="text-4xl mb-3 block">🎧</span>
            <h4 class="font-bold mb-1">7/24 Destek</h4>
            <p class="text-gray-400 text-sm">Her zaman yanınızdayız</p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const activeTab = ref('transfer')

const serviceTabs = [
  { id: 'transfer', name: 'Transfer', icon: '🚗' },
  { id: 'rental', name: 'Araç Kiralama', icon: '🔑' },
  { id: 'vip', name: 'VIP', icon: '👔' },
]

const bookingForm = reactive({
  pickup: '',
  dropoff: '',
  date: '',
  time: '',
  passengers: '2'
})

const serviceTypes = [
  {
    id: 1,
    name: 'Havalimanı Transfer',
    icon: '✈️',
    description: 'Havalimanı gidiş-dönüş transferi',
    startingPrice: 250,
    eta: '24 saat önceden'
  },
  {
    id: 2,
    name: 'Şehir İçi Transfer',
    icon: '🏙️',
    description: 'Şehir içi hızlı ulaşım',
    startingPrice: 80,
    eta: '15 dk içinde'
  },
  {
    id: 3,
    name: 'Şehirler Arası',
    icon: '🛣️',
    description: 'Uzun mesafe konforlu yolculuk',
    startingPrice: 500,
    eta: 'Planlı'
  },
  {
    id: 4,
    name: 'Saatlik Kiralama',
    icon: '⏰',
    description: 'Şoförlü araç kiralama',
    startingPrice: 300,
    eta: 'Min. 3 saat'
  },
]

const popularRoutes = [
  { id: 1, icon: '✈️', from: 'İstanbul Havalimanı', to: 'Taksim', distance: '45 km', duration: '45 dk', price: 350 },
  { id: 2, icon: '✈️', from: 'Sabiha Gökçen', to: 'Kadıköy', distance: '25 km', duration: '30 dk', price: 250 },
  { id: 3, icon: '🏨', from: 'Taksim', to: 'Sultanahmet', distance: '5 km', duration: '15 dk', price: 80 },
  { id: 4, icon: '🛣️', from: 'İstanbul', to: 'Bursa', distance: '150 km', duration: '2 saat', price: 800 },
  { id: 5, icon: '✈️', from: 'Antalya Havalimanı', to: 'Kemer', distance: '45 km', duration: '40 dk', price: 300 },
  { id: 6, icon: '🏨', from: 'Belek', to: 'Antalya Merkez', distance: '35 km', duration: '35 dk', price: 250 },
]

const vehicleClasses = [
  {
    id: 1,
    name: 'Ekonomi',
    image: '/images/vehicles/economy.jpg',
    capacity: '4 Kişi',
    description: 'Uygun fiyatlı, konforlu şehir içi ulaşım',
    pricePerKm: 8
  },
  {
    id: 2,
    name: 'Konfor',
    image: '/images/vehicles/comfort.jpg',
    capacity: '4 Kişi',
    description: 'Geniş iç mekan, premium konfor',
    pricePerKm: 12
  },
  {
    id: 3,
    name: 'VIP',
    image: '/images/vehicles/vip.jpg',
    capacity: '6 Kişi',
    description: 'Lüks araçlar, özel hizmet',
    pricePerKm: 20
  },
]

const searchRides = () => {
  router.push({
    path: '/rides/search',
    query: {
      pickup: bookingForm.pickup,
      dropoff: bookingForm.dropoff,
      date: bookingForm.date,
      time: bookingForm.time,
      passengers: bookingForm.passengers
    }
  })
}

const selectRoute = (route: any) => {
  bookingForm.pickup = route.from
  bookingForm.dropoff = route.to
}
</script>
