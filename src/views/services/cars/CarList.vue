<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl font-bold">Araç Kiralama</h1>
        <p class="text-blue-100">{{ cars.length }} araç bulundu</p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <!-- Car Grid -->
      <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="car in cars"
          :key="car.id"
          class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow cursor-pointer"
          @click="goToCar(car.id)"
        >
          <div class="relative h-48">
            <img :src="car.image" :alt="car.model" class="w-full h-full object-cover" />
            <div class="absolute top-3 right-3 bg-white px-2 py-1 rounded-lg">
              <span class="font-bold text-slate-800 text-xs">{{ car.supplier }}</span>
            </div>
            <div class="absolute bottom-3 left-3 bg-black/50 text-white px-2 py-1 rounded text-xs">
              {{ car.type }}
            </div>
          </div>
          <div class="p-4">
            <h3 class="font-bold text-slate-800 mb-1">{{ car.model }}</h3>
            <p class="text-sm text-slate-500 mb-3">⛽ {{ car.fuel }} • ⚙️ {{ car.transmission }}</p>
            <div class="flex items-center justify-between">
              <div>
                <span class="text-xl font-bold text-blue-600">{{ car.price_per_day }}₺</span>
                <span class="text-sm text-slate-500">/gün</span>
              </div>
              <button class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-lg hover:bg-blue-200 transition-colors">
                Kirala
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
import { carService, type Car } from '@/services/carService'

const router = useRouter()
const cars = ref<Car[]>([])
const loading = ref(true)

const fetchCars = async () => {
  try {
    const response = await carService.getCars()
    cars.value = response.data.data
  } catch (error) {
    console.error('Araçlar yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const goToCar = (id: number) => {
  router.push(`/cars/${id}`)
}

onMounted(() => {
  fetchCars()
})
</script>
