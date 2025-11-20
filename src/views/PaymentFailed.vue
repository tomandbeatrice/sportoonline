<template>
  <div class="payment-failed min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
      <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <!-- Error Icon -->
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-2">Ödeme Başarısız</h1>
        <p class="text-gray-600 mb-4">
          Ödeme işleminiz tamamlanamadı.
        </p>

        <div v-if="errorMessage" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
          <p class="text-sm text-red-700">{{ errorMessage }}</p>
        </div>

        <div class="space-y-3">
          <router-link
            to="/checkout"
            class="block w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors"
          >
            Tekrar Dene
          </router-link>
          <router-link
            to="/cart"
            class="block w-full py-3 border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-lg transition-colors"
          >
            Sepete Dön
          </router-link>
          <router-link
            to="/"
            class="block w-full py-3 text-gray-600 hover:text-gray-800 font-semibold transition-colors"
          >
            Ana Sayfaya Dön
          </router-link>
        </div>

        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
          <h3 class="font-semibold text-gray-900 mb-2">Yardıma mı ihtiyacınız var?</h3>
          <p class="text-sm text-gray-600 mb-3">
            Sorun devam ederse lütfen müşteri hizmetleriyle iletişime geçin.
          </p>
          <a
            href="mailto:destek@example.com"
            class="text-sm text-blue-500 hover:text-blue-600 font-semibold"
          >
            destek@example.com
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const errorMessage = ref('')

onMounted(() => {
  errorMessage.value = route.query.error || 'Ödeme işlemi sırasında bir hata oluştu. Lütfen tekrar deneyin.'
})
</script>
