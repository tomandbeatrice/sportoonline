<template>
  <div class="checkout-page min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Sipariş Tamamla</h1>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Steps -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Step 1: Delivery Address -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <span class="bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3">1</span>
                Teslimat Adresi
              </h2>
              <button
                v-if="!showAddressForm"
                @click="showAddressForm = true"
                class="text-blue-500 hover:text-blue-600 text-sm font-semibold"
              >
                + Yeni Adres Ekle
              </button>
            </div>

            <!-- Address Selection -->
            <div v-if="!showAddressForm && addresses.length > 0" class="space-y-3">
              <div
                v-for="address in addresses"
                :key="address.id"
                @click="selectedAddressId = address.id"
                class="border rounded-lg p-4 cursor-pointer transition-all"
                :class="{
                  'border-blue-500 bg-blue-50': selectedAddressId === address.id,
                  'border-gray-200 hover:border-gray-300': selectedAddressId !== address.id
                }"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <h3 class="font-semibold text-gray-900">{{ address.title }}</h3>
                      <span v-if="address.is_default" class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                        Varsayılan
                      </span>
                    </div>
                    <p class="text-gray-700">{{ address.full_name }}</p>
                    <p class="text-gray-600 text-sm">{{ address.phone }}</p>
                    <p class="text-gray-600 text-sm mt-2">{{ address.formatted_address }}</p>
                  </div>
                  <input
                    type="radio"
                    :checked="selectedAddressId === address.id"
                    class="mt-1"
                  />
                </div>
              </div>
            </div>

            <!-- Address Form -->
            <AddressForm
              v-if="showAddressForm || addresses.length === 0"
              @saved="handleAddressSaved"
              @cancel="showAddressForm = false"
            />
          </div>

          <!-- Step 2: Payment Method -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 flex items-center mb-4">
              <span class="bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3">2</span>
              Ödeme Yöntemi
            </h2>

            <div v-if="paymentGateways.length > 0" class="space-y-3">
              <div
                v-for="gateway in paymentGateways"
                :key="gateway.name"
                @click="selectedGateway = gateway.name"
                class="border rounded-lg p-4 cursor-pointer transition-all"
                :class="{
                  'border-blue-500 bg-blue-50': selectedGateway === gateway.name,
                  'border-gray-200 hover:border-gray-300': selectedGateway !== gateway.name
                }"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <h3 class="font-semibold text-gray-900">{{ gateway.display_name }}</h3>
                    <p class="text-sm text-gray-600">{{ gateway.description }}</p>
                    <div v-if="gateway.is_test_mode" class="mt-2">
                      <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded">
                        Test Modu
                      </span>
                    </div>
                  </div>
                  <input
                    type="radio"
                    :checked="selectedGateway === gateway.name"
                  />
                </div>
              </div>
            </div>
            <div v-else class="text-center text-gray-500 py-4">
              Aktif ödeme yöntemi bulunamadı
            </div>
          </div>

          <!-- Step 2.5: Coupon Code -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 flex items-center mb-4">
              <span class="bg-green-500 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3"><BadgeIcon name="ticket" cls="w-5 h-5 text-white" /></span>
              Kupon Kodu
            </h2>

            <div class="flex gap-3">
              <input
                v-model="couponCode"
                type="text"
                :disabled="appliedCoupon !== null"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100"
                placeholder="Kupon kodunuzu girin"
              />
              <button
                v-if="!appliedCoupon"
                @click="applyCoupon"
                :disabled="!couponCode || validatingCoupon"
                class="px-6 py-2 bg-green-500 hover:bg-green-600 disabled:bg-gray-300 text-white font-semibold rounded-lg"
              >
                {{ validatingCoupon ? 'Kontrol...' : 'Uygula' }}
              </button>
              <button
                v-else
                @click="removeCoupon"
                class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg"
              >
                Kaldır
              </button>
            </div>

            <!-- Applied Coupon -->
            <div v-if="appliedCoupon" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
              <div class="flex items-start justify-between">
                <div>
                  <div class="flex items-center gap-2 mb-1">
                    <BadgeIcon name="check" cls="w-5 h-5 text-green-600" />
                    <h3 class="font-bold text-green-900">{{ appliedCoupon.name }}</h3>
                  </div>
                  <p class="text-sm text-green-700">{{ appliedCoupon.description }}</p>
                  <p class="text-sm font-semibold text-green-900 mt-2">
                    İndirim: ₺{{ formatMoney(discountAmount) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Coupon Error -->
            <div v-if="couponError" class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
              {{ couponError }}
            </div>
          </div>

          <!-- Step 3: Order Note (Optional) -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 flex items-center mb-4">
              <span class="bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3">3</span>
              Sipariş Notu (Opsiyonel)
            </h2>
            <textarea
              v-model="orderNote"
              rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Satıcıya iletmek istediğiniz bir not varsa buraya yazabilirsiniz..."
            ></textarea>
          </div>
        </div>

        <!-- Right Column: Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Sipariş Özeti</h2>

            <!-- Cart Items -->
            <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
              <div
                v-for="item in cartItems"
                :key="item.id"
                class="flex items-center gap-3 pb-3 border-b"
              >
                <img
                  :src="item.product.image_url || '/placeholder.jpg'"
                  :alt="item.product.name"
                  class="w-16 h-16 object-cover rounded"
                />
                <div class="flex-1 min-w-0">
                  <h3 class="font-semibold text-sm text-gray-900 truncate">
                    {{ item.product.name }}
                  </h3>
                  <p class="text-xs text-gray-600">{{ item.quantity }} adet</p>
                </div>
                <div class="text-right">
                  <p class="font-semibold text-gray-900">₺{{ formatMoney(item.total_price) }}</p>
                </div>
              </div>
            </div>

            <!-- Price Summary -->
            <div class="space-y-2 border-t pt-4">
              <div class="flex justify-between text-gray-700">
                <span>Ara Toplam</span>
                <span>₺{{ formatMoney(subtotal) }}</span>
              </div>
              <div v-if="discountAmount > 0" class="flex justify-between text-green-600">
                <span>İndirim ({{ appliedCoupon?.coupon_code }})</span>
                <span>-₺{{ formatMoney(discountAmount) }}</span>
              </div>
              <div class="flex justify-between text-gray-700">
                <span>Kargo</span>
                <span class="text-green-600">Ücretsiz</span>
              </div>
              <div class="flex justify-between text-xl font-bold text-gray-900 pt-2 border-t">
                <span>Toplam</span>
                <span>₺{{ formatMoney(total) }}</span>
              </div>
            </div>

            <!-- Complete Order Button -->
            <button
              @click="completeOrder"
              :disabled="!canCompleteOrder || processing"
              class="w-full mt-6 py-3 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-lg transition-colors"
            >
              {{ processing ? 'İşleniyor...' : 'Siparişi Tamamla' }}
            </button>

            <!-- Security Badge -->
            <div class="mt-4 flex items-center justify-center text-sm text-gray-600">
              <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
              </svg>
              Güvenli Ödeme
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AddressForm from '@/components/AddressForm.vue'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const router = useRouter()

const addresses = ref([])
const selectedAddressId = ref(null)
const showAddressForm = ref(false)

const paymentGateways = ref([])
const selectedGateway = ref(null)

const cartItems = ref([])
const orderNote = ref('')
const processing = ref(false)

const couponCode = ref('')
const appliedCoupon = ref(null)
const discountAmount = ref(0)
const validatingCoupon = ref(false)
const couponError = ref('')

const subtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + parseFloat(item.total_price), 0)
})

