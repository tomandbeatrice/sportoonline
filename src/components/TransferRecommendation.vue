<template>
  <div v-if="showRecommendation" class="transfer-recommendation-card bg-white rounded-2xl shadow-lg border-2 border-green-200 p-6 my-6 animate-fade-in">
    <!-- Header with Icon -->
    <div class="flex items-start gap-4 mb-4">
      <div class="flex-shrink-0">
        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-green-200">
          <span class="text-3xl">🚗</span>
        </div>
      </div>
      
      <div class="flex-1">
        <div class="flex items-center gap-2 mb-2">
          <h3 class="text-xl font-bold text-slate-900">Akıllı Öneri</h3>
          <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Özel</span>
        </div>
        <p class="text-slate-600">
          {{ message }}
        </p>
      </div>
    </div>

    <!-- Transfer Details (if available) -->
    <div v-if="transferOptions.length > 0" class="mb-4">
      <div class="bg-slate-50 rounded-xl p-4 space-y-3">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-slate-700">Tahmini Mesafe:</span>
          <span class="text-sm font-semibold text-slate-900">{{ estimatedDistance }}</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-slate-700">Tahmini Süre:</span>
          <span class="text-sm font-semibold text-slate-900">{{ estimatedDuration }}</span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-slate-700">Başlangıç Fiyatı:</span>
          <span class="text-lg font-bold text-green-600">{{ formatCurrency(startingPrice) }}</span>
        </div>
      </div>
    </div>

    <!-- Benefits List -->
    <div class="mb-5">
      <ul class="space-y-2">
        <li class="flex items-start gap-2 text-sm text-slate-700">
          <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <span>Havalimanı/istasyon karşılama hizmeti</span>
        </li>
        <li class="flex items-start gap-2 text-sm text-slate-700">
          <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <span>Profesyonel sürücü ve konforlu araçlar</span>
        </li>
        <li class="flex items-start gap-2 text-sm text-slate-700">
          <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <span>7/24 müşteri desteği ve uçuş takibi</span>
        </li>
      </ul>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3">
      <button 
        @click="handleAddToCart"
        :disabled="isAdding"
        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 text-white font-bold rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 hover:shadow-xl hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
      >
        <svg v-if="isAdding" class="w-5 h-5 animate-spin" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>{{ isAdding ? 'Ekleniyor...' : 'Sepete Ekle' }}</span>
      </button>
      
      <button 
        @click="handleViewDetails"
        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:border-green-300 hover:text-green-600 hover:bg-green-50 transition-all"
      >
        <span>Detayları İncele</span>
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
      </button>
    </div>

    <!-- Dismiss Button -->
    <button 
      @click="handleDismiss"
      class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
      aria-label="Kapat"
    >
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cartStore'
import { useToast } from 'vue-toastification'

// Props
interface Props {
  hotelBooking?: {
    destinationCoordinates?: {
      lat: number
      lng: number
    }
    destinationAddress?: string
    checkInDate?: string
    numberOfGuests?: number
    hotelName?: string
  }
  message?: string
  autoShow?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  message: 'Otel rezervasyonunuz alındı. Otele ulaşım için transfer ister misiniz?',
  autoShow: true
})

// Composables
const router = useRouter()
const cartStore = useCartStore()
const toast = useToast()

// State
const showRecommendation = ref(false)
const isAdding = ref(false)
const transferOptions = ref<any[]>([])

// Computed
const estimatedDistance = computed(() => {
  // Mock calculation - would normally use coordinates
  return '35 km'
})

const estimatedDuration = computed(() => {
  return '~45 dakika'
})

const startingPrice = computed(() => {
  // Mock pricing - would normally be calculated based on distance and service type
  if (props.hotelBooking?.numberOfGuests && props.hotelBooking.numberOfGuests > 4) {
    return 450 // Larger vehicle needed
  }
  return 350
})

// Methods
const formatCurrency = (amount: number) => `₺${amount.toFixed(2)}`

const fetchTransferOptions = async () => {
  try {
    // In a real implementation, this would call the Ride/Transfer API
    // For now, we'll use mock data
    transferOptions.value = [
      {
        id: 1,
        type: 'Airport Transfer',
        vehicleType: 'Sedan',
        capacity: 4,
        price: 350
      },
      {
        id: 2,
        type: 'Airport Transfer',
        vehicleType: 'Van',
        capacity: 7,
        price: 450
      }
    ]
    
    // If we had real coordinates, we'd make an API call like this:
    // const response = await fetch('/api/rides/search', {
    //   method: 'POST',
    //   headers: { 'Content-Type': 'application/json' },
    //   body: JSON.stringify({
    //     destination: props.hotelBooking?.destinationCoordinates,
    //     date: props.hotelBooking?.checkInDate,
    //     passengers: props.hotelBooking?.numberOfGuests,
    //     serviceType: 'airport_transfer'
    //   })
    // })
    // transferOptions.value = await response.json()
  } catch (error) {
    console.error('Failed to fetch transfer options:', error)
  }
}

