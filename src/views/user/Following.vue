<template>
  <div class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-5xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Takip Ettiklerim</h1>
        <p class="text-slate-500">{{ followedSellers.length }} satıcı ve {{ followedCategories.length }} kategori takip ediyorsunuz</p>
      </div>

      <!-- Tabs -->
      <div class="bg-white rounded-2xl shadow-sm mb-6 border border-slate-200">
        <div class="border-b border-slate-100 px-6">
          <nav class="flex gap-6">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'py-4 text-sm font-medium border-b-2 transition-colors',
                activeTab === tab.id
                  ? 'border-green-600 text-green-600'
                  : 'border-transparent text-slate-500 hover:text-slate-700'
              ]"
            >
              {{ tab.icon }} {{ tab.name }}
            </button>
          </nav>
        </div>

        <!-- Sellers Tab -->
        <div v-if="activeTab === 'sellers'" class="p-6">
          <div v-if="followedSellers.length === 0" class="text-center py-12">
            <span class="text-5xl mb-4 block">🏪</span>
            <p class="text-slate-500">Henüz takip ettiğiniz satıcı yok</p>
          </div>
          <div v-else class="grid md:grid-cols-2 gap-4">
            <div 
              v-for="seller in followedSellers" 
              :key="seller.id"
              class="bg-white border border-slate-200 rounded-xl p-4 hover:shadow-lg transition"
            >
              <div class="flex items-start gap-4">
                <!-- Logo -->
                <div class="w-16 h-16 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0">
                  <img :src="seller.logo" :alt="seller.name" class="w-full h-full object-cover">
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between mb-2">
                    <h3 class="font-bold text-slate-900 truncate">{{ seller.name }}</h3>
                    <div class="flex items-center gap-1 text-sm">
                      <span class="text-yellow-500">⭐</span>
                      <span class="font-medium">{{ seller.rating }}</span>
                    </div>
                  </div>
                  
                  <div class="flex items-center gap-3 text-xs text-slate-500 mb-3">
                    <span>📦 {{ seller.productCount }} ürün</span>
                    <span>👥 {{ formatNumber(seller.followerCount) }} takipçi</span>
                  </div>

                  <!-- Notification Settings -->
                  <div class="space-y-2 mb-3">
                    <label class="flex items-center gap-2 text-xs cursor-pointer">
                      <input 
                        type="checkbox" 
                        v-model="seller.notifications.newProduct"
                        @change="updateNotification(seller.id, 'newProduct', seller.notifications.newProduct)"
                        class="w-4 h-4 text-green-600 border-slate-300 rounded focus:ring-green-500"
                      >
                      <span class="text-slate-600">Yeni ürün bildirimleri</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs cursor-pointer">
                      <input 
                        type="checkbox" 
                        v-model="seller.notifications.campaign"
                        @change="updateNotification(seller.id, 'campaign', seller.notifications.campaign)"
                        class="w-4 h-4 text-green-600 border-slate-300 rounded focus:ring-green-500"
                      >
                      <span class="text-slate-600">Kampanya bildirimleri</span>
                    </label>
                  </div>

                  <!-- Actions -->
                  <div class="flex gap-2">
                    <router-link 
                      :to="`/seller/${seller.id}`"
                      class="flex-1 py-2 text-center bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700"
                    >
                      Mağazayı Ziyaret Et
                    </router-link>
                    <button 
                      @click="unfollow('seller', seller.id)"
                      class="px-4 py-2 border border-red-200 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50"
                    >
                      Takibi Bırak
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Categories Tab -->
        <div v-if="activeTab === 'categories'" class="p-6">
          <div v-if="followedCategories.length === 0" class="text-center py-12">
            <span class="text-5xl mb-4 block">📁</span>
            <p class="text-slate-500">Henüz takip ettiğiniz kategori yok</p>
          </div>
          <div v-else class="grid md:grid-cols-3 gap-4">
            <div 
              v-for="category in followedCategories" 
              :key="category.id"
              class="bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-lg transition"
            >
              <!-- Image -->
              <div class="aspect-video bg-slate-100 overflow-hidden relative">
                <img :src="category.image" :alt="category.name" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end p-4">
                  <h3 class="font-bold text-white text-lg">{{ category.name }}</h3>
                </div>
              </div>

              <div class="p-4">
                <div class="text-xs text-slate-500 mb-3">
                  {{ category.productCount }} ürün • {{ category.newProducts }} yeni
                </div>

                <!-- Notification Settings -->
                <div class="space-y-2 mb-4">
                  <label class="flex items-center gap-2 text-xs cursor-pointer">
                    <input 
                      type="checkbox" 
                      v-model="category.notifications.newProduct"
                      @change="updateNotification(category.id, 'newProduct', category.notifications.newProduct)"
                      class="w-4 h-4 text-green-600 border-slate-300 rounded focus:ring-green-500"
                    >
                    <span class="text-slate-600">Yeni ürün bildir</span>
                  </label>
                  <label class="flex items-center gap-2 text-xs cursor-pointer">
                    <input 
                      type="checkbox" 
                      v-model="category.notifications.priceChange"
                      @change="updateNotification(category.id, 'priceChange', category.notifications.priceChange)"
                      class="w-4 h-4 text-green-600 border-slate-300 rounded focus:ring-green-500"
                    >
                    <span class="text-slate-600">Fiyat değişikliği</span>
                  </label>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                  <router-link 
                    :to="`/category/${category.id}`"
                    class="flex-1 py-2 text-center bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700"
                  >
                    Ürünleri Gör
                  </router-link>
                  <button 
                    @click="unfollow('category', category.id)"
                    class="px-4 py-2 border border-red-200 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50"
                    title="Takibi Bırak"
                  >
                    🗑️
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Brands Tab -->
        <div v-if="activeTab === 'brands'" class="p-6">
          <div class="text-center py-12">
            <span class="text-5xl mb-4 block">🏷️</span>
            <p class="text-slate-500 mb-4">Marka takibi özelliği yakında!</p>
            <router-link to="/products" class="inline-flex px-6 py-2.5 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700">
              Ürünleri Keşfet
            </router-link>
          </div>
        </div>
      </div>

      <!-- Recommendations -->
      <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
        <h3 class="font-bold text-lg text-slate-900 mb-4">🌟 Önerilen Satıcılar</h3>
        <div class="grid md:grid-cols-4 gap-4">
          <div 
            v-for="seller in recommendedSellers" 
            :key="seller.id"
            class="border border-slate-200 rounded-xl p-4 hover:border-green-500 hover:shadow-md transition text-center"
          >
            <div class="w-16 h-16 bg-slate-100 rounded-full mx-auto mb-3 overflow-hidden">
              <img :src="seller.logo" :alt="seller.name" class="w-full h-full object-cover">
            </div>
            <h4 class="font-semibold text-slate-900 text-sm mb-1 truncate">{{ seller.name }}</h4>
            <div class="flex items-center justify-center gap-1 text-xs text-slate-500 mb-3">
              <span class="text-yellow-500">⭐</span>
              <span>{{ seller.rating }}</span>
            </div>
            <button 
              @click="followSeller(seller)"
              class="w-full py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700"
            >
              + Takip Et
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const activeTab = ref('sellers')

