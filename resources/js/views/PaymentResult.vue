<template>
  <div class="payment-result-page max-w-3xl mx-auto p-8 text-center">
    <div v-if="status === 'success'" class="success-container">
      <div class="success-icon mb-6 flex justify-center">
        <BadgeIcon name="check-circle" cls="w-24 h-24 text-green-500" />
      </div>
      <h1 class="text-4xl font-bold text-green-600 mb-4 flex items-center justify-center gap-2">
        <BadgeIcon name="check" cls="w-10 h-10" /> Ödeme Başarılı!
      </h1>
      <p class="text-xl text-gray-700 mb-2">Siparişiniz alındı</p>
      <p class="text-gray-600 mb-8">Sipariş numaranız: <span class="font-bold">#{{ orderId }}</span></p>
      
      <div class="bg-green-50 rounded-lg p-6 mb-8">
        <p class="text-green-800">
          Siparişiniz başarıyla oluşturuldu. Satıcılarımız en kısa sürede siparişlerinizi hazırlamaya başlayacak.
          Sipariş durumunuzu "Siparişlerim" sayfasından takip edebilirsiniz.
        </p>
      </div>

      <div class="space-x-4">
        <button
          @click="goToOrders"
          class="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all"
        >
          Siparişlerimi Görüntüle
        </button>
        <button
          @click="goToHome"
          class="px-8 py-3 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition-all"
        >
          Alışverişe Devam Et
        </button>
      </div>
    </div>

    <div v-else class="failure-container">
      <div class="failure-icon mb-6 flex justify-center">
        <BadgeIcon name="x-circle" cls="w-24 h-24 text-red-500" />
      </div>
      <h1 class="text-4xl font-bold text-red-600 mb-4 flex items-center justify-center gap-2">
        <BadgeIcon name="x" cls="w-10 h-10" /> Ödeme Başarısız
      </h1>
      <p class="text-xl text-gray-700 mb-8">{{ failureMessage }}</p>
      
      <div class="bg-red-50 rounded-lg p-6 mb-8">
        <p class="text-red-800">
          Ödeme işleminiz tamamlanamadı. Lütfen kart bilgilerinizi kontrol edip tekrar deneyin.
          Sorun devam ederse banka kartınızı veren kuruluşla iletişime geçebilirsiniz.
        </p>
      </div>

      <div class="space-x-4">
        <button
          @click="goToCheckout"
          class="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all"
        >
          Tekrar Dene
        </button>
        <button
          @click="goToCart"
          class="px-8 py-3 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition-all"
        >
          Sepete Dön
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const router = useRouter()
const route = useRoute()

const status = ref<'success' | 'failure'>('success')
const orderId = ref<string | null>(null)
const failureMessage = ref('Ödeme işleminiz tamamlanamadı.')

const failureReasons: Record<string, string> = {
  invalid_token: 'Geçersiz ödeme token\'ı',
  order_not_found: 'Sipariş bulunamadı',
  payment_failed: 'Ödeme işlemi başarısız',
  invalid_session: 'Geçersiz ödeme oturumu',
  payment_not_completed: 'Ödeme tamamlanmadı',
  callback_error: 'Ödeme doğrulama hatası',
  user_cancelled: 'Ödeme iptal edildi',
}

function goToOrders() {
  router.push('/orders')
}

function goToHome() {
  router.push('/')
}

function goToCheckout() {
  router.push('/checkout')
}

function goToCart() {
  router.push('/cart')
}

onMounted(() => {
  const path = route.path
  
  if (path.includes('/success')) {
    status.value = 'success'
    orderId.value = route.query.order_id as string || 'Bilinmiyor'
  } else {
    status.value = 'failure'
    const reason = route.query.reason as string
    failureMessage.value = failureReasons[reason] || failureMessage.value
  }
})
</script>

<style scoped>
.success-icon svg {
  animation: scaleIn 0.5s ease-out;
}

.failure-icon svg {
  animation: shake 0.5s ease-out;
}

@keyframes scaleIn {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}

@keyframes shake {
  0%, 100% {
    transform: translateX(0);
  }
  25% {
    transform: translateX(-10px);
  }
  75% {
    transform: translateX(10px);
  }
}
</style>