const handleAddToCart = async () => {
  isAdding.value = true
  
  try {
    // Select appropriate transfer option based on guest count
    const selectedTransfer = props.hotelBooking?.numberOfGuests && props.hotelBooking.numberOfGuests > 4
      ? transferOptions.value[1] // Van
      : transferOptions.value[0] // Sedan
    
    // Create transfer product object for cart
    const transferProduct = {
      id: `transfer-${selectedTransfer?.id || 1}`,
      name: `Transfer Hizmeti - ${props.hotelBooking?.hotelName || 'Otel'}`,
      type: 'service',
      serviceType: 'airport_transfer',
      price: startingPrice.value,
      quantity: 1,
      image: '/images/services/transfer-default.jpg', // Placeholder image
      duration: typeof estimatedDuration.value === 'number' ? estimatedDuration.value : undefined,
      details: {
        destination: props.hotelBooking?.destinationAddress,
        checkInDate: props.hotelBooking?.checkInDate,
        numberOfGuests: props.hotelBooking?.numberOfGuests,
        vehicleType: selectedTransfer?.vehicleType || 'Sedan',
        estimatedDistance: estimatedDistance.value,
        estimatedDuration: estimatedDuration.value
      }
    }
    
    // Add to cart using cart store
    // @ts-ignore - allowing extra properties
    cartStore.addToCart(transferProduct)
    
    // Show success message
    toast.success('Transfer hizmeti sepete eklendi! 🚗')
    
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 500))
    
    // Hide the recommendation card after adding
    showRecommendation.value = false
  } catch (error) {
    console.error('Failed to add transfer to cart:', error)
    toast.error('Transfer eklenirken bir hata oluştu')
  } finally {
    isAdding.value = false
  }
}

const handleViewDetails = () => {
  // Navigate to rides/transfer page with pre-filled details
  const query: any = {}
  
  if (props.hotelBooking?.destinationAddress) {
    query.destination = props.hotelBooking.destinationAddress
  }
  if (props.hotelBooking?.checkInDate) {
    query.date = props.hotelBooking.checkInDate
  }
  if (props.hotelBooking?.numberOfGuests) {
    query.passengers = props.hotelBooking.numberOfGuests
  }
  
  router.push({ 
    path: '/rides', 
    query: {
      ...query,
      type: 'airport_transfer'
    }
  })
}

const handleDismiss = () => {
  showRecommendation.value = false
  
  // Store dismissal in localStorage to avoid showing again for this session
  const dismissedRecommendations = JSON.parse(
    localStorage.getItem('dismissedRecommendations') || '[]'
  )
  dismissedRecommendations.push({
    type: 'hotel_transfer',
    timestamp: new Date().toISOString()
  })
  localStorage.setItem('dismissedRecommendations', JSON.stringify(dismissedRecommendations))
}

// Lifecycle
onMounted(async () => {
  // Check if this recommendation has been dismissed recently
  const dismissedRecommendations = JSON.parse(
    localStorage.getItem('dismissedRecommendations') || '[]'
  )
  
  const recentlyDismissed = dismissedRecommendations.some((item: any) => {
    if (item.type !== 'hotel_transfer') return false
    const dismissedAt = new Date(item.timestamp)
    const hoursSinceDismissal = (Date.now() - dismissedAt.getTime()) / (1000 * 60 * 60)
    return hoursSinceDismissal < 24 // Don't show again within 24 hours
  })
  
  if (!recentlyDismissed && props.autoShow) {
    await fetchTransferOptions()
    // Show with a slight delay for better UX
    setTimeout(() => {
      showRecommendation.value = true
    }, 500)
  }
})

// Expose methods for parent components
defineExpose({
  show: () => {
    fetchTransferOptions()
    showRecommendation.value = true
  },
  hide: () => {
    showRecommendation.value = false
  }
})
</script>

<style scoped>
.transfer-recommendation-card {
  position: relative;
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .transfer-recommendation-card {
    margin: 1rem;
  }
}
</style>
