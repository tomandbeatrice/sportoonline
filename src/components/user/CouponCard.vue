<template>
  <div class="border border-slate-200 rounded-xl overflow-hidden hover:shadow-lg transition" :class="{ 'opacity-60': expired || used }">
    <div class="bg-gradient-to-r p-6 text-white relative overflow-hidden" :class="getCouponGradient(coupon.type)">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white rounded-full -ml-12 -mb-12"></div>
      </div>

      <div class="relative z-10">
        <div class="flex items-start justify-between mb-3">
          <div class="text-4xl">{{ coupon.icon }}</div>
          <div class="text-right">
            <div class="text-2xl font-bold">{{ coupon.discount }}</div>
            <div class="text-xs opacity-90">İNDİRİM</div>
          </div>
        </div>
        
        <h3 class="font-bold text-lg mb-1">{{ coupon.title }}</h3>
        <p class="text-sm opacity-90">{{ coupon.description }}</p>
      </div>
    </div>

    <div class="p-4 bg-white">
      <!-- Coupon Code -->
      <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100">
        <div>
          <div class="text-xs text-slate-500 mb-1">Kupon Kodu</div>
          <div class="font-mono font-bold text-lg text-slate-900">{{ coupon.code }}</div>
        </div>
        <button 
          @click="copyCode"
          class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm font-medium transition"
        >
          📋 Kopyala
        </button>
      </div>

      <!-- Details -->
      <div class="space-y-2 mb-4">
        <div v-if="coupon.minAmount > 0" class="flex items-center gap-2 text-sm text-slate-600">
          <span>💰</span>
          <span>Minimum {{ formatPrice(coupon.minAmount) }} alışveriş</span>
        </div>
        <div class="flex items-center gap-2 text-sm" :class="isExpiringSoon ? 'text-orange-600 font-medium' : 'text-slate-600'">
          <span>📅</span>
          <span>{{ getExpiryText() }}</span>
        </div>
        <div v-if="used && coupon.usedDate" class="flex items-center gap-2 text-sm text-green-600">
          <span>✅</span>
          <span>{{ coupon.usedDate }} tarihinde kullanıldı</span>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex gap-2">
        <button 
          v-if="!used && !expired"
          @click="$emit('use', coupon)"
          class="flex-1 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition"
        >
          Kullan
        </button>
        <button 
          @click="$emit('details', coupon)"
          class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-medium hover:bg-slate-50 transition"
        >
          Detaylar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

interface Coupon {
  id: number
  code: string
  title: string
  discount: string
  description: string
  minAmount: number
  expiryDate: string
  status: string
  type: string
  icon: string
  terms?: string[]
  usedDate?: string
}

const props = defineProps<{
  coupon: Coupon
  used?: boolean
  expired?: boolean
}>()

defineEmits(['use', 'details'])

const isExpiringSoon = computed(() => {
  const expiry = new Date(props.coupon.expiryDate)
  const now = new Date()
  const daysLeft = Math.ceil((expiry.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))
  return daysLeft <= 7 && daysLeft > 0
})

const getCouponGradient = (type: string) => {
  const gradients: Record<string, string> = {
    'fixed': 'from-blue-500 to-blue-600',
    'percentage': 'from-purple-500 to-purple-600',
    'shipping': 'from-green-500 to-green-600',
  }
  return gradients[type] || 'from-slate-500 to-slate-600'
}

const getExpiryText = () => {
  if (props.expired) {
    return `Süresi ${props.coupon.expiryDate} tarihinde doldu`
  }
  
  const expiry = new Date(props.coupon.expiryDate)
  const now = new Date()
  const daysLeft = Math.ceil((expiry.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))
  
  if (daysLeft === 0) return 'Bugün sona eriyor'
  if (daysLeft === 1) return 'Yarın sona eriyor'
  if (daysLeft <= 7) return `${daysLeft} gün kaldı`
  return `${props.coupon.expiryDate} tarihine kadar geçerli`
}

const formatPrice = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { 
    style: 'currency', 
    currency: 'TRY', 
    maximumFractionDigits: 0 
  }).format(value)
}

const copyCode = () => {
  navigator.clipboard.writeText(props.coupon.code)
  toast.success('Kupon kodu kopyalandı!')
}
</script>
