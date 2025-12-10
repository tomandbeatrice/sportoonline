<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500"></div>
    </div>

    <div v-else-if="activity" class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Breadcrumb -->
      <div class="flex items-center text-sm text-slate-500 mb-6">
        <router-link to="/activities" class="hover:text-orange-600">Aktiviteler</router-link>
        <span class="mx-2">/</span>
        <span class="text-slate-800 font-medium">{{ activity.title }}</span>
      </div>

      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white rounded-2xl overflow-hidden shadow-sm">
            <div class="relative h-96">
              <img :src="activity.image" :alt="activity.title" class="w-full h-full object-cover" />
              <div class="absolute top-4 right-4 bg-white px-3 py-1 rounded-lg shadow-md">
                <span class="font-bold text-slate-800">{{ activity.date.split(' ')[0] }}</span>
              </div>
            </div>
            
            <div class="p-8">
              <h1 class="text-3xl font-bold text-slate-800 mb-4">{{ activity.title }}</h1>
              
              <div class="flex flex-wrap gap-4 mb-6">
                <div class="bg-orange-50 px-4 py-2 rounded-lg flex items-center gap-2 text-orange-700">
                  <span>📍</span>
                  <span class="font-medium">{{ activity.location }}</span>
                </div>
                <div class="bg-orange-50 px-4 py-2 rounded-lg flex items-center gap-2 text-orange-700">
                  <span>📅</span>
                  <span class="font-medium">{{ activity.date }}</span>
                </div>
                <div v-if="activity.instructor" class="bg-orange-50 px-4 py-2 rounded-lg flex items-center gap-2 text-orange-700">
                  <span>👨‍🏫</span>
                  <span class="font-medium">{{ activity.instructor }}</span>
                </div>
              </div>

              <div class="prose max-w-none text-slate-600">
                <h3 class="text-xl font-bold text-slate-800 mb-3">Etkinlik Detayları</h3>
                <p>{{ activity.description || 'Bu etkinlik için detaylı açıklama bulunmamaktadır.' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24">
            <div class="mb-6">
              <span class="text-3xl font-bold text-orange-600">{{ activity.price }}₺</span>
              <span class="text-slate-500">/kişi başı</span>
            </div>

            <div class="space-y-4 mb-6">
              <div class="bg-slate-50 p-4 rounded-xl">
                <label class="block text-sm font-medium text-slate-700 mb-2">Katılımcı Sayısı</label>
                <select v-model="guestCount" class="w-full border-slate-200 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                  <option :value="1">1 Kişi</option>
                  <option :value="2">2 Kişi</option>
                  <option :value="3">3 Kişi</option>
                  <option :value="4">4+ Kişi</option>
                </select>
              </div>
            </div>

            <button 
              @click="addToCart"
              class="w-full bg-orange-600 text-white py-3 rounded-xl font-bold hover:bg-orange-700 transition-colors shadow-lg shadow-orange-200"
            >
              Bilet Al
            </button>
            
            <div class="mt-6 pt-6 border-t border-slate-100">
              <h4 class="font-bold text-slate-800 mb-2">Etkinlik Kuralları</h4>
              <ul class="text-sm text-slate-500 space-y-2">
                <li>• Etkinlikten 15 dakika önce alanda olunuz.</li>
                <li>• Bilet iadesi etkinlikten 24 saat öncesine kadar yapılabilir.</li>
              </ul>
            </div>
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
import { activityService, type Activity } from '@/services/activityService'
import { useCartStore } from '@/stores/cartStore'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()
const toast = useToast()

const activity = ref<Activity | null>(null)
const loading = ref(true)
const guestCount = ref(1)

const fetchActivity = async () => {
  try {
    const id = Number(route.params.id)
    const response = await activityService.getActivity(id)
    activity.value = response.data.data
  } catch (error) {
    console.error('Aktivite detayları yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const addToCart = () => {
  if (!activity.value) return

  cartStore.addToCart({
    id: activity.value.id,
    name: activity.value.title,
    price: activity.value.price,
    quantity: 1,
    type: 'booking',
    image: activity.value.image,
    startDate: activity.value.date,
    endDate: activity.value.date,
    guests: guestCount.value
  })

  toast.success('Etkinlik bileti sepete eklendi!')
  router.push('/cart')
}

onMounted(() => {
  fetchActivity()
})
</script>
