<template>
  <div class="payment-success min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
      <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <!-- Success Icon -->
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-2">Ödeme Başarılı!</h1>
        <p class="text-gray-600 mb-8">
          Siparişiniz başarıyla oluşturuldu. Sipariş detaylarınız email adresinize gönderildi.
        </p>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
          <div class="flex justify-between text-sm mb-2">
            <span class="text-gray-600">Sipariş Numarası:</span>
            <span class="font-semibold text-gray-900">#{{ orderId }}</span>
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
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const orderId = ref(route.query.order_id || '')
const orderTotal = ref(0)

const formatMoney = (amount) => {
  return new Intl.NumberFormat('tr-TR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount)
}

onMounted(() => {
  // You can fetch order details here if needed
  if (route.query.total) {
    orderTotal.value = parseFloat(route.query.total)
  }
})
</script>
