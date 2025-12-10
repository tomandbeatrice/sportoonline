<template>
  <div class="flex flex-col items-center justify-center p-8 text-center bg-white rounded-xl shadow-lg max-w-md mx-auto mt-10">
    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
      <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
    </div>
    <h3 class="text-xl font-bold text-gray-900 mb-2">Konum Erişimi Gerekli</h3>
    <p class="text-gray-600 mb-6">
      Size en yakın işletmeleri ve fırsatları gösterebilmemiz için konumunuza erişmemize izin vermeniz gerekiyor.
    </p>
    <button 
      @click="$emit('request')"
      class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
      :disabled="loading"
    >
      <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span>{{ loading ? 'Konum Alınıyor...' : 'Konumumu Paylaş' }}</span>
    </button>
    <p v-if="error" class="mt-4 text-sm text-red-600 bg-red-50 px-4 py-2 rounded-lg">
      {{ error }}
    </p>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  loading: boolean
  error: string | null
}>()

defineEmits(['request'])
</script>
