<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl font-bold">Tüm Oteller</h1>
        <p class="text-blue-100">{{ filteredHotels.length }} otel bulundu</p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Filters -->
      <div class="bg-white rounded-xl p-4 mb-6 flex flex-wrap gap-4 items-center">
        <select v-model="filters.stars" class="px-4 py-2 border rounded-lg text-sm">
          <option value="">Tüm Yıldızlar</option>
          <option value="5">5 Yıldız</option>
          <option value="4">4 Yıldız</option>
          <option value="3">3 Yıldız</option>
        </select>
        <select v-model="filters.priceRange" class="px-4 py-2 border rounded-lg text-sm">
          <option value="">Tüm Fiyatlar</option>
          <option value="budget">Ekonomik (< 1000₺)</option>
          <option value="mid">Orta (1000-3000₺)</option>
          <option value="luxury">Lüks (> 3000₺)</option>
        </select>
        <select v-model="sortBy" class="px-4 py-2 border rounded-lg text-sm">
          <option value="rating">Puana Göre</option>
          <option value="price-asc">Fiyat (Artan)</option>
          <option value="price-desc">Fiyat (Azalan)</option>
        </select>
      </div>

      <!-- Hotel Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="hotel in filteredHotels"
          :key="hotel.id"
          class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow cursor-pointer"
          @click="goToHotel(hotel.id)"
        >
          <div class="relative">
            <div class="w-full h-48 bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
              <span class="text-6xl">{{ hotel.icon }}</span>
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
                <span class="text-xl font-bold text-blue-600">{{ hotel.price }}₺</span>
                <span class="text-sm text-slate-500">/gece</span>
              </div>
              <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">{{ hotel.tag }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import ServiceNav from '@/components/shared/ServiceNav.vue'

const router = useRouter()
const sortBy = ref('rating')
const filters = reactive({
  stars: '',
  priceRange: ''
})

const hotels = ref([
  { id: 1, name: 'Four Seasons Bosphorus', location: 'Beşiktaş, İstanbul', icon: '🏨', rating: 4.9, stars: 5, price: 4500, discount: 18, tag: 'Ücretsiz İptal' },
  { id: 2, name: 'Rixos Premium Belek', location: 'Belek, Antalya', icon: '🏖️', rating: 4.7, stars: 5, price: 3200, discount: null, tag: 'Her Şey Dahil' },
  { id: 3, name: 'Swissôtel The Bosphorus', location: 'Beşiktaş, İstanbul', icon: '🌆', rating: 4.6, stars: 5, price: 2800, discount: 20, tag: 'Kahvaltı Dahil' },
  { id: 4, name: 'Hilton Istanbul', location: 'Şişli, İstanbul', icon: '🏢', rating: 4.5, stars: 5, price: 2200, discount: null, tag: 'Merkezi Konum' },
  { id: 5, name: 'Marriott Şişli', location: 'Şişli, İstanbul', icon: '🏨', rating: 4.4, stars: 4, price: 1800, discount: 15, tag: 'İş Oteli' },
  { id: 6, name: 'Holiday Inn Kadıköy', location: 'Kadıköy, İstanbul', icon: '🛏️', rating: 4.2, stars: 4, price: 950, discount: null, tag: 'Ekonomik' },
  { id: 7, name: 'Ibis Taksim', location: 'Taksim, İstanbul', icon: '🛌', rating: 4.0, stars: 3, price: 650, discount: 10, tag: 'Bütçe Dostu' },
  { id: 8, name: 'Akra Hotel', location: 'Lara, Antalya', icon: '🌴', rating: 4.8, stars: 5, price: 3800, discount: null, tag: 'Deniz Manzarası' },
])

const filteredHotels = computed(() => {
  let result = [...hotels.value]
  
  if (filters.stars) {
    result = result.filter(h => h.stars === parseInt(filters.stars))
  }
  
  if (filters.priceRange) {
    switch (filters.priceRange) {
      case 'budget':
        result = result.filter(h => h.price < 1000)
        break
      case 'mid':
        result = result.filter(h => h.price >= 1000 && h.price <= 3000)
        break
      case 'luxury':
        result = result.filter(h => h.price > 3000)
        break
    }
  }
  
  switch (sortBy.value) {
    case 'rating':
      result.sort((a, b) => b.rating - a.rating)
      break
    case 'price-asc':
      result.sort((a, b) => a.price - b.price)
      break
    case 'price-desc':
      result.sort((a, b) => b.price - a.price)
      break
  }
  
  return result
})

const goToHotel = (id: number) => {
  router.push(`/hotels/${id}`)
}
</script>
