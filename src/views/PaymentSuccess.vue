<template>
  <div class="payment-success min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-2xl w-full space-y-6">
      <!-- Success Message -->
      <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <!-- Success Icon -->
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-2">Ödeme Başarılı!</h1>
        <p class="text-gray-600 mb-8">
          {{ successMessage }}
        </p>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
          <div class="flex justify-between text-sm mb-2">
            <span class="text-gray-600">Sipariş Numarası:</span>
            <span class="font-semibold text-gray-900">#{{ orderId }}</span>
          </div>
          <div v-if="orderType" class="flex justify-between text-sm mb-2">
            <span class="text-gray-600">Sipariş Tipi:</span>
            <span class="font-semibold text-gray-900">{{ orderTypeLabel }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Toplam Tutar:</span>
            <span class="font-bold text-gray-900">₺{{ formatMoney(orderTotal) }}</span>
          </div>
        </div>

        <div class="space-y-3">
          <router-link
            :to="`/orders/${orderId}`"
            class="block w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors"
          >
            Siparişi Görüntüle
          </router-link>
          <router-link
            to="/"
            class="block w-full py-3 border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-lg transition-colors"
          >
            Alışverişe Devam Et
          </router-link>
        </div>

        <p class="text-xs text-gray-500 mt-6">
          Siparişinizle ilgili güncellemeler için email ve bildirimlerinizi kontrol edin.
        </p>
      </div>

      <!-- Cross-Sell Card for Hotel Bookings -->
      <CrossSellCard
        v-if="isHotelBooking && showCrossSell"
        :booking-id="orderId"
        :hotel-location="hotelLocation"
        @add-transfer="handleAddTransfer"
        @view-options="handleViewTransferOptions"
        @dismiss="handleDismissCrossSell"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import CrossSellCard from '@/components/marketplace/CrossSellCard.vue'

const route = useRoute()
const router = useRouter()

const orderId = ref(route.query.order_id || '')
const orderTotal = ref(0)
const orderType = ref(route.query.type || '') // 'hotel', 'food', 'product', etc.
const hotelLocation = ref(route.query.location || '')
const showCrossSell = ref(true)

const isHotelBooking = computed(() => {
  return ['hotel', 'accommodation'].includes(orderType.value)
})

const orderTypeLabel = computed(() => {
  const types = {
    hotel: 'Otel Rezervasyonu',
    accommodation: 'Konaklama',
    food: 'Yemek Siparişi',
    ride: 'Ulaşım',
    product: 'Ürün',
  }
  return types[orderType.value] || 'Genel'
})

const successMessage = computed(() => {
  if (isHotelBooking.value) {
    return 'Otel rezervasyonunuz başarıyla oluşturuldu. Rezervasyon detaylarınız email adresinize gönderildi.'
  }
  return 'Siparişiniz başarıyla oluşturuldu. Sipariş detaylarınız email adresinize gönderildi.'
})

const formatMoney = (amount) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

const handleAddTransfer = async (option) => {
  try {
    // Add transfer to order via API
    await axios.post(`/api/orders/${orderId.value}/add-transfer`, {
      transfer_type: option.type,
      pickup_location: option.from,
      dropoff_location: option.to
    })
    
    console.log('Transfer added to order:', orderId.value, option)
    
    // Navigate to rides/transfer page with pre-filled data
    router.push({
      path: '/rides',
      query: {
        from: 'airport',
        to: hotelLocation.value,
        booking_ref: orderId.value,
        service_type: option?.id || 1
      }
    })
  } catch (error) {
    console.error('Failed to add transfer:', error)
    alert('Transfer eklenirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.')
  }
}

const handleViewTransferOptions = () => {
  // Navigate to transfer options page
  router.push({
    path: '/rides',
    query: {
      booking_ref: orderId.value,
      view: 'options'
    }
  })
}

const handleDismissCrossSell = () => {
  showCrossSell.value = false
  
  // Save dismissal to localStorage to avoid showing again for this booking
  try {
    const dismissed = JSON.parse(localStorage.getItem('crossSellDismissed') || '[]')
    
    // Limit the array size to prevent unbounded growth
    const maxDismissedItems = 100
    if (dismissed.length >= maxDismissedItems) {
      dismissed.shift() // Remove oldest item
    }
    
    dismissed.push(orderId.value)
    localStorage.setItem('crossSellDismissed', JSON.stringify(dismissed))
  } catch (error) {
    console.error('Failed to save dismissal preference:', error)
  }
}

onMounted(() => {
  // Fetch order details if needed
  if (route.query.total) {
    orderTotal.value = parseFloat(route.query.total)
  }
  
  // Check if cross-sell was already dismissed for this order
  try {
    const dismissed = JSON.parse(localStorage.getItem('crossSellDismissed') || '[]')
    if (dismissed.includes(orderId.value)) {
      showCrossSell.value = false
    }
  } catch (error) {
    console.error('Failed to read dismissal preference:', error)
  }
})
</script>