const total = computed(() => Math.max(0, subtotal.value - discountAmount.value))

const canCompleteOrder = computed(() => {
  return selectedAddressId.value && selectedGateway.value && cartItems.value.length > 0
})

const loadAddresses = async () => {
  try {
    const { data } = await axios.get('/api/addresses')
    addresses.value = data.addresses
    
    // Auto-select default address
    const defaultAddress = addresses.value.find(a => a.is_default)
    if (defaultAddress) {
      selectedAddressId.value = defaultAddress.id
    } else if (addresses.value.length > 0) {
      selectedAddressId.value = addresses.value[0].id
    }
  } catch (error) {
    console.error('Failed to load addresses:', error)
  }
}

const loadPaymentGateways = async () => {
  try {
    const { data } = await axios.get('/api/payment/gateways')
    paymentGateways.value = data.gateways
    
    // Auto-select first gateway
    if (paymentGateways.value.length > 0) {
      selectedGateway.value = paymentGateways.value[0].name
    }
  } catch (error) {
    console.error('Failed to load payment gateways:', error)
  }
}

const loadCart = async () => {
  try {
    const { data } = await axios.get('/api/cart')
    cartItems.value = data.items || []
  } catch (error) {
    console.error('Failed to load cart:', error)
  }
}

const applyCoupon = async () => {
  if (!couponCode.value || validatingCoupon.value) return

  validatingCoupon.value = true
  couponError.value = ''

  try {
    const { data } = await axios.post('/api/validate-coupon', {
      coupon_code: couponCode.value,
      cart_total: subtotal.value,
      product_ids: cartItems.value.map(item => item.product_id)
    })

    if (data.valid) {
      appliedCoupon.value = data.campaign
      discountAmount.value = parseFloat(data.discount_amount)
      couponError.value = ''
    }
  } catch (error) {
    couponError.value = error.response?.data?.error || 'Kupon kodu doğrulanamadı'
    appliedCoupon.value = null
    discountAmount.value = 0
  } finally {
    validatingCoupon.value = false
  }
}

