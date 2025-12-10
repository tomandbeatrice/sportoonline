<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Service Navigation -->
    <ServiceNav />
    
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center gap-4 mb-4">
          <span class="text-5xl">🍔</span>
          <div>
            <h1 class="text-3xl font-bold">Yemek Siparişi</h1>
            <p class="text-orange-100">En sevdiğin restoranlardan kapına gelsin</p>
          </div>
        </div>
        
        <!-- Search Bar -->
        <div class="mt-6 max-w-2xl">
          <div class="flex bg-white rounded-xl overflow-hidden shadow-lg">
            <input
              v-model="searchQuery"
              @keyup.enter="handleSearch"
              type="text"
              placeholder="Restoran veya yemek ara..."
              class="flex-1 px-4 py-3 text-slate-800 focus:outline-none"
            />
            <button @click="handleSearch" class="bg-orange-600 hover:bg-orange-700 px-6 py-3 font-medium transition-colors">
              Ara
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <!-- Categories -->
      <section class="mb-10">
        <h2 class="text-xl font-bold text-slate-800 mb-4">Kategoriler</h2>
        <div class="grid grid-cols-4 md:grid-cols-8 gap-4">
          <button
            v-for="category in categories"
            :key="category.id"
            @click="selectCategory(category.id)"
            :class="[
              'flex flex-col items-center p-4 rounded-xl transition-all',
              selectedCategory === category.id 
                ? 'bg-orange-100 border-2 border-orange-500' 
                : 'bg-white border-2 border-slate-100 hover:border-orange-200 shadow-sm'
            ]"
          >
            <span class="text-3xl mb-2">{{ category.icon }}</span>
            <span class="text-sm font-medium text-slate-700">{{ category.name }}</span>
          </button>
        </div>
      </section>

      <!-- Featured Restaurants -->
      <section class="mb-10">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-slate-800">Öne Çıkan Restoranlar</h2>
          <router-link to="/food/restaurants" class="text-orange-600 hover:text-orange-700 font-medium">
            Tümünü Gör →
          </router-link>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="restaurant in featuredRestaurants"
            :key="restaurant.id"
            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow cursor-pointer"
            @click="goToRestaurant(restaurant.id)"
          >
            <div class="relative">
              <div class="w-full h-40 bg-gradient-to-br from-orange-100 to-red-100 flex items-center justify-center">
                <span class="text-6xl">🍽️</span>
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
      </section>

      <!-- Group Order CTA -->
      <section class="mb-10">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-6 text-white">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-2xl font-bold mb-2">👥 Grup Siparişi</h3>
              <p class="text-purple-100 mb-4">Arkadaşlarınla birlikte sipariş ver, teslimat ücretini paylaş!</p>
              <router-link
                to="/food/group-order"
                class="inline-block bg-white text-purple-600 px-6 py-2 rounded-lg font-bold hover:bg-purple-50 transition-colors"
              >
                Grup Oluştur
              </router-link>
            </div>
            <span class="text-6xl hidden md:block">🎉</span>
          </div>
        </div>
      </section>

      <!-- Popular Dishes -->
      <section>
        <h2 class="text-xl font-bold text-slate-800 mb-4">Popüler Yemekler</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <div
            v-for="dish in popularDishes"
            :key="dish.id"
            class="bg-white rounded-xl p-4 text-center hover:shadow-md transition-shadow cursor-pointer"
          >
            <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-orange-100 to-yellow-100 flex items-center justify-center mb-3">
              <span class="text-3xl">🍕</span>
            </div>
            <h4 class="font-medium text-slate-800 text-sm">{{ dish.name }}</h4>
            <p class="text-orange-600 font-bold mt-1">{{ dish.price }}₺</p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import ServiceNav from '@/components/shared/ServiceNav.vue'

const router = useRouter()
const searchQuery = ref('')
const selectedCategory = ref<string | null>(null)

const categories = [
  { id: 'burger', name: 'Burger', icon: '🍔' },
  { id: 'pizza', name: 'Pizza', icon: '🍕' },
  { id: 'kebab', name: 'Kebap', icon: '🥙' },
  { id: 'sushi', name: 'Sushi', icon: '🍣' },
  { id: 'dessert', name: 'Tatlı', icon: '🍰' },
  { id: 'coffee', name: 'Kahve', icon: '☕' },
  { id: 'healthy', name: 'Sağlıklı', icon: '🥗' },
  { id: 'turkish', name: 'Türk Mutfağı', icon: '🍲' },
]

const featuredRestaurants = ref([
  {
    id: 1,
    name: 'Burger King',
    cuisine: 'Fast Food, Burger',
    image: '/images/restaurants/burger-king.jpg',
    rating: 4.5,
    reviewCount: 1250,
    deliveryTime: 25,
    minOrder: 50,
    discount: 20
  },
  {
    id: 2,
    name: 'Pizza Hut',
    cuisine: 'İtalyan, Pizza',
    image: '/images/restaurants/pizza-hut.jpg',
    rating: 4.3,
    reviewCount: 890,
    deliveryTime: 35,
    minOrder: 60,
    discount: null
  },
  {
    id: 3,
    name: 'Köfteci Yusuf',
    cuisine: 'Türk Mutfağı, Köfte',
    image: '/images/restaurants/kofteci.jpg',
    rating: 4.7,
    reviewCount: 2100,
    deliveryTime: 30,
    minOrder: 40,
    discount: 15
  },
])

const popularDishes = ref([
  { id: 1, name: 'Whopper', image: '/images/dishes/whopper.jpg', price: 89 },
  { id: 2, name: 'Margherita', image: '/images/dishes/margherita.jpg', price: 120 },
  { id: 3, name: 'Adana Kebap', image: '/images/dishes/adana.jpg', price: 150 },
  { id: 4, name: 'Künefe', image: '/images/dishes/kunefe.jpg', price: 65 },
  { id: 5, name: 'Latte', image: '/images/dishes/latte.jpg', price: 45 },
  { id: 6, name: 'Salata', image: '/images/dishes/salad.jpg', price: 55 },
])

const selectCategory = (categoryId: string) => {
  selectedCategory.value = categoryId
  router.push(`/food/category/${categoryId}`)
}

const goToRestaurant = (restaurantId: number) => {
  router.push(`/food/restaurant/${restaurantId}`)
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ path: '/food/restaurants', query: { q: searchQuery.value } })
  }
}
</script>
