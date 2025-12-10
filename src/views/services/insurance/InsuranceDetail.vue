<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
    </div>

    <div v-else-if="insurance" class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Breadcrumb -->
      <div class="flex items-center text-sm text-slate-500 mb-6">
        <router-link to="/insurance" class="hover:text-purple-600">Sigorta</router-link>
        <span class="mx-2">/</span>
        <span class="text-slate-800 font-medium">{{ insurance.title }}</span>
      </div>

      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white rounded-2xl overflow-hidden shadow-sm p-8">
            <div class="flex items-center gap-4 mb-6">
              <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center text-3xl">
                🛡️
              </div>
              <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ insurance.title }}</h1>
                <p class="text-purple-600 font-medium">{{ insurance.provider }}</p>
              </div>
            </div>

            <div class="prose max-w-none text-slate-600 mb-8">
              <h3 class="text-lg font-bold text-slate-800 mb-2">Poliçe Hakkında</h3>
              <p>{{ insurance.description || 'Bu sigorta paketi için detaylı açıklama bulunmamaktadır.' }}</p>
            </div>

            <div class="bg-purple-50 rounded-xl p-6 mb-8">
              <h3 class="text-lg font-bold text-purple-900 mb-4">Teminat Kapsamı</h3>
              <div class="grid md:grid-cols-2 gap-4">
                <div v-for="(feature, index) in insurance.features" :key="index" class="flex items-center gap-3">
                  <div class="w-6 h-6 rounded-full bg-purple-200 flex items-center justify-center text-purple-700 text-xs">✓</div>
                  <span class="text-slate-700">{{ feature }}</span>
                </div>
              </div>
            </div>

            <div class="border-t border-slate-100 pt-6">
              <h3 class="text-lg font-bold text-slate-800 mb-4">Sıkça Sorulan Sorular</h3>
              <div class="space-y-4">
                <details class="group">
                  <summary class="flex justify-between items-center font-medium cursor-pointer list-none text-slate-700">
                    <span>Poliçe ne zaman başlar?</span>
                    <span class="transition group-open:rotate-180">
                      <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                    </span>
                  </summary>
                  <p class="text-slate-500 mt-3 group-open:animate-fadeIn">
                    Ödeme işleminiz tamamlandıktan hemen sonra poliçeniz aktif hale gelir ve e-posta adresinize gönderilir.
                  </p>
                </details>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24">
            <div class="mb-6">
              <span class="text-3xl font-bold text-purple-600">{{ insurance.price }}₺</span>
              <span class="text-slate-500">/yıllık</span>
            </div>

            <div class="space-y-4 mb-6">
              <div class="flex justify-between items-center py-3 border-b border-slate-100">
                <span class="text-slate-600">Teminat Limiti</span>
                <span class="font-bold text-slate-800">{{ insurance.coverage }}</span>
              </div>
              <div class="flex justify-between items-center py-3 border-b border-slate-100">
                <span class="text-slate-600">Geçerlilik</span>
                <span class="font-bold text-slate-800">1 Yıl</span>
              </div>
            </div>

            <button 
              @click="addToCart"
              class="w-full bg-purple-600 text-white py-3 rounded-xl font-bold hover:bg-purple-700 transition-colors shadow-lg shadow-purple-200"
            >
              Teklif Al & Satın Al
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
import { insuranceService, type Insurance } from '@/services/insuranceService'
import { useCartStore } from '@/stores/cartStore'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()
const toast = useToast()

const insurance = ref<Insurance | null>(null)
const loading = ref(true)

const fetchInsurance = async () => {
  try {
    const id = Number(route.params.id)
    const response = await insuranceService.getInsurance(id)
    insurance.value = response.data.data
  } catch (error) {
    console.error('Sigorta detayları yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const addToCart = () => {
  if (!insurance.value) return

  cartStore.addToCart({
    id: insurance.value.id,
    name: insurance.value.title,
    price: insurance.value.price,
    quantity: 1,
    type: 'service',
    category: { name: 'Sigorta' }
  })

  toast.success('Sigorta paketi sepete eklendi!')
  router.push('/cart')
}

onMounted(() => {
  fetchInsurance()
})
</script>