const tabs = [
  { id: 'sellers', name: 'Satıcılar', icon: '🏪' },
  { id: 'categories', name: 'Kategoriler', icon: '📁' },
  { id: 'brands', name: 'Markalar', icon: '🏷️' }
]

const followedSellers = ref([
  {
    id: 1,
    name: 'Spor Dünyası',
    logo: 'https://images.unsplash.com/photo-1556740738-b6a63e27c4df?w=100',
    rating: 4.8,
    productCount: 245,
    followerCount: 15200,
    notifications: {
      newProduct: true,
      campaign: true
    }
  },
  {
    id: 2,
    name: 'Teknoloji Market',
    logo: 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?w=100',
    rating: 4.6,
    productCount: 532,
    followerCount: 28400,
    notifications: {
      newProduct: false,
      campaign: true
    }
  },
  {
    id: 3,
    name: 'Moda Butik',
    logo: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=100',
    rating: 4.9,
    productCount: 189,
    followerCount: 9850,
    notifications: {
      newProduct: true,
      campaign: false
    }
  }
])

const followedCategories = ref([
  {
    id: 1,
    name: 'Spor Ayakkabı',
    image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400',
    productCount: 1245,
    newProducts: 23,
    notifications: {
      newProduct: true,
      priceChange: false
    }
  },
  {
    id: 2,
    name: 'Elektronik',
    image: 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400',
    productCount: 3421,
    newProducts: 87,
    notifications: {
      newProduct: true,
      priceChange: true
    }
  },
  {
    id: 3,
    name: 'Giyim',
    image: 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=400',
    productCount: 2156,
    newProducts: 45,
    notifications: {
      newProduct: false,
      priceChange: true
    }
  }
])

const recommendedSellers = ref([
  { id: 101, name: 'Kitap Evi', logo: 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=100', rating: 4.7 },
  { id: 102, name: 'Ev Dekoru', logo: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=100', rating: 4.5 },
  { id: 103, name: 'Oyuncak Dünyası', logo: 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=100', rating: 4.8 },
  { id: 104, name: 'Kozmetik Store', logo: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=100', rating: 4.6 }
])

const formatNumber = (num: number) => {
  if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K'
  }
  return num.toString()
}

const updateNotification = (id: number, type: string, enabled: boolean) => {
  const text = enabled ? 'açıldı' : 'kapatıldı'
  toast.success(`Bildirim ${text}`)
}

const unfollow = (type: string, id: number) => {
  if (type === 'seller') {
    followedSellers.value = followedSellers.value.filter(s => s.id !== id)
    toast.success('Satıcı takipten çıkarıldı')
  } else if (type === 'category') {
    followedCategories.value = followedCategories.value.filter(c => c.id !== id)
    toast.success('Kategori takipten çıkarıldı')
  }
}

const followSeller = (seller: any) => {
  toast.success(`${seller.name} takip edildi!`)
}
</script>
