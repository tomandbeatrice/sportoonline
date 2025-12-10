<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Hotel Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <button @click="goBack" class="flex items-center gap-2 text-blue-100 hover:text-white mb-4">
          ← Geri
        </button>
        <div class="flex items-center gap-1 mb-2">
          <span v-for="n in hotel.stars" :key="n" class="text-yellow-400">★</span>
        </div>
        <h1 class="text-3xl font-bold mb-2">{{ hotel.name }}</h1>
        <p class="text-blue-100">📍 {{ hotel.location }}</p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Hotel Info -->
        <div class="lg:col-span-2">
          <!-- Gallery -->
          <div class="bg-white rounded-2xl overflow-hidden mb-6">
            <div class="h-64 bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
              <span class="text-8xl">{{ hotel.icon }}</span>
            </div>
          </div>

          <!-- Description -->
          <div class="bg-white rounded-2xl p-6 mb-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Otel Hakkında</h2>
            <p class="text-slate-600">{{ hotel.description }}</p>
          </div>

          <!-- Amenities -->
          <div class="bg-white rounded-2xl p-6 mb-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Olanaklar</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div v-for="amenity in hotel.amenities" :key="amenity.name" class="flex items-center gap-2">
                <span class="text-2xl">{{ amenity.icon }}</span>
                <span class="text-sm text-slate-600">{{ amenity.name }}</span>
              </div>
            </div>
          </div>

          <!-- Room Types -->
          <div class="bg-white rounded-2xl p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Oda Tipleri</h2>
            <div class="space-y-4">
              <div
                v-for="room in hotel.rooms"
                :key="room.id"
                class="border border-slate-200 rounded-xl p-4 hover:border-blue-300 transition-colors"
              >
                <div class="flex justify-between items-start">
                  <div>
                    <h3 class="font-bold text-slate-800">{{ room.name }}</h3>
                    <p class="text-sm text-slate-500 mt-1">{{ room.features }}</p>
                    <div class="flex gap-2 mt-2">
                      <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">👥 {{ room.capacity }} kişi</span>
                      <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">📐 {{ room.size }} m²</span>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-2xl font-bold text-blue-600">{{ room.price }}₺</p>
                    <p class="text-sm text-slate-500">/gece</p>
                    <button 
                      @click="selectRoom(room)"
                      class="mt-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition"
                    >
                      Seç
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Booking Sidebar -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24">
            <div class="flex items-center justify-between mb-4">
              <div>
                <span class="text-2xl font-bold text-blue-600">{{ hotel.price }}₺</span>
                <span class="text-slate-500">/gece</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="text-yellow-500">⭐</span>
                <span class="font-bold">{{ hotel.rating }}</span>
              </div>
            </div>

            <div class="space-y-4 mb-6">
              <div>
                <label class="block text-sm text-slate-600 mb-1">Giriş Tarihi</label>
                <input type="date" v-model="booking.checkIn" class="w-full px-4 py-2 border rounded-lg">
              </div>
              <div>
                <label class="block text-sm text-slate-600 mb-1">Çıkış Tarihi</label>
                <input type="date" v-model="booking.checkOut" class="w-full px-4 py-2 border rounded-lg">
              </div>
              <div>
                <label class="block text-sm text-slate-600 mb-1">Misafir Sayısı</label>
                <select v-model="booking.guests" class="w-full px-4 py-2 border rounded-lg">
                  <option value="1">1 Misafir</option>
                  <option value="2">2 Misafir</option>
                  <option value="3">3 Misafir</option>
                  <option value="4">4 Misafir</option>
                </select>
              </div>
            </div>

            <div class="border-t border-slate-200 pt-4 mb-4">
              <div class="flex justify-between text-sm text-slate-600 mb-2">
                <span>{{ nights }} gece x {{ hotel.price }}₺</span>
                <span>{{ hotel.price * nights }}₺</span>
              </div>
              <div class="flex justify-between text-sm text-slate-600 mb-2">
                <span>Hizmet bedeli</span>
                <span>{{ serviceFee }}₺</span>
              </div>
              <div class="flex justify-between font-bold text-lg mt-4">
                <span>Toplam</span>
                <span class="text-blue-600">{{ totalPrice }}₺</span>
              </div>
            </div>

            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold transition">
              Rezervasyon Yap
            </button>

            <p class="text-center text-sm text-slate-400 mt-4">Ücretsiz iptal hakkı</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ServiceNav from '@/components/shared/ServiceNav.vue'

const route = useRoute()
const router = useRouter()

const hotel = ref({
  id: route.params.id,
  name: 'Four Seasons Bosphorus',
  location: 'Beşiktaş, İstanbul',
  icon: '🏨',
  rating: 4.9,
  stars: 5,
  price: 4500,
  description: 'Boğaz manzaralı lüks otelimiz, İstanbul\'un kalbinde benzersiz bir konaklama deneyimi sunuyor. Tarihi yarımadaya yakın konumu ve dünya standartlarındaki hizmet kalitesiyle unutulmaz anılar biriktirin.',
  amenities: [
    { icon: '🏊', name: 'Havuz' },
    { icon: '💆', name: 'Spa' },
    { icon: '🏋️', name: 'Fitness' },
    { icon: '🍽️', name: 'Restoran' },
    { icon: '📶', name: 'Ücretsiz WiFi' },
    { icon: '🅿️', name: 'Otopark' },
    { icon: '🛎️', name: '24 Saat Resepsiyon' },
    { icon: '🧳', name: 'Bagaj Saklama' },
  ],
  rooms: [
    { id: 1, name: 'Deluxe Oda', features: 'Şehir manzarası, King yatak', capacity: 2, size: 35, price: 4500 },
    { id: 2, name: 'Premium Oda', features: 'Deniz manzarası, King yatak, Balkon', capacity: 2, size: 45, price: 5500 },
    { id: 3, name: 'Suite', features: 'Boğaz manzarası, Oturma odası, Jakuzi', capacity: 3, size: 65, price: 8500 },
  ]
})

const booking = reactive({
  checkIn: '',
  checkOut: '',
  guests: '2'
})

const nights = computed(() => {
  if (!booking.checkIn || !booking.checkOut) return 1
  const start = new Date(booking.checkIn)
  const end = new Date(booking.checkOut)
  const diff = Math.ceil((end.getTime() - start.getTime()) / (1000 * 60 * 60 * 24))
  return diff > 0 ? diff : 1
})

const serviceFee = computed(() => Math.round(hotel.value.price * nights.value * 0.1))
const totalPrice = computed(() => hotel.value.price * nights.value + serviceFee.value)

const selectRoom = (room: { price: number }) => {
  hotel.value.price = room.price
}

const goBack = () => {
  router.back()
}
</script>
