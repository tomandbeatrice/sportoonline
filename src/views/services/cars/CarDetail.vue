<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <div v-else-if="car" class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Breadcrumb -->
      <div class="flex items-center text-sm text-slate-500 mb-6">
        <router-link to="/cars" class="hover:text-blue-600">Araç Kiralama</router-link>
        <span class="mx-2">/</span>
        <span class="text-slate-800 font-medium">{{ car.brand }} {{ car.model }}</span>
      </div>

      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white rounded-2xl overflow-hidden shadow-sm">
            <div class="relative h-96">
              <img :src="car.image" :alt="car.model" class="w-full h-full object-cover" />
              <div class="absolute top-4 left-4 bg-black/60 text-white px-3 py-1 rounded-lg">
                {{ car.type }}
              </div>
              <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-lg shadow-md">
                <span class="font-bold text-slate-800">{{ car.supplier }}</span>
              </div>
            </div>
            
            <div class="p-8">
              <h1 class="text-3xl font-bold text-slate-800 mb-6">{{ car.brand }} {{ car.model }}</h1>
              
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-slate-50 p-4 rounded-xl text-center">
                  <div class="text-2xl mb-2">⚙️</div>
                  <div class="text-sm text-slate-500">Vites</div>
                  <div class="font-semibold text-slate-800">{{ car.transmission }}</div>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl text-center">
                  <div class="text-2xl mb-2">⛽</div>
                  <div class="text-sm text-slate-500">Yakıt</div>
                  <div class="font-semibold text-slate-800">{{ car.fuel }}</div>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl text-center">
                  <div class="text-2xl mb-2">💺</div>
                  <div class="text-sm text-slate-500">Koltuk</div>
                  <div class="font-semibold text-slate-800">5 Kişilik</div>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl text-center">
                  <div class="text-2xl mb-2">❄️</div>
                  <div class="text-sm text-slate-500">Klima</div>
                  <div class="font-semibold text-slate-800">Var</div>
                </div>
              </div>

              <div v-if="car.features" class="mb-6">
                <h3 class="text-xl font-bold text-slate-800 mb-3">Araç Özellikleri</h3>
                <ul class="grid md:grid-cols-2 gap-2">
                  <li v-for="(feature, index) in car.features" :key="index" class="flex items-center gap-2 text-slate-600">
                    <span class="text-blue-500">✓</span>
                    {{ feature }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24">
            <div class="mb-6">
              <span class="text-3xl font-bold text-blue-600">{{ car.price_per_day }}₺</span>
              <span class="text-slate-500">/günlük</span>
            </div>

            <div class="space-y-4 mb-6">
              <div class="bg-slate-50 p-4 rounded-xl">
                <label class="block text-sm font-medium text-slate-700 mb-2">Alış Tarihi</label>
                <input v-model="startDate" type="date" class="w-full border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
              </div>
              
              <div class="bg-slate-50 p-4 rounded-xl">
                <label class="block text-sm font-medium text-slate-700 mb-2">İade Tarihi</label>
                <input v-model="endDate" type="date" class="w-full border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
              </div>
            </div>

            <button 
              @click="addToCart"
              class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200"
            >
              Hemen Kirala
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ServiceNav from '@/components/shared/ServiceNav.vue'
import { carService, type Car } from '@/services/carService'
import { useCartStore } from '@/stores/cartStore'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()
const toast = useToast()

const car = ref<Car | null>(null)
const loading = ref(true)
const startDate = ref('')
const endDate = ref('')

const fetchCar = async () => {
  try {
    const id = Number(route.params.id)
    const response = await carService.getCar(id)
    car.value = response.data.data
    
    // Set default dates (tomorrow and day after)
    const tomorrow = new Date()
    tomorrow.setDate(tomorrow.getDate() + 1)
    startDate.value = tomorrow.toISOString().split('T')[0]
    
    const dayAfter = new Date(tomorrow)
    dayAfter.setDate(dayAfter.getDate() + 1)
    endDate.value = dayAfter.toISOString().split('T')[0]
  } catch (error) {
    console.error('Araç detayları yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const addToCart = () => {
  if (!car.value) return

  cartStore.addToCart({
    id: car.value.id,
    name: `${car.value.brand} ${car.value.model}`,
    price: car.value.price_per_day,
    quantity: 1,
    type: 'booking',
    image: car.value.image,
    startDate: startDate.value,
    endDate: endDate.value,
    guests: 1 // Not relevant for car but required by type
  })

  toast.success('Araç kiralama sepete eklendi!')
  router.push('/cart')
}

onMounted(() => {
  fetchCar()
})
</script>