const removeCoupon = () => {
  couponCode.value = ''
  appliedCoupon.value = null
  discountAmount.value = 0
  couponError.value = ''
}

const handleAddressSaved = async (newAddress) => {
  await loadAddresses()
  selectedAddressId.value = newAddress.id
  showAddressForm.value = false
}

const completeOrder = async () => {
  if (!canCompleteOrder.value || processing.value) return

  processing.value = true

  try {
    // Create order
    const orderResponse = await axios.post('/api/orders', {
      address_id: selectedAddressId.value,
      note: orderNote.value,
      campaign_id: appliedCoupon.value?.id || null,
      discount_amount: discountAmount.value || 0,
    })

    const orderId = orderResponse.data.order.id

    // Get selected address for customer data
    const selectedAddress = addresses.value.find(a => a.id === selectedAddressId.value)

    // Initiate payment
    const paymentResponse = await axios.post('/api/payment/initiate', {
      order_id: orderId,
      gateway: selectedGateway.value,
      customer_data: {
        first_name: selectedAddress.first_name,
        last_name: selectedAddress.last_name,
        email: selectedAddress.email || '',
        phone: selectedAddress.phone,
        address: selectedAddress.address,
        city: selectedAddress.city,
        district: selectedAddress.district,
        zip_code: selectedAddress.zip_code,
        country: selectedAddress.country,
        ip: '127.0.0.1', // This should come from backend
      }
    })

    if (paymentResponse.data.success) {
      // Redirect to payment gateway
      window.location.href = paymentResponse.data.data.payment_page_url
    } else {
      alert('Ödeme başlatılamadı: ' + paymentResponse.data.error)
    }
  } catch (error) {
    console.error('Order completion failed:', error)
    alert('Sipariş oluşturulurken hata oluştu')
  } finally {
    processing.value = false
  }
}

const formatMoney = (amount) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

onMounted(() => {
  loadAddresses()
  loadPaymentGateways()
  loadCart()
})
</script>

<style scoped>
/* Custom scrollbar for cart items */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
