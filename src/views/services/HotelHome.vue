<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Service Navigation -->
    <ServiceNav />
    
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center gap-4 mb-6">
          <span class="text-5xl">🏨</span>
          <div>
            <h1 class="text-3xl font-bold">Otel Rezervasyonu</h1>
            <p class="text-blue-100">En iyi fiyatlarla konforlu konaklama</p>
          </div>
        </div>
        
        <!-- Search Form -->
        <div class="bg-white rounded-2xl p-6 shadow-xl max-w-4xl">
          <div class="grid md:grid-cols-4 gap-4">
            <div>
              <label class="block text-slate-600 text-sm mb-1">Nereye?</label>
              <input
                v-model="searchForm.destination"
                type="text"
                placeholder="Şehir veya otel adı"
                class="w-full px-4 py-3 border rounded-lg text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-slate-600 text-sm mb-1">Giriş Tarihi</label>
              <input
                v-model="searchForm.checkIn"
                type="date"
                class="w-full px-4 py-3 border rounded-lg text-slate-800 focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-slate-600 text-sm mb-1">Çıkış Tarihi</label>
              <input
                v-model="searchForm.checkOut"
                type="date"
                class="w-full px-4 py-3 border rounded-lg text-slate-800 focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-slate-600 text-sm mb-1">Misafir</label>
              <select
                v-model="searchForm.guests"
                class="w-full px-4 py-3 border rounded-lg text-slate-800 focus:ring-2 focus:ring-blue-500"
              >
                <option value="1">1 Misafir</option>
                <option value="2">2 Misafir</option>
                <option value="3">3 Misafir</option>
                <option value="4">4+ Misafir</option>
              </select>
            </div>
          </div>
          <button
            @click="searchHotels"
            class="mt-4 w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold transition-colors"
          >
            🔍 Otel Ara
          </button>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Popular Destinations -->
      <section class="mb-10">
        <h2 class="text-xl font-bold text-slate-800 mb-4">Popüler Destinasyonlar</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div
            v-for="destination in popularDestinations"
            :key="destination.id"
            @click="selectDestination(destination.name)"
            class="relative rounded-2xl overflow-hidden cursor-pointer group h-40"
          >
            <div class="w-full h-full bg-gradient-to-br flex items-center justify-center" :class="destination.gradient">
              <span class="text-5xl group-hover:scale-110 transition-transform">{{ destination.icon }}</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent" />
            <div class="absolute bottom-4 left-4 text-white">
              <h3 class="font-bold text-lg">{{ destination.name }}</h3>
              <p class="text-sm text-slate-200">{{ destination.hotelCount }} otel</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Featured Hotels -->
      <section class="mb-10">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-slate-800">Öne Çıkan Oteller</h2>
          <router-link to="/hotels/all" class="text-blue-600 hover:text-blue-700 font-medium">
            Tümünü Gör →
          </router-link>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="hotel in featuredHotels"
            :key="hotel.id"
            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow cursor-pointer"
            @click="goToHotel(hotel.id)"
          >
            <div class="relative">
              <div class="w-full h-48 bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                <span class="text-6xl">🏨</span>
              </div>
              <div class="absolute top-3 right-3 bg-white px-2 py-1 rounded-lg">
                <span class="text-yellow-500">⭐</span>
                <span class="font-bold text-slate-800">{{ hotel.rating }}</span>
              </div>
              <div v-if="hotel.discount" class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-lg text-sm font-bold">
                %{{ hotel.discount }} İndirim
              </div>
            </div>
            <div class="p-4">
              <div class="flex items-center gap-1 mb-2">
                <span v-for="n in hotel.stars" :key="n" class="text-yellow-400 text-sm">★</span>
              </div>
              <h3 class="font-bold text-slate-800 mb-1">{{ hotel.name }}</h3>
              <p class="text-sm text-slate-500 mb-3">📍 {{ hotel.location }}</p>
              <div class="flex items-center justify-between">
                <div>
                  <span class="text-sm text-slate-400 line-through" v-if="hotel.originalPrice">{{ hotel.originalPrice }}₺</span>
                  <span class="text-xl font-bold text-blue-600 ml-1">{{ hotel.price }}₺</span>
                  <span class="text-sm text-slate-500">/gece</span>
                </div>
                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">{{ hotel.tag }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Hotel Types -->
      <section class="mb-10">
        <h2 class="text-xl font-bold text-slate-800 mb-4">Konaklama Türleri</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div
            v-for="type in hotelTypes"
            :key="type.id"
            class="bg-white rounded-xl p-6 text-center hover:shadow-md transition-shadow cursor-pointer border-2 border-slate-100 hover:border-blue-200"
          >
            <span class="text-4xl mb-3 block">{{ type.icon }}</span>
            <h4 class="font-bold text-slate-800">{{ type.name }}</h4>
            <p class="text-sm text-slate-500 mt-1">{{ type.count }} seçenek</p>
          </div>
        </div>
      </section>

      <!-- Transfer Recommendation Banner -->
      <section class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-2xl font-bold mb-2">🚗 Havalimanı Transferi</h3>
            <p class="text-emerald-100 mb-4">Otel rezervasyonunuzla birlikte transfer ekleyin, %15 indirim kazanın!</p>
            <router-link
              to="/rides"
              class="inline-block bg-white text-emerald-600 px-6 py-2 rounded-lg font-bold hover:bg-emerald-50 transition-colors"
            >
              Transfer Ekle
            </router-link>
          </div>
          <span class="text-6xl hidden md:block">✈️</span>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import ServiceNav from '@/components/shared/ServiceNav.vue'

