<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-600 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center gap-2 mb-2">
          <router-link to="/food" class="text-orange-200 hover:text-white">Yemek</router-link>
          <span>›</span>
          <span>{{ categoryName }}</span>
        </div>
        <h1 class="text-2xl font-bold">{{ categoryName }}</h1>
        <p class="text-orange-100">{{ restaurants.length }} restoran bulundu</p>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Filters -->
      <div class="flex flex-wrap gap-3 mb-6">
        <button
          v-for="filter in filters"
          :key="filter.id"
          @click="toggleFilter(filter.id)"
          :class="[
            'px-4 py-2 rounded-full text-sm font-medium transition-all',
            activeFilters.includes(filter.id)
              ? 'bg-orange-600 text-white'
              : 'bg-white text-slate-600 border border-slate-200 hover:border-orange-300'
          ]"
        >
          {{ filter.name }}
        </button>
      </div>

      <!-- Sort Options -->
      <div class="flex items-center gap-4 mb-6">
        <span class="text-slate-500 text-sm">Sırala:</span>
        <select v-model="sortBy" class="bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-600">
          <option value="popular">En Popüler</option>
          <option value="rating">En Yüksek Puan</option>
          <option value="fast">En Hızlı Teslimat</option>
          <option value="cheap">En Uygun Fiyat</option>
        </select>
      </div>

      <!-- Restaurant Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <router-link
          v-for="restaurant in restaurants"
          :key="restaurant.id"
          :to="`/food/restaurant/${restaurant.id}`"
          class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow"
        >
          <div class="relative">
            <img :src="restaurant.image" :alt="restaurant.name" class="w-full h-40 object-cover" />
            <div v-if="restaurant.discount" class="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded-lg text-sm font-bold">
              %{{ restaurant.discount }} İndirim
            </div>
            <div v-if="restaurant.promoted" class="absolute top-3 right-3 bg-orange-500 text-white px-2 py-1 rounded-lg text-sm">
              Öne Çıkan
            </div>
          </div>
          <div class="p-4">
            <h3 class="font-bold text-slate-800 mb-1">{{ restaurant.name }}</h3>
            <p class="text-sm text-slate-500 mb-2">{{ restaurant.cuisine }}</p>
            <div class="flex items-center gap-4 text-sm mb-2">
              <div class="flex items-center gap-1">
                <span class="text-yellow-500">⭐</span>
                <span class="font-medium">{{ restaurant.rating }}</span>
              </div>
              <span class="text-slate-400">•</span>
              <span class="text-slate-500">{{ restaurant.deliveryTime }} dk</span>
              <span class="text-slate-400">•</span>
              <span class="text-slate-500">Min: {{ restaurant.minOrder }}₺</span>
            </div>
            <div class="flex items-center gap-2">
              <span v-if="restaurant.freeDelivery" class="text-green-600 text-sm">🚚 Ücretsiz Teslimat</span>
              <span v-else class="text-slate-400 text-sm">Teslimat: {{ restaurant.deliveryFee }}₺</span>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import ServiceNav from '@/components/shared/ServiceNav.vue'

const route = useRoute()
const categoryId = computed(() => route.params.id as string)

const categoryNames: Record<string, string> = {
  'burger': 'Burger',
  'pizza': 'Pizza',
  'kebap': 'Kebap & Döner',
  'sushi': 'Sushi',
  'cigkofte': 'Çiğ Köfte',
  'dessert': 'Tatlı',
  'coffee': 'Kahve',
  'healthy': 'Sağlıklı'
}

const categoryName = computed(() => categoryNames[categoryId.value] || 'Kategori')

const activeFilters = ref<string[]>([])
const sortBy = ref('popular')

const filters = [
  { id: 'free-delivery', name: 'Ücretsiz Teslimat' },
  { id: 'fast', name: 'Hızlı Teslimat' },
  { id: 'discount', name: 'İndirimli' },
  { id: 'high-rated', name: '4.5+ Puan' },
]

const toggleFilter = (id: string) => {
  const index = activeFilters.value.indexOf(id)
  if (index > -1) {
    activeFilters.value.splice(index, 1)
  } else {
    activeFilters.value.push(id)
  }
}

const restaurants = ref([
  { id: 1, name: 'Burger King', cuisine: 'Fast Food, Burger', image: 'https://placehold.co/400x200/F97316/FFFFFF?text=BK', rating: 4.5, deliveryTime: 25, minOrder: 50, freeDelivery: false, deliveryFee: 12, discount: 20, promoted: true },
  { id: 2, name: "McDonald's", cuisine: 'Fast Food, Burger', image: 'https://placehold.co/400x200/EAB308/FFFFFF?text=MC', rating: 4.3, deliveryTime: 20, minOrder: 40, freeDelivery: true, deliveryFee: 0, discount: null, promoted: false },
  { id: 3, name: 'Shake Shack', cuisine: 'Burger, Amerikan', image: 'https://placehold.co/400x200/22C55E/FFFFFF?text=SS', rating: 4.7, deliveryTime: 35, minOrder: 80, freeDelivery: false, deliveryFee: 15, discount: null, promoted: true },
  { id: 4, name: 'Big Chefs', cuisine: 'Burger, Cafe', image: 'https://placehold.co/400x200/3B82F6/FFFFFF?text=BC', rating: 4.4, deliveryTime: 30, minOrder: 60, freeDelivery: true, deliveryFee: 0, discount: 15, promoted: false },
  { id: 5, name: 'Smashburger', cuisine: 'Burger, Amerikan', image: 'https://placehold.co/400x200/EF4444/FFFFFF?text=SB', rating: 4.6, deliveryTime: 28, minOrder: 70, freeDelivery: false, deliveryFee: 10, discount: null, promoted: false },
  { id: 6, name: "Carl's Jr.", cuisine: 'Fast Food, Burger', image: 'https://placehold.co/400x200/F59E0B/FFFFFF?text=CJ', rating: 4.2, deliveryTime: 22, minOrder: 45, freeDelivery: true, deliveryFee: 0, discount: 10, promoted: false },
])
</script>
