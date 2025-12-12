<template>
  <div class="flex items-center gap-4 p-4 hover:bg-slate-50 transition rounded-xl">
    <!-- Image -->
    <div class="flex-shrink-0 w-24 h-24 bg-slate-100 rounded-xl overflow-hidden">
      <img 
        :src="item.image || item.image_url || 'https://via.placeholder.com/100'" 
        :alt="item.name"
        class="w-full h-full object-cover"
      >
    </div>

    <!-- Info -->
    <div class="flex-1 min-w-0">
      <h3 class="font-medium text-slate-900 text-sm line-clamp-2">{{ item.name }}</h3>
      
      <!-- Variant Info -->
      <div v-if="item.variant" class="mt-1 text-xs text-slate-500">
        {{ item.variant }}
      </div>
      
      <!-- Type Badge -->
      <div class="mt-2 flex items-center gap-2">
        <span 
          class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
          :class="getTypeBadgeClass(item.type)"
        >
          {{ getTypeLabel(item.type) }}
        </span>
        <span v-if="item.stock && item.stock < 5" class="text-xs text-orange-600 font-medium">
          Son {{ item.stock }} adet
        </span>
      </div>

      <!-- Delivery Info -->
      <div class="mt-2 flex items-center gap-1 text-xs text-slate-500">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ getDeliveryTime(item.type) }}</span>
      </div>
    </div>

    <!-- Quantity & Price -->
    <div class="flex flex-col items-end gap-3">
      <!-- Quantity Selector -->
      <div class="flex items-center border border-slate-200 rounded-lg">
        <button 
          @click="decreaseQuantity"
          :disabled="item.quantity <= 1"
          class="px-3 py-1.5 text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          −
        </button>
        <input 
          :value="item.quantity"
          @input="updateQuantity($event)"
          type="number" 
          min="1"
          :max="item.stock || 99"
          class="w-12 text-center border-0 py-1.5 text-sm focus:ring-0"
        >
        <button 
          @click="increaseQuantity"
          :disabled="item.stock && item.quantity >= item.stock"
          class="px-3 py-1.5 text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          +
        </button>
      </div>

      <!-- Price -->
      <div class="text-right">
        <div v-if="item.originalPrice && item.originalPrice > item.price" class="text-xs text-slate-400 line-through">
          {{ formatPrice(item.originalPrice * item.quantity) }} TL
        </div>
        <div class="text-lg font-bold text-slate-900">
          {{ formatPrice(item.price * item.quantity) }} TL
        </div>
        <div class="text-xs text-slate-500">
          {{ formatPrice(item.price) }} TL / adet
        </div>
      </div>

      <!-- Remove Button -->
      <button 
        @click="removeItem"
        class="text-red-600 hover:text-red-700 text-xs font-medium flex items-center gap-1"
      >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Kaldır
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCartStore } from '@/stores/cartStore'
import { useToast } from 'vue-toastification'

const cartStore = useCartStore()
const toast = useToast()

interface CartItem {
  id: number
  name: string
  price: number
  originalPrice?: number
  image?: string
  image_url?: string
  quantity: number
  stock?: number
  type?: string
  variant?: string
}

const props = defineProps<{
  item: CartItem
}>()

const getTypeBadgeClass = (type?: string) => {
  const classes: Record<string, string> = {
    'product': 'bg-blue-100 text-blue-700',
    'service': 'bg-purple-100 text-purple-700',
    'food': 'bg-orange-100 text-orange-700',
    'hotel': 'bg-green-100 text-green-700',
    'ride': 'bg-yellow-100 text-yellow-700'
  }
  return classes[type || 'product'] || 'bg-slate-100 text-slate-700'
}

const getTypeLabel = (type?: string) => {
  const labels: Record<string, string> = {
    'product': 'Ürün',
    'service': 'Hizmet',
    'food': 'Yemek',
    'hotel': 'Konaklama',
    'ride': 'Ulaşım'
  }
  return labels[type || 'product'] || 'Ürün'
}

const getDeliveryTime = (type?: string) => {
  const times: Record<string, string> = {
    'product': '1-3 iş günü',
    'service': 'Rezervasyon sonrası',
    'food': '30-45 dakika',
    'hotel': 'Check-in tarihinde',
    'ride': 'Anında'
  }
  return times[type || 'product'] || '1-3 iş günü'
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price)
}

const decreaseQuantity = () => {
  if (props.item.quantity > 1) {
    cartStore.updateQuantity(props.item.id, props.item.quantity - 1)
  }
}

const increaseQuantity = () => {
  const maxQty = props.item.stock || 99
  if (props.item.quantity < maxQty) {
    cartStore.updateQuantity(props.item.id, props.item.quantity + 1)
  } else {
    toast.warning(`Maksimum ${maxQty} adet ekleyebilirsiniz`)
  }
}

const updateQuantity = (event: Event) => {
  const input = event.target as HTMLInputElement
  const newQty = parseInt(input.value) || 1
  const maxQty = props.item.stock || 99
  
  if (newQty < 1) {
    cartStore.updateQuantity(props.item.id, 1)
  } else if (newQty > maxQty) {
    cartStore.updateQuantity(props.item.id, maxQty)
    toast.warning(`Maksimum ${maxQty} adet ekleyebilirsiniz`)
  } else {
    cartStore.updateQuantity(props.item.id, newQty)
  }
}

const removeItem = () => {
  if (confirm('Bu ürünü sepetten çıkarmak istediğinize emin misiniz?')) {
    cartStore.removeFromCart(props.item.id)
    toast.success('Ürün sepetten çıkarıldı')
  }
}
</script>
