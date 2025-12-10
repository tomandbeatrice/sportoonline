<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl font-bold mb-2">🔍 Otel Ara</h1>
        <p class="text-blue-100">En iyi fiyatlarla otel bul</p>
      </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
      <!-- Search Form -->
      <div class="bg-white rounded-2xl p-6 shadow-sm mb-8">
        <div class="grid md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-slate-600 text-sm font-medium mb-2">Şehir veya Otel Adı</label>
            <div class="relative">
              <input
                v-model="searchForm.location"
                type="text"
                placeholder="Nerede konaklayacaksınız?"
                class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">📍</span>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-slate-600 text-sm font-medium mb-2">Giriş</label>
              <input
                v-model="searchForm.checkIn"
                type="date"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-slate-600 text-sm font-medium mb-2">Çıkış</label>
              <input
                v-model="searchForm.checkOut"
                type="date"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>
        </div>
        
        <div class="grid md:grid-cols-3 gap-4 mb-4">
          <div>
            <label class="block text-slate-600 text-sm font-medium mb-2">Oda Sayısı</label>
            <select
              v-model="searchForm.rooms"
              class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-blue-500"
            >
              <option value="1">1 Oda</option>
              <option value="2">2 Oda</option>
              <option value="3">3 Oda</option>
              <option value="4">4+ Oda</option>
            </select>
          </div>
          <div>
            <label class="block text-slate-600 text-sm font-medium mb-2">Yetişkin</label>
            <select
              v-model="searchForm.adults"
              class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-blue-500"
            >
              <option value="1">1 Yetişkin</option>
              <option value="2">2 Yetişkin</option>
              <option value="3">3 Yetişkin</option>
              <option value="4">4 Yetişkin</option>
            </select>
          </div>
          <div>
            <label class="block text-slate-600 text-sm font-medium mb-2">Çocuk</label>
            <select
              v-model="searchForm.children"
              class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-blue-500"
            >
              <option value="0">Çocuk Yok</option>
              <option value="1">1 Çocuk</option>
              <option value="2">2 Çocuk</option>
              <option value="3">3 Çocuk</option>
            </select>
          </div>
        </div>
        
        <button
          @click="searchHotels"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-lg transition-colors"
        >
          🔍 Otel Ara
        </button>
      </div>

      <!-- Popular Destinations -->
      <div class="mb-8">
        <h2 class="text-xl font-bold text-slate-800 mb-4">Popüler Destinasyonlar</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div
            v-for="destination in destinations"
            :key="destination.id"
            @click="searchForm.location = destination.name"
            class="relative rounded-2xl overflow-hidden cursor-pointer group"
          >
            <img :src="destination.image" :alt="destination.name" class="w-full h-32 object-cover group-hover:scale-110 transition-transform duration-300" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
            <div class="absolute bottom-3 left-3 text-white">
              <div class="font-bold">{{ destination.name }}</div>
              <div class="text-sm text-white/80">{{ destination.hotels }} otel</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Searches -->
      <div>
        <h2 class="text-xl font-bold text-slate-800 mb-4">Son Aramalar</h2>
        <div class="space-y-2">
          <div
            v-for="search in recentSearches"
            :key="search.id"
            @click="applySearch(search)"
            class="bg-white rounded-xl p-4 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition-colors"
          >
            <div class="flex items-center gap-3">
              <span class="text-xl">🕐</span>
              <div>
                <div class="font-medium text-slate-800">{{ search.location }}</div>
                <div class="text-sm text-slate-400">{{ search.dates }} • {{ search.guests }}</div>
              </div>
            </div>
            <span class="text-blue-600">→</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import ServiceNav from '@/components/shared/ServiceNav.vue'

const router = useRouter()
const route = useRoute()

const searchForm = reactive({
  location: '',
  checkIn: '',
  checkOut: '',
  rooms: '1',
  adults: '2',
  children: '0'
})

onMounted(() => {
  if (route.query.destination) searchForm.location = route.query.destination as string
  if (route.query.checkIn) searchForm.checkIn = route.query.checkIn as string
  if (route.query.checkOut) searchForm.checkOut = route.query.checkOut as string
  if (route.query.guests) searchForm.adults = route.query.guests as string
})

const destinations = [
  { id: 1, name: 'İstanbul', hotels: 2500, image: 'https://placehold.co/200x150/3B82F6/FFFFFF?text=IST' },
  { id: 2, name: 'Antalya', hotels: 1800, image: 'https://placehold.co/200x150/F97316/FFFFFF?text=AYT' },
  { id: 3, name: 'İzmir', hotels: 950, image: 'https://placehold.co/200x150/22C55E/FFFFFF?text=IZM' },
  { id: 4, name: 'Bodrum', hotels: 620, image: 'https://placehold.co/200x150/06B6D4/FFFFFF?text=BJV' },
]

const recentSearches = [
  { id: 1, location: 'İstanbul, Taksim', dates: '15-18 Mart', guests: '2 Yetişkin' },
  { id: 2, location: 'Antalya, Konyaaltı', dates: '22-25 Mart', guests: '2 Yetişkin, 1 Çocuk' },
]

const searchHotels = () => {
  router.push({
    path: '/hotels/all',
    query: {
      location: searchForm.location,
      checkIn: searchForm.checkIn,
      checkOut: searchForm.checkOut,
      rooms: searchForm.rooms,
      adults: searchForm.adults,
      children: searchForm.children
    }
  })
}

const applySearch = (search: any) => {
  searchForm.location = search.location
}
</script>
