<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold">Tüm Restoranlar</h1>
            <p class="text-orange-100">{{ filteredRestaurants.length }} restoran bulundu</p>
          </div>
          <div class="flex gap-3">
            <select v-model="sortBy" class="px-4 py-2 rounded-lg text-slate-800 text-sm">
              <option value="rating">Puana Göre</option>
              <option value="delivery">Teslimat Süresine Göre</option>
              <option value="minOrder">Min. Siparişe Göre</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Filters -->
      <div class="flex flex-wrap gap-2 mb-6">
        <button
          v-for="cuisine in cuisines"
          :key="cuisine"
          @click="toggleCuisine(cuisine)"
          :class="[
            'px-4 py-2 rounded-full text-sm font-medium transition-all',
            selectedCuisines.includes(cuisine)
              ? 'bg-orange-500 text-white'
              : 'bg-white text-slate-600 border border-slate-200 hover:border-orange-300'
          ]"
        >
          {{ cuisine }}
        </button>
      </div>

      <!-- Restaurant Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="restaurant in filteredRestaurants"
          :key="restaurant.id"
          class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow cursor-pointer"
          @click="goToRestaurant(restaurant.id)"
        >
          <div class="relative">
            <div class="w-full h-40 bg-gradient-to-br from-orange-100 to-red-100 flex items-center justify-center">
              <span class="text-5xl">{{ restaurant.icon }}</span>
            </div>
            <div class="absolute top-3 right-3 bg-white px-2 py-1 rounded-lg text-sm font-bold text-orange-600">
              {{ restaurant.deliveryTime }} dk
            </div>
            <div v-if="restaurant.discount" class="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded-lg text-sm font-bold">
              %{{ restaurant.discount }} İndirim
            </div>
          </div>
          <div class="p-4">
            <h3 class="font-bold text-slate-800 mb-1">{{ restaurant.name }}</h3>
            <p class="text-sm text-slate-500 mb-2">{{ restaurant.cuisine }}</p>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-1">
                <span class="text-yellow-500">⭐</span>
                <span class="font-medium">{{ restaurant.rating }}</span>
                <span class="text-slate-400 text-sm">({{ restaurant.reviewCount }})</span>
              </div>
              <span class="text-sm text-slate-500">Min. {{ restaurant.minOrder }}₺</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import ServiceNav from '@/components/shared/ServiceNav.vue'

const router = useRouter()
const route = useRoute()
const sortBy = ref('rating')
const selectedCuisines = ref<string[]>([])
const searchQuery = ref('')

onMounted(() => {
  if (route.query.q) {
    searchQuery.value = route.query.q as string
  }
})

const cuisines = ['Fast Food', 'Türk Mutfağı', 'İtalyan', 'Japon', 'Çin', 'Meksika', 'Tatlı', 'Kahve']

const restaurants = ref([
  { id: 1, name: 'Burger King', cuisine: 'Fast Food, Burger', icon: '🍔', rating: 4.5, reviewCount: 1250, deliveryTime: 25, minOrder: 50, discount: 20 },
  { id: 2, name: 'Pizza Hut', cuisine: 'İtalyan, Pizza', icon: '🍕', rating: 4.3, reviewCount: 890, deliveryTime: 35, minOrder: 60, discount: null },
  { id: 3, name: 'Köfteci Yusuf', cuisine: 'Türk Mutfağı, Köfte', icon: '🍖', rating: 4.7, reviewCount: 2100, deliveryTime: 30, minOrder: 40, discount: 15 },
  { id: 4, name: 'Sushi Master', cuisine: 'Japon, Sushi', icon: '🍣', rating: 4.6, reviewCount: 450, deliveryTime: 40, minOrder: 80, discount: null },
  { id: 5, name: 'Kebapçı Mahmut', cuisine: 'Türk Mutfağı, Kebap', icon: '🥙', rating: 4.8, reviewCount: 1800, deliveryTime: 35, minOrder: 45, discount: 10 },
  { id: 6, name: 'Starbucks', cuisine: 'Kahve, Tatlı', icon: '☕', rating: 4.4, reviewCount: 3200, deliveryTime: 20, minOrder: 30, discount: null },
  { id: 7, name: 'Popeyes', cuisine: 'Fast Food, Tavuk', icon: '🍗', rating: 4.2, reviewCount: 980, deliveryTime: 30, minOrder: 55, discount: 25 },
  { id: 8, name: 'Taco Bell', cuisine: 'Meksika', icon: '🌮', rating: 4.1, reviewCount: 560, deliveryTime: 35, minOrder: 50, discount: null },
  { id: 9, name: 'Pide Salonu', cuisine: 'Türk Mutfağı, Pide', icon: '🫓', rating: 4.5, reviewCount: 780, deliveryTime: 30, minOrder: 35, discount: null },
])

const filteredRestaurants = computed(() => {
  let result = [...restaurants.value]
  
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(r => 
      r.name.toLowerCase().includes(q) || 
      r.cuisine.toLowerCase().includes(q)
    )
  }
  
  if (selectedCuisines.value.length > 0) {
    result = result.filter(r => 
      selectedCuisines.value.some(c => r.cuisine.toLowerCase().includes(c.toLowerCase()))
    )
  }
  
  switch (sortBy.value) {
    case 'rating':
      result.sort((a, b) => b.rating - a.rating)
      break
    case 'delivery':
      result.sort((a, b) => a.deliveryTime - b.deliveryTime)
      break
    case 'minOrder':
      result.sort((a, b) => a.minOrder - b.minOrder)
      break
  }
  
  return result
})

const toggleCuisine = (cuisine: string) => {
  const index = selectedCuisines.value.indexOf(cuisine)
  if (index > -1) {
    selectedCuisines.value.splice(index, 1)
  } else {
    selectedCuisines.value.push(cuisine)
  }
}

const goToRestaurant = (id: number) => {
  router.push(`/food/restaurant/${id}`)
}
</script>
