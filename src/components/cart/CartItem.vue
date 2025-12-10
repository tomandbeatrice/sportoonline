<template>
  <div class="flex gap-4 py-4">
    <!-- Image -->
    <SmartImage 
      :src="item.image || '/placeholder-product.png'" 
      :alt="item.name" 
      container-class="h-20 w-20 flex-shrink-0 rounded-lg border border-slate-100"
    />

    <!-- Content -->
    <div class="flex flex-1 flex-col justify-between">
      <div class="flex justify-between gap-2">
        <div>
          <h3 class="font-medium text-slate-900">{{ item.name }}</h3>
          
          <!-- Type Specific Details -->
          <div class="mt-1 space-y-1">
            <!-- Product -->
            <p v-if="item.type === 'product' && item.variant" class="text-sm text-slate-500">
              {{ item.variant }}
            </p>
            
            <!-- Service -->
            <p v-if="item.type === 'service'" class="text-sm text-slate-500">
              <span v-if="item.duration">⏱️ {{ item.duration }} dk</span>
            </p>

            <!-- Booking -->
            <div v-if="item.type === 'booking'" class="text-sm text-slate-500">
              <p>📅 {{ formatDate(item.startDate) }} - {{ formatDate(item.endDate) }}</p>
              <p>👥 {{ item.guests }} Misafir</p>
            </div>
          </div>

        </div>
        
        <!-- Remove Button -->
        <button 
          @click="removeItem" 
          class="text-slate-400 hover:text-red-500 transition-colors"
          title="Sepetten Çıkar"
        >
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>

      <div class="flex items-center justify-between mt-2">
        <!-- Quantity Control -->
        <div class="flex items-center rounded-lg border border-slate-200 bg-white shadow-sm">
          <button 
            @click="updateQuantity(-1)"
            class="px-3 py-1 text-slate-600 hover:bg-slate-50 disabled:opacity-50 transition-colors"
            :disabled="item.quantity <= 1"
          >
            -
          </button>
          <span class="min-w-[2rem] text-center text-sm font-medium text-slate-700">{{ item.quantity }}</span>
          <button 
            @click="updateQuantity(1)"
            class="px-3 py-1 text-slate-600 hover:bg-slate-50 transition-colors"
          >
            +
          </button>
        </div>

        <!-- Price -->
        <div class="text-right">
          <div class="font-bold text-slate-900">{{ formatPrice(item.price * item.quantity) }}</div>
          <div v-if="item.quantity > 1" class="text-xs text-slate-500">
            {{ formatPrice(item.price) }} / adet
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue'
import { useCartStore, type CartItem } from '../../stores/cartStore'
import SmartImage from '@/components/ui/SmartImage.vue'

const props = defineProps<{
  item: CartItem
}>()

const cartStore = useCartStore()

const updateQuantity = (change: number) => {
  cartStore.updateQuantity(props.item.id, props.item.quantity + change)
}

const removeItem = () => {
  cartStore.removeFromCart(props.item.id)
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY'
  }).format(price)
}

const formatDate = (dateStr: string | undefined) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('tr-TR', { day: 'numeric', month: 'short' })
}
</script>
