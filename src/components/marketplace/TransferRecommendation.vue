<!-- 
  TransferRecommendation.vue - Cross-Selling Component for Hotel -> Transfer
  Displays a friendly recommendation card for transfer services after hotel booking
-->
<template>
  <div class="transfer-recommendation bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border-2 border-green-200 shadow-lg">
    <!-- Header with Icon -->
    <div class="flex items-start gap-4">
      <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-green-200 flex-shrink-0">
        <span class="text-3xl">🚗</span>
      </div>
      
      <div class="flex-1">
        <!-- Title -->
        <h3 class="text-xl font-bold text-slate-900 mb-2">
          Need a ride{{ destination ? ` to ${destination}` : '' }}?
        </h3>
        
        <!-- Description -->
        <p class="text-slate-600 mb-4">
          Complete your trip with a comfortable transfer service. Get picked up from the airport or city center and arrive at your hotel stress-free.
        </p>
        
        <!-- Benefits List -->
        <ul class="space-y-2 mb-5">
          <li class="flex items-center gap-2 text-sm text-slate-700">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Professional drivers</span>
          </li>
          <li class="flex items-center gap-2 text-sm text-slate-700">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>24/7 customer support</span>
          </li>
          <li class="flex items-center gap-2 text-sm text-slate-700">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Competitive pricing</span>
          </li>
        </ul>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
          <button 
            @click="handleAddTransfer"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 hover:shadow-xl hover:-translate-y-0.5 transition-all"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Transfer
          </button>
          
          <button 
            @click="handleNoThanks"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:border-slate-300 hover:bg-slate-50 transition-all"
          >
            No thanks
          </button>
        </div>
      </div>
    </div>
    
    <!-- Special Offer Badge -->
    <div v-if="showDiscount" class="absolute top-4 right-4 px-3 py-1 bg-red-500 text-white text-sm font-bold rounded-full shadow-lg animate-pulse">
      15% OFF
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

interface Props {
  destination?: string
  showDiscount?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  destination: '',
  showDiscount: true
})

const emit = defineEmits<{
  (e: 'add-transfer'): void
  (e: 'dismiss'): void
}>()

const handleAddTransfer = () => {
  emit('add-transfer')
}

const handleNoThanks = () => {
  emit('dismiss')
}
</script>

<style scoped>
.transfer-recommendation {
  position: relative;
  animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
