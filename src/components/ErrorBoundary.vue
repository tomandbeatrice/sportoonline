<template>
  <div v-if="hasError" class="min-h-screen flex items-center justify-center bg-slate-50">
    <div class="text-center p-8 max-w-md">
      <div class="mb-6">
        <svg class="h-24 w-24 mx-auto text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-slate-900 mb-2">Bir Hata Oluştu</h1>
      <p class="text-slate-600 mb-6">{{ errorMessage }}</p>
      <div class="space-y-3">
        <button 
          @click="handleReset"
          class="w-full px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition"
        >
          Tekrar Dene
        </button>
        <button 
          @click="handleGoHome"
          class="w-full px-6 py-3 border border-slate-200 text-slate-700 rounded-xl font-semibold hover:bg-slate-50 transition"
        >
          Ana Sayfaya Dön
        </button>
      </div>
      <details v-if="errorDetails" class="mt-6 text-left">
        <summary class="text-sm text-slate-500 cursor-pointer hover:text-slate-700">Teknik Detaylar</summary>
        <pre class="mt-2 text-xs bg-slate-100 p-3 rounded overflow-x-auto text-slate-700">{{ errorDetails }}</pre>
      </details>
    </div>
  </div>
  <slot v-else></slot>
</template>

<script setup lang="ts">
import { ref, onErrorCaptured } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const hasError = ref(false)
const errorMessage = ref('Beklenmeyen bir sorun oluştu. Lütfen tekrar deneyin.')
const errorDetails = ref('')

const props = defineProps<{
  error?: Error
}>()

if (props.error) {
  hasError.value = true
  errorMessage.value = props.error.message || errorMessage.value
  errorDetails.value = props.error.stack || ''
}

onErrorCaptured((err) => {
  hasError.value = true
  errorMessage.value = err.message || errorMessage.value
  errorDetails.value = err.stack || ''
  return false
})

const handleReset = () => {
  hasError.value = false
  errorMessage.value = 'Beklenmeyen bir sorun oluştu. Lütfen tekrar deneyin.'
  errorDetails.value = ''
  window.location.reload()
}

const handleGoHome = () => {
  hasError.value = false
  router.push('/')
}
</script>
