<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-violet-700 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl font-bold">Sigorta Hizmetleri</h1>
        <p class="text-purple-100">{{ insurances.length }} plan bulundu</p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
      </div>

      <!-- Insurance Grid -->
      <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="insurance in insurances"
          :key="insurance.id"
          class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow cursor-pointer"
          @click="goToInsurance(insurance.id)"
        >
          <div class="p-6 border-b border-slate-100">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-2xl">
                🛡️
              </div>
              <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-medium">
                {{ insurance.provider }}
              </span>
            </div>
            <h3 class="font-bold text-slate-800 text-lg mb-2">{{ insurance.title }}</h3>
            <div class="text-slate-500 text-sm mb-2">
              <span class="block font-semibold text-purple-600 mb-1">Kapsam: {{ insurance.coverage }}</span>
              <ul class="list-disc list-inside">
                <li v-for="(feature, index) in insurance.features" :key="index" class="truncate">
                  {{ feature }}
                </li>
              </ul>
            </div>
          </div>
          
          <div class="p-4 bg-slate-50">
            <div class="flex items-center justify-between">
              <div>
                <span class="text-xl font-bold text-purple-600">{{ insurance.price }}₺</span>
                <span class="text-sm text-slate-500">/yıl</span>
              </div>
              <button class="text-sm bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Teklif Al
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
import { insuranceService, type Insurance } from '@/services/insuranceService'

const router = useRouter()
const insurances = ref<Insurance[]>([])
const loading = ref(true)

const fetchInsurances = async () => {
  try {
    const response = await insuranceService.getInsuranceOptions()
    insurances.value = response.data.data
  } catch (error) {
    console.error('Sigortalar yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const goToInsurance = (id: number) => {
  router.push(`/insurance/${id}`)
}

onMounted(() => {
  fetchInsurances()
})
</script>
