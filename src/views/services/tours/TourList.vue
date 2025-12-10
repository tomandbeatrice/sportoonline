<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-700 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl font-bold">Tüm Turlar</h1>
        <p class="text-green-100">{{ tours.length }} tur bulundu</p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
      </div>

      <!-- Tour Grid -->
      <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="tour in tours"
          :key="tour.id"
          class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow cursor-pointer"
          @click="goToTour(tour.id)"
        >
          <div class="relative h-48">
            <img :src="tour.image" :alt="tour.title" class="w-full h-full object-cover" />
            <div class="absolute top-3 right-3 bg-white px-2 py-1 rounded-lg">
              <span class="text-yellow-500">⭐</span>
              <span class="font-bold text-slate-800">{{ tour.rating }}</span>
            </div>
          </div>
          <div class="p-4">
            <h3 class="font-bold text-slate-800 mb-1">{{ tour.title }}</h3>
            <p class="text-sm text-slate-500 mb-3">📍 {{ tour.location }} • ⏱️ {{ tour.duration }}</p>
            <div class="flex items-center justify-between">
              <div>
                <span class="text-xl font-bold text-green-600">{{ tour.price }}₺</span>
                <span class="text-sm text-slate-500">/kişi</span>
              </div>
              <button class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded-lg hover:bg-green-200 transition-colors">
                İncele
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ServiceNav from '@/components/shared/ServiceNav.vue'
import { tourService, type Tour } from '@/services/tourService'

const router = useRouter()
const tours = ref<Tour[]>([])
const loading = ref(true)

const fetchTours = async () => {
  try {
    const response = await tourService.getTours()
    tours.value = response.data.data
  } catch (error) {
    console.error('Turlar yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const goToTour = (id: number) => {
  router.push(`/tours/${id}`)
}

onMounted(() => {
  fetchTours()
})
</script>
