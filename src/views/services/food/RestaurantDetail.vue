<template>
  <div class="min-h-screen bg-slate-50">
    <ServiceNav />
    
    <!-- Restaurant Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <button @click="goBack" class="flex items-center gap-2 text-orange-100 hover:text-white mb-4">
          ← Geri
        </button>
        <div class="flex items-center gap-4">
          <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center">
            <span class="text-4xl">{{ restaurant.icon }}</span>
          </div>
          <div>
            <h1 class="text-3xl font-bold">{{ restaurant.name }}</h1>
            <p class="text-orange-100">{{ restaurant.cuisine }}</p>
            <div class="flex items-center gap-4 mt-2">
              <span class="flex items-center gap-1">⭐ {{ restaurant.rating }} ({{ restaurant.reviewCount }} değerlendirme)</span>
              <span>🕐 {{ restaurant.deliveryTime }} dk</span>
              <span>💰 Min. {{ restaurant.minOrder }}₺</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Menu -->
        <div class="lg:col-span-2">
          <h2 class="text-xl font-bold text-slate-800 mb-4">Menü</h2>
          
          <div v-for="category in menuCategories" :key="category.name" class="mb-8">
            <h3 class="text-lg font-semibold text-slate-700 mb-3">{{ category.name }}</h3>
            <div class="space-y-3">
              <div
                v-for="item in category.items"
                :key="item.id"
                class="bg-white rounded-xl p-4 flex justify-between items-center hover:shadow-md transition-shadow"
              >
                <div class="flex items-center gap-4">
                  <div class="w-16 h-16 bg-gradient-to-br from-orange-100 to-red-100 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">{{ item.icon }}</span>
                  </div>
                  <div>
                    <h4 class="font-medium text-slate-800">{{ item.name }}</h4>
                    <p class="text-sm text-slate-500">{{ item.description }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="font-bold text-orange-600">{{ item.price }}₺</p>
                  <button 
                    @click="addToCart(item)"
                    class="mt-2 px-4 py-1 bg-orange-500 text-white text-sm rounded-lg hover:bg-orange-600 transition"
                  >
                    + Ekle
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cart Sidebar -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl p-6 shadow-sm sticky top-24">
            <h3 class="text-lg font-bold text-slate-800 mb-4">🛒 Sepetim</h3>
            
            <div v-if="cartStore.items.length === 0" class="text-center py-8 text-slate-400">
              <span class="text-4xl block mb-2">🛒</span>
              <p>Sepetiniz boş</p>
            </div>
            
            <div v-else>
              <div v-for="item in cartStore.items" :key="item.id" class="flex justify-between items-center py-3 border-b border-slate-100">
                <div>
                  <p class="font-medium text-slate-800">{{ item.name }}</p>
                  <p class="text-sm text-slate-500">{{ item.quantity }} adet</p>
                </div>
                <div class="flex items-center gap-3">
                  <span class="font-medium">{{ item.price * item.quantity }}₺</span>
                  <button @click="removeFromCart(item.id)" class="text-red-500 hover:text-red-600">✕</button>
                </div>
              </div>
              
              <div class="mt-4 pt-4 border-t border-slate-200">
                <div class="flex justify-between text-lg font-bold">
                  <span>Toplam</span>
                  <span class="text-orange-600">{{ cartTotal }}₺</span>
                </div>
                <button class="w-full mt-4 bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl font-bold transition">
                  Siparişi Tamamla
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cartStore'
import ServiceNav from '@/components/shared/ServiceNav.vue'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()

const restaurant = ref({
  id: route.params.id,
  name: 'Burger King',
  cuisine: 'Fast Food, Burger',
  icon: '🍔',
  rating: 4.5,
  reviewCount: 1250,
  deliveryTime: 25,
  minOrder: 50
})

const menuCategories = ref([
  {
    name: 'Burgerler',
    items: [
      { id: 1, name: 'Whopper', description: 'Alev ızgarada pişmiş et, domates, marul', icon: '🍔', price: 89 },
      { id: 2, name: 'Double Whopper', description: 'İki kat et, iki kat lezzet', icon: '🍔', price: 119 },
      { id: 3, name: 'Cheese Burger', description: 'Cheddar peynirli klasik', icon: '🧀', price: 59 },
    ]
  },
  {
    name: 'Menüler',
    items: [
      { id: 4, name: 'Whopper Menü', description: 'Whopper + Orta Patates + İçecek', icon: '🍟', price: 149 },
      { id: 5, name: 'King Menü', description: 'Double Whopper + Büyük Patates + İçecek', icon: '🍟', price: 189 },
    ]
  },
  {
    name: 'Tatlılar',
    items: [
      { id: 6, name: 'Sufle', description: 'Sıcak çikolatalı sufle', icon: '🍫', price: 45 },
      { id: 7, name: 'Dondurma', description: 'Vanilya dondurma', icon: '🍦', price: 25 },
    ]
  }
])

const cartTotal = computed(() => cartStore.subtotal)

const addToCart = (item: { id: number; name: string; price: number }) => {
  cartStore.addToCart({
    ...item,
    type: 'product',
    shippingClass: 'instant',
    restaurantName: restaurant.value.name
  })
}

const removeFromCart = (id: number) => {
  cartStore.removeFromCart(id)
}

const goBack = () => {
  router.back()
}
</script>
