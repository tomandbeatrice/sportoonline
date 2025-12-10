<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Hero / Search Section -->
    <div class="bg-gradient-to-r from-yellow-500 to-amber-600 text-white py-12">
      <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <h1 class="text-3xl font-bold mb-6 text-center">Nereye Gitmek İstiyorsunuz?</h1>
        
        <!-- Search Form -->
        <div class="bg-white rounded-2xl p-6 shadow-xl">
          <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-slate-600 text-sm font-medium mb-2">Nereden</label>
              <div class="relative">
                <input
                  v-model="searchForm.from"
                  type="text"
                  placeholder="Mevcut konum veya adres girin"
                  class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                />
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">📍</span>
              </div>
            </div>
            <div>
              <label class="block text-slate-600 text-sm font-medium mb-2">Nereye</label>
              <div class="relative">
                <input
                  v-model="searchForm.to"
                  type="text"
                  placeholder="Gitmek istediğiniz adresi girin"
                  class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                />
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🎯</span>
              </div>
            </div>
          </div>
          
          <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div>
              <label class="block text-slate-600 text-sm font-medium mb-2">Tarih</label>
              <input
                v-model="searchForm.date"
                type="date"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-yellow-500"
              />
            </div>
            <div>
              <label class="block text-slate-600 text-sm font-medium mb-2">Saat</label>
              <input
                v-model="searchForm.time"
                type="time"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-yellow-500"
              />
            </div>
            <div>
              <label class="block text-slate-600 text-sm font-medium mb-2">Yolcu Sayısı</label>
              <select
                v-model="searchForm.passengers"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-yellow-500"
              >
                <option value="1">1 Yolcu</option>
                <option value="2">2 Yolcu</option>
                <option value="3">3 Yolcu</option>
                <option value="4">4 Yolcu</option>
                <option value="5">5+ Yolcu</option>
              </select>
            </div>
          </div>
          
          <button
            @click="searchRides"
            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-3 rounded-xl font-bold text-lg transition-colors"
          >
            🔍 Araç Bul
          </button>
        </div>
      </div>
    </div>

    <!-- Available Vehicles -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
      <h2 class="text-xl font-bold text-slate-800 mb-6">Mevcut Araçlar</h2>
      
      <div class="space-y-4">
        <div
          v-for="vehicle in vehicles"
          :key="vehicle.id"
          @click="selectedVehicle = vehicle.id"
          :class="[
            'bg-white rounded-2xl p-4 cursor-pointer transition-all border-2',
            selectedVehicle === vehicle.id
              ? 'border-yellow-500 shadow-lg'
              : 'border-transparent shadow-sm hover:shadow-md'
          ]"
        >
          <div class="flex items-center gap-4">
            <div class="text-4xl">{{ vehicle.icon }}</div>
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <h3 class="font-bold text-slate-800">{{ vehicle.name }}</h3>
                <span v-if="vehicle.tag" class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full">
                  {{ vehicle.tag }}
                </span>
              </div>
              <p class="text-sm text-slate-500">{{ vehicle.description }}</p>
              <div class="flex items-center gap-4 mt-2 text-sm text-slate-400">
                <span>👤 {{ vehicle.capacity }} Kişi</span>
                <span>⏱️ {{ vehicle.eta }} dk</span>
              </div>
            </div>
            <div class="text-right">
              <div class="text-xl font-bold text-slate-800">{{ vehicle.price }}₺</div>
              <div class="text-sm text-slate-400">tahmini</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Request Button -->
      <button
        v-if="selectedVehicle"
        class="w-full mt-6 bg-yellow-500 hover:bg-yellow-600 text-white py-4 rounded-xl font-bold text-lg transition-colors"
      >
        🚗 Araç Çağır
      </button>
    </div>

    <!-- Recent Destinations -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 pb-8">
      <h2 class="text-xl font-bold text-slate-800 mb-4">Son Gidilen Yerler</h2>
      <div class="space-y-2">
        <div
          v-for="destination in recentDestinations"
          :key="destination.id"
          class="bg-white rounded-xl p-4 flex items-center gap-3 cursor-pointer hover:bg-slate-50 transition-colors"
          @click="searchForm.to = destination.address"
        >
          <span class="text-xl">{{ destination.icon }}</span>
          <div>
            <div class="font-medium text-slate-800">{{ destination.name }}</div>
            <div class="text-sm text-slate-400">{{ destination.address }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import ServiceNav from '@/components/shared/ServiceNav.vue'

const route = useRoute()

const searchForm = reactive({
  from: '',
  to: '',
  date: '',
  time: '',
  passengers: '1'
})

onMounted(() => {
  if (route.query.pickup) searchForm.from = route.query.pickup as string
  if (route.query.dropoff) searchForm.to = route.query.dropoff as string
  if (route.query.date) searchForm.date = route.query.date as string
  if (route.query.time) searchForm.time = route.query.time as string
  if (route.query.passengers) searchForm.passengers = route.query.passengers as string
})

const selectedVehicle = ref<number | null>(null)

const vehicles = [
  { id: 1, name: 'Ekonomi', icon: '🚗', description: 'Uygun fiyatlı, pratik ulaşım', capacity: 4, eta: 5, price: 85, tag: 'En Popüler' },
  { id: 2, name: 'Konfor', icon: '🚙', description: 'Geniş, rahat araçlar', capacity: 4, eta: 7, price: 120, tag: null },
  { id: 3, name: 'Premium', icon: '🚘', description: 'Lüks araç deneyimi', capacity: 4, eta: 10, price: 180, tag: null },
  { id: 4, name: 'XL', icon: '🚐', description: 'Büyük gruplar için ideal', capacity: 6, eta: 8, price: 150, tag: null },
  { id: 5, name: 'Motor Kurye', icon: '🏍️', description: 'Hızlı, tek kişilik ulaşım', capacity: 1, eta: 3, price: 45, tag: 'En Hızlı' },
]

const recentDestinations = [
  { id: 1, name: 'İş Yeri', icon: '🏢', address: 'Maslak, Sarıyer, İstanbul' },
  { id: 2, name: 'Ev', icon: '🏠', address: 'Kadıköy, İstanbul' },
  { id: 3, name: 'Havalimanı', icon: '✈️', address: 'İstanbul Havalimanı, Arnavutköy' },
]

const searchRides = () => {
  console.log('Searching rides:', searchForm)
}
</script>
