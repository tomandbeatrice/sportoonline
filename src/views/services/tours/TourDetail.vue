<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
    </div>

    <div v-else-if="tour" class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Breadcrumb -->
      <div class="flex items-center text-sm text-slate-500 mb-6">
        <router-link to="/tours" class="hover:text-green-600">Turlar</router-link>
        <span class="mx-2">/</span>
        <span class="text-slate-800 font-medium">{{ tour.title }}</span>
      </div>

      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white rounded-2xl overflow-hidden shadow-sm">
            <div class="relative h-96">
              <img :src="tour.image" :alt="tour.title" class="w-full h-full object-cover" />
              <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-lg shadow-md">
                <span class="text-yellow-500">⭐</span>
                <span class="font-bold text-slate-800">{{ tour.rating }}</span>
                <span class="text-slate-400 text-sm ml-1">({{ tour.reviews }} değerlendirme)</span>
              </div>
            </div>
            
            <div class="p-8">
              <h1 class="text-3xl font-bold text-slate-800 mb-4">{{ tour.title }}</h1>
              <div class="flex items-center gap-6 text-slate-600 mb-6">
                <div class="flex items-center gap-2">
                  <span>📍</span>
                  <span>{{ tour.location }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <span>⏱️</span>
                  <span>{{ tour.duration }}</span>
                </div>
              </div>

              <div class="prose max-w-none text-slate-600">
                <h3 class="text-xl font-bold text-slate-800 mb-3">Tur Hakkında</h3>
                <p>{{ tour.description || 'Bu tur için detaylı açıklama bulunmamaktadır.' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24">
            <div class="mb-6">
              <span class="text-3xl font-bold text-green-600">{{ tour.price }}₺</span>
              <span class="text-slate-500">/kişi başı</span>
            </div>

            <div class="space-y-4 mb-6">
              <div class="bg-slate-50 p-4 rounded-xl">
                <label class="block text-sm font-medium text-slate-700 mb-2">Tarih Seçin</label>
                <select v-model="selectedDate" class="w-full border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500">
                  <option v-for="date in tour.dates" :key="date" :value="date">{{ date }}</option>
                </select>
              </div>
              
              <div class="bg-slate-50 p-4 rounded-xl">
                <label class="block text-sm font-medium text-slate-700 mb-2">Kişi Sayısı</label>
                <select v-model="guestCount" class="w-full border-slate-200 rounded-lg focus:ring-green-500 focus:border-green-500">
                  <option :value="1">1 Yetişkin</option>
                  <option :value="2">2 Yetişkin</option>
                  <option :value="3">3 Yetişkin</option>
                  <option :value="4">4 Yetişkin</option>
                </select>
              </div>
            </div>

            <button 
              @click="addToCart"
              class="w-full bg-green-600 text-white py-3 rounded-xl font-bold hover:bg-green-700 transition-colors shadow-lg shadow-green-200"
            >
              Rezervasyon Yap
            </button>
            
            <p class="text-center text-xs text-slate-400 mt-4">
              Ücretsiz iptal seçeneği mevcuttur.
            </p>
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
import { tourService, type Tour } from '@/services/tourService'
import { useCartStore } from '@/stores/cartStore'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()
const toast = useToast()

const tour = ref<Tour | null>(null)
const loading = ref(true)
const selectedDate = ref('')
const guestCount = ref(1)

const fetchTour = async () => {
  try {
    const id = Number(route.params.id)
    const response = await tourService.getTour(id)
    tour.value = response.data.data
    if (tour.value && tour.value.dates.length > 0) {
      selectedDate.value = tour.value.dates[0]
    }
  } catch (error) {
    console.error('Tur detayları yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const addToCart = () => {
  if (!tour.value) return

  cartStore.addToCart({
    id: tour.value.id,
    name: tour.value.title,
    price: tour.value.price,
    quantity: 1,
    type: 'booking',
    image: tour.value.image,
    startDate: selectedDate.value,
    endDate: selectedDate.value, // Assuming 1 day or handled elsewhere
    guests: guestCount.value
  })

  toast.success('Tur sepete eklendi!')
  router.push('/cart')
}

onMounted(() => {
  fetchTour()
})
</script>
