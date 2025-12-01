<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-6xl">
      <h1 class="text-3xl font-bold mb-8">Ödeme</h1>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Adres ve Ödeme Bilgileri -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Teslimat Adresi -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Teslimat Adresi</h2>
            <form class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Ad Soyad *
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Telefon *
                </label>
                <input
                  v-model="form.phone"
                  type="tel"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Adres *
                </label>
                <textarea
                  v-model="form.address"
                  rows="3"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                ></textarea>
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    İl *
                  </label>
                  <input
                    v-model="form.city"
                    type="text"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    İlçe *
                  </label>
                  <input
                    v-model="form.district"
                    type="text"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                  />
                </div>
              </div>
            </form>
          </div>

          <!-- Ödeme Bilgileri -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Ödeme Bilgileri</h2>
            <form class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Kart Üzerindeki İsim *
                </label>
                <input
                  v-model="payment.cardName"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Kart Numarası *
                </label>
                <input
                  v-model="payment.cardNumber"
                  type="text"
                  maxlength="19"
                  placeholder="1234 5678 9012 3456"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Son Kullanma Tarihi *
                  </label>
                  <input
                    v-model="payment.expiry"
                    type="text"
                    maxlength="5"
                    placeholder="MM/YY"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    CVV *
                  </label>
                  <input
                    v-model="payment.cvv"
                    type="text"
                    maxlength="3"
                    placeholder="123"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                  />
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Sipariş Özeti -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow p-6 sticky top-4">
            <h2 class="text-xl font-bold mb-4">Sipariş Özeti</h2>
            
            <div class="space-y-3 mb-6 max-h-60 overflow-y-auto">
              <div
                v-for="item in cartItems"
                :key="item.id"
                class="flex justify-between text-sm"
              >
                <span>{{ item.product.name }} (x{{ item.quantity }})</span>
                <span>{{ (item.product.price * item.quantity).toFixed(2) }} TL</span>
              </div>
            </div>

            <div class="space-y-2 pt-4 border-t">
              <div class="flex justify-between">
                <span>Ara Toplam</span>
                <span>{{ subtotal.toFixed(2) }} TL</span>
              </div>
              <div class="flex justify-between">
                <span>Kargo</span>
                <span>{{ shippingCost.toFixed(2) }} TL</span>
              </div>
              <div class="flex justify-between text-lg font-bold pt-2 border-t">
                <span>Toplam</span>
                <span>{{ total.toFixed(2) }} TL</span>
              </div>
            </div>

            <button
              @click="submitOrder"
              :disabled="loading"
              class="w-full mt-6 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-400"
            >
              {{ loading ? 'İşleniyor...' : 'Siparişi Onayla' }}
            </button>

            <p class="text-xs text-gray-500 mt-4 text-center">
              Siparişinizi onaylayarak <a href="#" class="text-blue-600">kullanım koşullarını</a> kabul etmiş olursunuz.
            </p>
          </div>
        </div>
      </div>

      <!-- Başarı Mesajı -->
      <div
        v-if="successMessage"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center flex flex-col items-center">
          <BadgeIcon name="check-circle" cls="w-20 h-20 text-green-600 mb-4" />
          <h2 class="text-2xl font-bold mb-2">Siparişiniz Alındı!</h2>
          <p class="text-gray-600 mb-6">{{ successMessage }}</p>
          <button
            @click="goToOrders"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Siparişlerime Git
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cartStore'
import { useApi } from '@/composables/useApi'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const router = useRouter()
const loading = ref(false)
const successMessage = ref('')
const cartItems = ref([])

const form = ref({
  name: '',
  phone: '',
  address: '',
  city: '',
  district: ''
})

const payment = ref({
  cardName: '',
  cardNumber: '',
  expiry: '',
  cvv: ''
})

const fetchCart = async () => {
  try {
    const { data } = await api.get('/cart')
    cartItems.value = data
  } catch (error) {
    console.error('Sepet yüklenemedi:', error)
    router.push('/cart')
  }
}

const subtotal = computed(() =>
  cartItems.value.reduce((sum, item) => sum + item.product.price * item.quantity, 0)
)

const shippingCost = computed(() => {
  return subtotal.value > 200 ? 0 : 29.90
})

const total = computed(() => subtotal.value + shippingCost.value)

const submitOrder = async () => {
  if (!form.value.name || !form.value.address || !payment.value.cardNumber) {
    alert('Lütfen tüm alanları doldurun!')
    return
  }

  loading.value = true
  try {
    const { data } = await api.post('/checkout', {
      shipping_address: {
        name: form.value.name,
        phone: form.value.phone,
        address: form.value.address,
        city: form.value.city,
        district: form.value.district
      },
      payment_info: {
        card_name: payment.value.cardName,
        card_number: payment.value.cardNumber,
        expiry: payment.value.expiry,
        cvv: payment.value.cvv
      },
      total: total.value
    })

    successMessage.value = `Sipariş numaranız: #${data.order_id}`
  } catch (error) {
    console.error('Sipariş oluşturulamadı:', error)
    alert('Sipariş sırasında bir hata oluştu. Lütfen tekrar deneyin.')
  } finally {
    loading.value = false
  }
}

const goToOrders = () => {
  router.push('/buyer/dashboard')
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