const router = useRouter()

const searchForm = reactive({
  destination: '',
  checkIn: '',
  checkOut: '',
  guests: '2'
})

const popularDestinations = [
  { id: 1, name: 'İstanbul', icon: '🕌', hotelCount: 1250, gradient: 'from-blue-400 to-purple-500' },
  { id: 2, name: 'Antalya', icon: '🏖️', hotelCount: 890, gradient: 'from-orange-400 to-red-500' },
  { id: 3, name: 'İzmir', icon: '🌊', hotelCount: 450, gradient: 'from-cyan-400 to-blue-500' },
  { id: 4, name: 'Bodrum', icon: '⛵', hotelCount: 380, gradient: 'from-teal-400 to-emerald-500' },
]

const featuredHotels = ref([
  {
    id: 1,
    name: 'Four Seasons Bosphorus',
    location: 'Beşiktaş, İstanbul',
    rating: 4.9,
    stars: 5,
    price: 4500,
    originalPrice: 5500,
    discount: 18,
    tag: 'Ücretsiz İptal'
  },
  {
    id: 2,
    name: 'Rixos Premium Belek',
    location: 'Belek, Antalya',
    rating: 4.7,
    stars: 5,
    price: 3200,
    originalPrice: null,
    discount: null,
    tag: 'Her Şey Dahil'
  },
  {
    id: 3,
    name: 'Swissôtel The Bosphorus',
    location: 'Beşiktaş, İstanbul',
    rating: 4.6,
    stars: 5,
    price: 2800,
    originalPrice: 3500,
    discount: 20,
    tag: 'Kahvaltı Dahil'
  },
])

const hotelTypes = [
  { id: 1, name: 'Otel', icon: '🏨', count: 2500 },
  { id: 2, name: 'Apart', icon: '🏢', count: 850 },
  { id: 3, name: 'Villa', icon: '🏡', count: 320 },
  { id: 4, name: 'Butik Otel', icon: '🏛️', count: 180 },
]

const searchHotels = () => {
  router.push({
    path: '/hotels/search',
    query: {
      destination: searchForm.destination,
      checkIn: searchForm.checkIn,
      checkOut: searchForm.checkOut,
      guests: searchForm.guests
    }
  })
}

const selectDestination = (destination: string) => {
  searchForm.destination = destination
}

const goToHotel = (hotelId: number) => {
  router.push(`/hotels/${hotelId}`)
}
</script>
