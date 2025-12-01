<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
      <h1 class="text-3xl font-bold mb-8">🛒 Sepetim</h1>

      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-500">Sepet yükleniyor...</p>
      </div>

      <div v-else-if="cart.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
        <p class="text-xl text-gray-500 mb-4">Sepetiniz boş</p>
        <router-link
          to="/"
          class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Alışverişe Başla
        </router-link>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sepet Ürünleri -->
        <div class="lg:col-span-2 space-y-4">
          <div
            v-for="item in cart"
            :key="item.id"
            class="bg-white rounded-lg shadow p-4"
          >
            <div class="flex items-center space-x-4">
              <img
                :src="item.product.image_url"
                :alt="item.product.name"
                class="w-24 h-24 object-cover rounded"
              />
              <div class="flex-1">
                <h3 class="font-semibold text-lg">{{ item.product.name }}</h3>
                <p class="text-gray-600">{{ item.product.price }} TL</p>
              </div>
              <div class="flex items-center space-x-2">
                <button
                  @click="updateQuantity(item.id, item.quantity - 1)"
                  :disabled="item.quantity <= 1"
                  class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50"
                >
                  -
                </button>
                <span class="px-4 py-1 border rounded">{{ item.quantity }}</span>
                <button
                  @click="updateQuantity(item.id, item.quantity + 1)"
                  class="px-3 py-1 border rounded hover:bg-gray-100"
                >
                  +
                </button>
              </div>
              <div class="text-right">
                <p class="font-bold text-lg">
                  {{ (item.product.price * item.quantity).toFixed(2) }} TL
                </p>
                <button
                  @click="removeFromCart(item.id)"
                  class="text-red-600 text-sm hover:underline mt-2"
                >
                  Sil
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Sipariş Özeti -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow p-6 sticky top-4">
            <h2 class="text-xl font-bold mb-4">Sipariş Özeti</h2>
            <div class="space-y-3 mb-6">
              <div class="flex justify-between">
                <span>Ara Toplam</span>
                <span>{{ subtotal.toFixed(2) }} TL</span>
              </div>
              <div class="flex justify-between">
                <span>Kargo</span>
                <span>{{ shippingCost.toFixed(2) }} TL</span>
              </div>
              <div class="flex justify-between text-lg font-bold pt-3 border-t">
                <span>Toplam</span>
                <span>{{ total.toFixed(2) }} TL</span>
              </div>
            </div>
            <button
              @click="goToCheckout"
              :disabled="checkoutLoading"
              class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400"
            >
              {{ checkoutLoading ? 'İşleniyor...' : 'Siparişi Tamamla' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const cart = ref([])
const loading = ref(false)
const checkoutLoading = ref(false)

const fetchCart = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/cart')
    cart.value = data
  } catch (error) {
    console.error('Sepet yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

const updateQuantity = async (itemId, newQuantity) => {
  if (newQuantity < 1) return

  try {
    await api.put('/cart', {
      item_id: itemId,
      quantity: newQuantity
    })
    await fetchCart()
  } catch (error) {
    console.error('Miktar güncellenemedi:', error)
  }
}

const removeFromCart = async (itemId) => {
  if (!confirm('Bu ürünü sepetten kaldırmak istediğinizden emin misiniz?')) return

  try {
    await api.delete('/cart', { data: { item_id: itemId } })
    await fetchCart()
  } catch (error) {
    console.error('Ürün silinemedi:', error)
  }
}

const subtotal = computed(() =>
  cart.value.reduce((sum, item) => sum + item.product.price * item.quantity, 0)
)

const shippingCost = computed(() => {
  return subtotal.value > 200 ? 0 : 29.90
})

const total = computed(() => subtotal.value + shippingCost.value)

const goToCheckout = () => {
  checkoutLoading.value = true
  router.push('/checkout')
}

onMounted(() => {
  fetchCart()
})
</script>

<style scoped>
.container {
  max-width: 1200px;
}
</style>

</script>

<style scoped>
.cart {
  padding: 2rem;
  background-color: var(--card);
  border-radius: 8px;
  box-shadow: 0 0 8px rgba(0,0,0,0.05);
}

h2 {
  color: var(--accent);
  margin-bottom: 1rem;
}

ul {
  list-style: none;
  padding: 0;
  margin-bottom: 1rem;
}

li {
  margin-bottom: 0.5rem;
  font-weight: 500;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.subtotal {
  font-weight: 400;
  color: var(--text-muted);
}

.summary {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1rem;
}

button {
  background-color: var(--accent);
  color: white;
  border: none;
  padding: 0.6rem 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

button:hover:not(:disabled) {
  background-color: #2e8b57;
}

.empty {
  font-style: italic;
  color: var(--text-muted);
}
</style>