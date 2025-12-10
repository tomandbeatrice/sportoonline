<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-600 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl font-bold">Aktiviteler</h1>
        <p class="text-orange-100">{{ activities.length }} aktivite bulundu</p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500"></div>
      </div>

      <!-- Activity Grid -->
      <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="activity in activities"
          :key="activity.id"
          class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow cursor-pointer"
          @click="goToActivity(activity.id)"
        >
          <div class="relative h-48">
            <img :src="activity.image" :alt="activity.title" class="w-full h-full object-cover" />
            <div v-if="activity.instructor" class="absolute bottom-3 left-3 bg-black/50 text-white px-2 py-1 rounded text-xs">
              Eğitmen: {{ activity.instructor }}
            </div>
          </div>
          <div class="p-4">
            <h3 class="font-bold text-slate-800 mb-1">{{ activity.title }}</h3>
            <p class="text-sm text-slate-500 mb-3">📍 {{ activity.location }} • 📅 {{ activity.date }}</p>
            <div class="flex items-center justify-between">
              <div>
                <span class="text-xl font-bold text-orange-600">{{ activity.price }}₺</span>
                <span class="text-sm text-slate-500">/kişi</span>
              </div>
              <button class="text-sm bg-orange-100 text-orange-700 px-3 py-1 rounded-lg hover:bg-orange-200 transition-colors">
                Katıl
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
import { activityService, type Activity } from '@/services/activityService'

const router = useRouter()
const activities = ref<Activity[]>([])
const loading = ref(true)

const fetchActivities = async () => {
  try {
    const response = await activityService.getActivities()
    activities.value = response.data.data
  } catch (error) {
    console.error('Aktiviteler yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const goToActivity = (id: number) => {
  router.push(`/activities/${id}`)
}

onMounted(() => {
  fetchActivities()
})
</script>
