<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h1 class="text-2xl font-bold">Tüm Hizmetler</h1>
        <p class="text-purple-100">{{ filteredServices.length }} hizmet bulundu</p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Category Filters -->
      <div class="flex flex-wrap gap-2 mb-6">
        <button
          v-for="category in categories"
          :key="category.id"
          @click="selectedCategory = selectedCategory === category.id ? null : category.id"
          :class="[
            'px-4 py-2 rounded-full text-sm font-medium transition-all flex items-center gap-2',
            selectedCategory === category.id
              ? 'bg-purple-600 text-white'
              : 'bg-white text-slate-600 border border-slate-200 hover:border-purple-300'
          ]"
        >
          <span>{{ category.icon }}</span>
          <span>{{ category.name }}</span>
        </button>
      </div>

      <!-- Services Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="service in filteredServices"
          :key="service.id"
          class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow"
        >
          <div class="relative">
            <div class="w-full h-40 bg-gradient-to-br flex items-center justify-center" :class="service.gradient">
              <span class="text-5xl">{{ service.icon }}</span>
            </div>
            <div class="absolute top-3 left-3 bg-white px-3 py-1 rounded-lg text-sm">
              <span class="font-bold text-purple-600">{{ service.category }}</span>
            </div>
          </div>
          <div class="p-4">
            <h3 class="font-bold text-slate-800 mb-2">{{ service.name }}</h3>
            <p class="text-sm text-slate-500 mb-3">{{ service.description }}</p>
            <div class="flex items-center justify-between mb-4">
              <div>
                <span class="text-purple-600 font-bold text-lg">{{ service.price }}₺</span>
                <span class="text-slate-400 text-sm">/saat</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="text-yellow-500">⭐</span>
                <span class="font-medium">{{ service.rating }}</span>
                <span class="text-slate-400 text-sm">({{ service.reviewCount }})</span>
              </div>
            </div>
            <button 
              @click="bookService(service)"
              class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg font-medium transition-colors"
            >
              Hizmet Al
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import ServiceNav from '@/components/shared/ServiceNav.vue'
import { useCartStore } from '@/stores/cartStore'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const cartStore = useCartStore()
const selectedCategory = ref<string | null>(null)

onMounted(() => {
  if (route.query.category) {
    selectedCategory.value = route.query.category as string
  }
})

const categories = [
  { id: 'cleaning', name: 'Temizlik', icon: '🧹' },
  { id: 'repair', name: 'Tamir', icon: '🔧' },
  { id: 'plumbing', name: 'Tesisatçı', icon: '🚿' },
  { id: 'electric', name: 'Elektrikçi', icon: '⚡' },
  { id: 'moving', name: 'Nakliyat', icon: '📦' },
  { id: 'beauty', name: 'Güzellik', icon: '💅' },
]

const services = ref([
  { id: 1, name: 'Ev Temizliği', category: 'Temizlik', categoryId: 'cleaning', description: 'Detaylı ev temizliği, cam silme dahil', icon: '🧹', gradient: 'from-cyan-100 to-blue-200', price: 350, rating: 4.8, reviewCount: 520 },
  { id: 2, name: 'Ofis Temizliği', category: 'Temizlik', categoryId: 'cleaning', description: 'Profesyonel ofis temizliği', icon: '🏢', gradient: 'from-cyan-100 to-blue-200', price: 500, rating: 4.7, reviewCount: 280 },
  { id: 3, name: 'Klima Bakımı', category: 'Tamir', categoryId: 'repair', description: 'Klima temizlik ve bakım hizmeti', icon: '❄️', gradient: 'from-blue-100 to-indigo-200', price: 250, rating: 4.6, reviewCount: 180 },
  { id: 4, name: 'Boya Badana', category: 'Tadilat', categoryId: 'repair', description: 'Profesyonel iç cephe boyama', icon: '🎨', gradient: 'from-orange-100 to-red-200', price: 150, rating: 4.7, reviewCount: 340 },
  { id: 5, name: 'Tesisat Tamiri', category: 'Tesisatçı', categoryId: 'plumbing', description: 'Tıkanıklık açma, musluk tamiri', icon: '🚿', gradient: 'from-blue-100 to-cyan-200', price: 200, rating: 4.5, reviewCount: 420 },
  { id: 6, name: 'Elektrik Arıza', category: 'Elektrikçi', categoryId: 'electric', description: 'Elektrik arıza tespiti ve onarım', icon: '⚡', gradient: 'from-yellow-100 to-orange-200', price: 180, rating: 4.6, reviewCount: 310 },
  { id: 7, name: 'Evden Eve Nakliyat', category: 'Nakliyat', categoryId: 'moving', description: 'Profesyonel taşıma hizmeti', icon: '📦', gradient: 'from-amber-100 to-orange-200', price: 1500, rating: 4.4, reviewCount: 890 },
  { id: 8, name: 'Manikür Pedikür', category: 'Güzellik', categoryId: 'beauty', description: 'Evde profesyonel bakım', icon: '💅', gradient: 'from-pink-100 to-rose-200', price: 150, rating: 4.9, reviewCount: 620 },
])

const filteredServices = computed(() => {
  let result = services.value
  
  if (selectedCategory.value) {
    result = result.filter(s => s.categoryId === selectedCategory.value)
  }
  
  if (route.query.q) {
    const q = (route.query.q as string).toLowerCase()
    result = result.filter(s => 
      s.name.toLowerCase().includes(q) || 
      s.description.toLowerCase().includes(q)
    )
  }
  

  return result
})

const bookService = (service: any) => {
  cartStore.addToCart({
    id: service.id,
    name: service.name,
    price: service.price,
    quantity: 1,
    type: 'service',
    image: service.icon,
    category: { name: service.category }
  })
  
  toast.success(`${service.name} hizmeti sepete eklendi!`)
  router.push('/cart')
}
</script>
