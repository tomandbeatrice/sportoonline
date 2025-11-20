<template>
  <div class="cart-page max-w-7xl mx-auto p-8">
    <h1 class="text-3xl font-bold mb-8">Sepetim</h1>

    <!-- Yükleniyor -->
    <div v-if="loading" class="text-center py-20">
      <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">Sepet yükleniyor...</p>
    </div>

    <!-- Boş Sepet -->
    <div v-else-if="cartItems.length === 0" class="text-center py-20">
      <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
      </svg>
      <p class="text-xl text-gray-600 mb-4">Sepetiniz boş</p>
      <router-link to="/products" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 inline-block">
        Alışverişe Başla
      </router-link>
    </div>

    <!-- Dolu Sepet -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Sol Taraf - Sepet Ürünleri -->
      <div class="lg:col-span-2 space-y-4">
        <div
          v-for="item in cartItems"
          :key="item.id"
          class="bg-white rounded-lg shadow p-6 flex items-center space-x-4"
        >
          <img
            :src="item.product.image || '/placeholder.png'"
            :alt="item.product.name"
            class="w-24 h-24 object-cover rounded"
          />
          <div class="flex-1">
            <h3 class="font-semibold text-lg">{{ item.product.name }}</h3>
            <p class="text-sm text-gray-600">{{ item.product.description }}</p>
            <p v-if="item.product.seller" class="text-xs text-gray-500 mt-1">
              🏪 {{ item.product.seller.name }}
            </p>
          </div>
          <div class="text-center">
            <div class="flex items-center space-x-2 mb-2">
              <button
                @click="updateQuantity(item.id, item.quantity - 1)"
                class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100"
                :disabled="item.quantity <= 1"
              >
                -
              </button>
              <span class="w-12 text-center font-semibold">{{ item.quantity }}</span>
              <button
                @click="updateQuantity(item.id, item.quantity + 1)"
                class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100"
              >
                +
              </button>
            </div>
            <p class="text-lg font-bold text-blue-600">{{ formatPrice(item.product.price * item.quantity) }} ₺</p>
            <p class="text-sm text-gray-500">{{ formatPrice(item.product.price) }} ₺ / adet</p>
          </div>
          <button
            @click="removeItem(item.id)"
            class="text-red-600 hover:text-red-800"
            title="Sepetten Çıkar"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Sağ Taraf - Sipariş Özeti -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6 sticky top-8">
          <h2 class="text-xl font-semibold mb-4">Sipariş Özeti</h2>
          
          <div class="space-y-3 mb-6">
            <div class="flex justify-between">
              <span class="text-gray-600">Ara Toplam</span>
              <span class="font-semibold">{{ formatPrice(subtotal) }} ₺</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Kargo</span>
              <span class="text-green-600 font-semibold">Ücretsiz</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">KDV</span>
              <span class="text-gray-600">Dahil</span>
            </div>
            <div class="pt-3 border-t flex justify-between text-lg">
              <span class="font-bold">Toplam</span>
              <span class="font-bold text-blue-600">{{ formatPrice(total) }} ₺</span>
            </div>
          </div>

          <button
            @click="goToCheckout"
            class="w-full py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors mb-3"
          >
            Ödemeye Geç
          </button>

          <button
            @click="$router.push('/products')"
            class="w-full py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
          >
            Alışverişe Devam Et
          </button>

          <div class="mt-4 text-center text-xs text-gray-500">
            🔒 Güvenli alışveriş
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

interface CartItem {
  id: number
  product: {
    name: string
    description: string
    image?: string
    price: number
    seller?: {
      name: string
    }
  }
  quantity: number
}

const router = useRouter()
const cartItems = ref<CartItem[]>([])
const loading = ref(true)

const subtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.product.price * item.quantity), 0)
})

const total = computed(() => subtotal.value)

function formatPrice(price: number) {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price)
}

async function loadCart() {
  loading.value = true
  try {
    const response = await axios.get('/api/cart')
    cartItems.value = response.data
  } catch (error) {
    console.error('Sepet yüklenirken hata:', error)
    alert('Sepet yüklenemedi')
  } finally {
    loading.value = false
  }
}

async function updateQuantity(itemId: number, newQuantity: number) {
  if (newQuantity < 1) return
  
  try {
    await axios.put(`/api/cart/${itemId}`, { quantity: newQuantity })
    await loadCart()
  } catch (error) {
    console.error('Miktar güncellenirken hata:', error)
    alert('Miktar güncellenemedi')
  }
}

async function removeItem(itemId: number) {
  if (!confirm('Bu ürünü sepetten çıkarmak istediğinizden emin misiniz?')) return
  
  try {
    await axios.delete(`/api/cart/${itemId}`)
    await loadCart()
  } catch (error) {
    console.error('Ürün silinirken hata:', error)
    alert('Ürün silinemedi')
  }
}

function goToCheckout() {
  if (cartItems.value.length === 0) {
    alert('Sepetiniz boş')
    return
  }
  router.push('/checkout')
}

onMounted(() => {
  loadCart()
})
</script>

<style scoped>
/* İsteğe bağlı özel stiller */
</style>