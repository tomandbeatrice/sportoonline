<template>
  <div class="buyer-favorites">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Favorilerim</h2>

    <!-- Favorites Grid -->
    <div v-if="favorites.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div
        v-for="favorite in favorites"
        :key="favorite.id"
        class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow group relative"
      >
        <!-- Remove Button -->
        <button
          @click="removeFavorite(favorite.product_id)"
          class="absolute top-2 right-2 z-10 w-8 h-8 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity"
          title="Favorilerden Çıkar"
        >
          <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>

        <!-- Product Image -->
        <router-link :to="`/products/${favorite.product?.id}`" class="block">
          <div class="aspect-square bg-gray-100 overflow-hidden">
            <img
              v-if="favorite.product?.image_url"
              :src="favorite.product.image_url"
              :alt="favorite.product.name"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
              <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
        </router-link>

        <!-- Product Info -->
        <div class="p-4">
          <router-link :to="`/products/${favorite.product?.id}`">
            <h3 class="font-medium text-gray-900 line-clamp-2 mb-2 hover:text-blue-600">
              {{ favorite.product?.name }}
            </h3>
          </router-link>

          <!-- Price & Stock -->
          <div class="flex items-center justify-between mb-3">
            <div class="text-xl font-bold text-blue-600">
              ₺{{ formatMoney(favorite.product?.price) }}
            </div>
            <div v-if="favorite.product?.stock > 0" class="text-xs text-green-600 font-semibold">
              Stokta var
            </div>
            <div v-else class="text-xs text-red-600 font-semibold">
              Tükendi
            </div>
          </div>

          <!-- Actions -->
          <button
            @click="addToCart(favorite.product)"
            :disabled="favorite.product?.stock === 0"
            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-medium text-sm transition-colors"
          >
            {{ favorite.product?.stock === 0 ? 'Stokta Yok' : 'Sepete Ekle' }}
          </button>
        </div>

        <!-- Added Date -->
        <div class="px-4 pb-3 text-xs text-gray-500">
          {{ formatDate(favorite.created_at) }} tarihinde eklendi
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center">
      <div class="text-6xl mb-4">❤️</div>
      <div class="text-xl font-semibold text-gray-900 mb-2">Henüz Favori Ürününüz Yok</div>
      <div class="text-gray-500 mb-6">Beğendiğiniz ürünleri favorilere ekleyerek daha sonra kolayca bulabilirsiniz</div>
      <router-link
        to="/products"
        class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold"
      >
        Ürünleri Keşfet
      </router-link>
    </div>

    <!-- Stats -->
    <div v-if="favorites.length > 0" class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-600 mb-1">Toplam Favori</div>
        <div class="text-3xl font-bold text-gray-900">{{ favorites.length }}</div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-600 mb-1">Stokta Olanlar</div>
        <div class="text-3xl font-bold text-green-600">{{ inStockCount }}</div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-sm text-gray-600 mb-1">Ortalama Fiyat</div>
        <div class="text-3xl font-bold text-blue-600">₺{{ formatMoney(averagePrice) }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const favorites = ref([])

const inStockCount = computed(() => {
  return favorites.value.filter(f => f.product?.stock > 0).length
})

const averagePrice = computed(() => {
  if (favorites.value.length === 0) return 0
  const total = favorites.value.reduce((sum, f) => sum + (f.product?.price || 0), 0)
  return total / favorites.value.length
})

const loadFavorites = async () => {
  try {
    const { data } = await axios.get('/api/buyer/favorites')
    favorites.value = data.favorites || data
  } catch (error) {
    console.error('Favoriler yüklenemedi:', error)
  }
}

const removeFavorite = async (productId) => {
  try {
    await axios.delete(`/api/favorites/${productId}`)
    favorites.value = favorites.value.filter(f => f.product_id !== productId)
  } catch (error) {
    console.error('Favoriden çıkarılamadı:', error)
    alert('İşlem başarısız')
  }
}

const addToCart = async (product) => {
  if (!product || product.stock === 0) return

  try {
    await axios.post('/api/cart', {
      product_id: product.id,
      quantity: 1
    })
    alert(`${product.name} sepete eklendi!`)
  } catch (error) {
    console.error('Sepete eklenemedi:', error)
    alert('Sepete ekleme işlemi başarısız')
  }
}

const formatMoney = (amount) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount || 0)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

onMounted(() => {
  loadFavorites()
})
</script>
