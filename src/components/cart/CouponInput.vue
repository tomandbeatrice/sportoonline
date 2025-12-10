<template>
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <!-- Header -->
    <div class="px-5 py-4 border-b border-slate-100">
      <h3 class="font-bold text-slate-900 flex items-center gap-2">
        <span>🏷️</span>
        İndirim Kuponu
      </h3>
    </div>

    <!-- Applied Coupon -->
    <div v-if="appliedCoupon" class="px-5 py-4 bg-green-50">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-lg">✓</div>
          <div>
            <div class="font-mono font-bold text-green-700">{{ appliedCoupon.code }}</div>
            <div class="text-sm text-green-600">{{ appliedCoupon.description }}</div>
          </div>
        </div>
        <div class="text-right">
          <div class="font-bold text-green-700">-{{ formatDiscount(appliedCoupon) }}</div>
          <button 
            @click="removeCoupon"
            class="text-xs text-red-500 hover:underline"
          >
            Kaldır
          </button>
        </div>
      </div>
    </div>

    <!-- Input -->
    <div v-else class="px-5 py-4">
      <div class="flex gap-2">
        <input
          v-model="couponCode"
          type="text"
          placeholder="Kupon kodunuz"
          class="flex-1 px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 font-mono uppercase"
          :disabled="isLoading"
          @keyup.enter="applyCoupon"
        />
        <button 
          @click="applyCoupon"
          :disabled="!couponCode.trim() || isLoading"
          class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition disabled:opacity-50"
        >
          {{ isLoading ? '...' : 'Uygula' }}
        </button>
      </div>
      
      <p v-if="error" class="text-red-500 text-sm mt-2 flex items-center gap-1">
        <span>⚠️</span>
        {{ error }}
      </p>
    </div>

    <!-- Available Coupons -->
    <div v-if="!appliedCoupon && availableCoupons.length > 0" class="border-t border-slate-100">
      <button 
        @click="showAvailable = !showAvailable"
        class="w-full px-5 py-3 flex items-center justify-between text-sm text-indigo-600 hover:bg-slate-50 transition"
      >
        <span>🎁 {{ availableCoupons.length }} kullanılabilir kupon</span>
        <span>{{ showAvailable ? '▲' : '▼' }}</span>
      </button>
      
      <Transition name="collapse">
        <div v-if="showAvailable" class="px-5 pb-4 space-y-3">
          <div 
            v-for="coupon in availableCoupons"
            :key="coupon.code"
            @click="selectCoupon(coupon)"
            class="flex items-center justify-between p-4 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-100 rounded-xl cursor-pointer hover:shadow-md transition"
          >
            <div class="flex items-center gap-3">
              <div 
                class="w-12 h-12 rounded-xl flex items-center justify-center text-xl font-bold"
                :class="coupon.type === 'percentage' ? 'bg-purple-100 text-purple-600' : 
                        coupon.type === 'fixed' ? 'bg-green-100 text-green-600' : 
                        'bg-blue-100 text-blue-600'"
              >
                {{ coupon.type === 'percentage' ? '%' : coupon.type === 'fixed' ? '₺' : '🚚' }}
              </div>
              <div>
                <div class="font-mono font-bold text-slate-900">{{ coupon.code }}</div>
                <div class="text-sm text-slate-500">{{ coupon.description }}</div>
                <div v-if="coupon.minPurchase" class="text-xs text-slate-400">
                  Min. {{ formatPrice(coupon.minPurchase) }} alışveriş
                </div>
              </div>
            </div>
            <div class="text-right">
              <div class="font-bold text-indigo-600 text-lg">
                {{ formatCouponValue(coupon) }}
              </div>
              <div class="text-xs text-slate-400">
                {{ formatDate(coupon.endDate) }}'e kadar
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

interface Coupon {
  code: string
  description: string
  type: 'percentage' | 'fixed' | 'shipping'
  value: number
  maxDiscount?: number
  minPurchase: number
  endDate: string
}

const props = defineProps<{
  cartTotal: number
}>()

const emit = defineEmits<{
  apply: [coupon: Coupon, discount: number]
  remove: []
}>()

