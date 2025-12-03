<template>
  <div class="cross-sell-card bg-gradient-to-br from-green-50 to-blue-50 rounded-xl p-6 border-2 border-green-200 shadow-lg animate-fade-in">
    <!-- Header with Icon -->
    <div class="flex items-start gap-4 mb-4">
      <div class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
        <span class="text-3xl">🚗</span>
      </div>
      
      <div class="flex-1">
        <h3 class="text-xl font-bold text-gray-900 mb-2">
          {{ title }}
        </h3>
        <p class="text-gray-600 text-sm">
          {{ description }}
        </p>
      </div>
    </div>

    <!-- Transfer Options Preview -->
    <div v-if="showOptions" class="space-y-3 mb-4">
      <div 
        v-for="option in transferOptions" 
        :key="option.id"
        class="bg-white rounded-lg p-4 border border-gray-200 hover:border-green-300 hover:shadow-md transition-all cursor-pointer"
        @click="selectOption(option)"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="text-2xl">{{ option.icon }}</span>
            <div>
              <p class="font-semibold text-gray-900">{{ option.name }}</p>
              <p class="text-sm text-gray-500">{{ option.description }}</p>
            </div>
          </div>
          <div class="text-right">
            <p class="text-lg font-bold text-green-600">₺{{ formatPrice(option.price) }}</p>
            <p class="text-xs text-gray-500">{{ option.duration }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3">
      <button
        @click="handleAddTransfer"
        :disabled="loading"
        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <svg v-if="!loading" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <svg v-else class="w-5 h-5 animate-spin" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <span>{{ primaryButtonText }}</span>
      </button>
      
      <button
        @click="handleViewOptions"
        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-green-500 text-green-600 hover:bg-green-50 font-semibold rounded-lg transition-all"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        <span>{{ secondaryButtonText }}</span>
      </button>
      
      <button
        @click="handleDismiss"
        class="sm:w-auto px-4 py-3 text-gray-500 hover:text-gray-700 hover:bg-gray-100 font-medium rounded-lg transition-all"
      >
        Şimdi Değil
      </button>
    </div>

    <!-- Special Offer Badge -->
    <div v-if="hasDiscount" class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 bg-orange-100 border border-orange-300 rounded-full text-sm font-medium text-orange-700">
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
      </svg>
      <span>Otel rezervasyonuyla birlikte %15 indirim!</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

// Props
interface TransferOption {
  id: number
  name: string
  description: string
  price: number
  duration: string
  icon: string
}

interface Props {
  title?: string
  description?: string
  primaryButtonText?: string
  secondaryButtonText?: string
  hasDiscount?: boolean
  hotelLocation?: string
  bookingId?: string | number
  transferOptions?: TransferOption[]
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Havalimanı Transfer Hizmeti',
  description: 'Otel rezervasyonunuz alındı. Havalimanından otele transfer ister misiniz?',
  primaryButtonText: 'Transfer Ekle',
  secondaryButtonText: 'Seçenekleri Gör',
  hasDiscount: true,
  transferOptions: () => [
    {
      id: 1,
      name: 'Ekonomik Transfer',
      description: 'Paylaşımlı araç',
      price: 150,
      duration: '~45 dk',
      icon: '🚕'
    },
    {
      id: 2,
      name: 'Konforlu Transfer',
      description: 'Özel araç',
      price: 300,
      duration: '~30 dk',
      icon: '🚗'
    },
    {
      id: 3,
      name: 'VIP Transfer',
      description: 'Lüks araç',
      price: 500,
      duration: '~25 dk',
      icon: '✨'
    }
  ]
})

// Emits
const emit = defineEmits<{
  addTransfer: [option?: TransferOption]
  viewOptions: []
  dismiss: []
}>()

// State
const loading = ref(false)
const showOptions = ref(false)
const selectedOption = ref<TransferOption | null>(null)

// Methods
const formatPrice = (price: number) => {
  return price.toFixed(2)
}

const handleAddTransfer = async () => {
  loading.value = true
  try {
    // If no option selected, use the first one by default
    const option = selectedOption.value || props.transferOptions[0]
    emit('addTransfer', option)
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500))
  } finally {
    loading.value = false
  }
}

const handleViewOptions = () => {
  showOptions.value = !showOptions.value
  emit('viewOptions')
}

const handleDismiss = () => {
  emit('dismiss')
}

const selectOption = (option: TransferOption) => {
  selectedOption.value = option
}
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}
</style>