const couponCode = ref('')
const appliedCoupon = ref<Coupon | null>(null)
const isLoading = ref(false)
const error = ref('')
const showAvailable = ref(false)

// Available coupons from API
const availableCoupons = ref<Coupon[]>([])

// Fetch available coupons on mount
const fetchAvailableCoupons = async () => {
  try {
    const { data } = await axios.get('/api/coupons/available')
    availableCoupons.value = data.coupons || data || []
  } catch (err) {
    // Fallback mock data for demo
    availableCoupons.value = [
      { code: 'HOSGELDIN20', description: 'Yeni üye indirimi', type: 'percentage', value: 20, maxDiscount: 100, minPurchase: 200, endDate: '2024-12-31' },
      { code: 'YAZ15', description: 'Yaz kampanyası', type: 'percentage', value: 15, minPurchase: 300, endDate: '2024-08-31' },
    ]
  }
}

onMounted(() => {
  fetchAvailableCoupons()
})

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(price)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', { day: 'numeric', month: 'short' })
}

const formatCouponValue = (coupon: Coupon) => {
  if (coupon.type === 'percentage') return `%${coupon.value}`
  if (coupon.type === 'fixed') return formatPrice(coupon.value)
  return 'Ücretsiz Kargo'
}

const formatDiscount = (coupon: Coupon) => {
  const discount = calculateDiscount(coupon)
  return formatPrice(discount)
}

const calculateDiscount = (coupon: Coupon): number => {
  if (coupon.type === 'percentage') {
    let discount = (props.cartTotal * coupon.value) / 100
    if (coupon.maxDiscount && discount > coupon.maxDiscount) {
      discount = coupon.maxDiscount
    }
    return discount
  }
  if (coupon.type === 'fixed') {
    return coupon.value
  }
  // Shipping - would be actual shipping cost
  return 29.90
}

const applyCoupon = async () => {
  if (!couponCode.value.trim()) return
  
  error.value = ''
  isLoading.value = true
  
  try {
    // Validate coupon via API
    const { data } = await axios.post('/api/seller/turbo-coupons/validate', {
      code: couponCode.value,
      cart_total: props.cartTotal
    })
    
    if (data.valid && data.coupon) {
      const coupon: Coupon = {
        code: data.coupon.code,
        description: data.coupon.description || '',
        type: data.coupon.type || 'percentage',
        value: data.coupon.value || data.coupon.discount_value,
        maxDiscount: data.coupon.max_discount,
        minPurchase: data.coupon.min_purchase || 0,
        endDate: data.coupon.end_date || data.coupon.expires_at
      }
      
      appliedCoupon.value = coupon
      const discount = data.discount || calculateDiscount(coupon)
      emit('apply', coupon, discount)
      couponCode.value = ''
    } else {
      error.value = data.message || 'Geçersiz kupon kodu'
    }
  } catch (err: any) {
    // Fallback to local validation
    const coupon = availableCoupons.value.find(
      c => c.code.toLowerCase() === couponCode.value.toLowerCase()
    )
    
    if (!coupon) {
      error.value = 'Geçersiz kupon kodu'
      return
    }
    
    if (coupon.minPurchase && props.cartTotal < coupon.minPurchase) {
      error.value = `Bu kupon için minimum ${formatPrice(coupon.minPurchase)} alışveriş gerekli`
      return
    }
    
    // Check expiry
    if (new Date(coupon.endDate) < new Date()) {
      error.value = 'Bu kuponun süresi dolmuş'
      return
    }
    
    // Apply coupon
    appliedCoupon.value = coupon
    const discount = calculateDiscount(coupon)
    emit('apply', coupon, discount)
    couponCode.value = ''
  } finally {
    isLoading.value = false
  }
}

const selectCoupon = (coupon: Coupon) => {
  couponCode.value = coupon.code
  applyCoupon()
}

const removeCoupon = () => {
  appliedCoupon.value = null
  emit('remove')
}
</script>

<style scoped>
.collapse-enter-active,
.collapse-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}
.collapse-enter-from,
.collapse-leave-to {
  opacity: 0;
  max-height: 0;
}
.collapse-enter-to,
.collapse-leave-from {
  max-height: 500px;
}
</style>
