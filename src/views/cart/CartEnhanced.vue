<template>
  <div class="min-h-screen bg-slate-50 py-12 font-sans">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-slate-900">
          Sepetim 
          <span v-if="cartStore.totalItems > 0" class="text-blue-600">({{ cartStore.totalItems }} Ürün)</span>
        </h1>
        <router-link to="/" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Alışverişe Devam Et
        </router-link>
      </div>

      <!-- Empty State -->
      <div v-if="cartStore.items.length === 0" class="text-center py-20 bg-white rounded-3xl shadow-sm border border-slate-100">
        <div class="text-6xl mb-4">🛒</div>
        <h2 class="text-xl font-bold text-slate-900 mb-2">Sepetiniz Boş</h2>
        <p class="text-slate-500 mb-8">Henüz sepetinize ürün eklemediniz.</p>
        <router-link to="/products" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-8 py-3 text-base font-bold text-white shadow-lg shadow-blue-200 transition-all hover:bg-blue-700 hover:shadow-blue-300 hover:-translate-y-1">
          Alışverişe Başla
        </router-link>
      </div>

      <!-- Cart Content -->
      <div v-else class="lg:grid lg:grid-cols-12 lg:gap-x-8 lg:items-start">
        <!-- Cart Items -->
        <div class="lg:col-span-8 space-y-6">
          
          <!-- Grouped Items -->
          <div v-for="(group, groupType) in groupedItems" :key="groupType" v-show="group.items.length > 0">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
              <!-- Group Header -->
              <div :class="getGroupHeaderClass(groupType)" class="px-6 py-4 border-b flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <span class="text-2xl">{{ group.icon }}</span>
                  <div>
                    <h3 class="font-bold">{{ group.title }}</h3>
                    <p class="text-sm opacity-80">{{ group.subtitle }}</p>
                  </div>
                </div>
                <span class="font-bold">{{ group.items.length }} ürün</span>
              </div>

              <!-- Items -->
              <ul class="divide-y divide-slate-100">
                <li v-for="item in group.items" :key="item.id">
                  <CartItemEnhanced :item="item" />
                </li>
              </ul>
            </div>
          </div>

        </div>

        <!-- Order Summary Sidebar -->
        <div class="lg:col-span-4 mt-8 lg:mt-0">
          <div class="sticky top-24 space-y-6">
            
            <!-- Coupon Section -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h3 class="font-bold text-slate-900 mb-4">İndirim Kuponu</h3>
              
              <!-- Applied Coupon -->
              <div v-if="appliedCoupon" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center gap-2">
                    <span class="text-2xl">🎫</span>
                    <div>
                      <div class="font-bold text-green-800">{{ appliedCoupon.code }}</div>
                      <div class="text-xs text-green-600">{{ appliedCoupon.description }}</div>
                    </div>
                  </div>
                  <button 
                    @click="removeCoupon"
                    class="text-red-600 hover:text-red-700 text-xs font-medium"
                  >
                    Kaldır
                  </button>
                </div>
                <div class="text-sm font-medium text-green-700">
                  -{{ formatPrice(couponDiscount) }} TL indirim
                </div>
              </div>

              <!-- Coupon Input -->
              <div v-else>
                <div class="flex gap-2">
                  <input 
                    v-model="couponCode"
                    type="text" 
                    placeholder="Kupon kodunu girin"
                    class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase"
                    @keyup.enter="applyCoupon"
                  >
                  <button 
                    @click="applyCoupon"
                    :disabled="!couponCode.trim()"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Uygula
                  </button>
                </div>
                
                <!-- Available Coupons -->
                <div v-if="availableCoupons.length > 0" class="mt-4">
                  <p class="text-xs text-slate-500 mb-2">Kullanabileceğiniz kuponlar:</p>
                  <div class="space-y-2">
                    <button 
                      v-for="coupon in availableCoupons" 
                      :key="coupon.code"
                      @click="quickApplyCoupon(coupon)"
                      class="w-full text-left p-3 border border-slate-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-xs"
                    >
                      <div class="font-bold text-slate-900">{{ coupon.code }}</div>
                      <div class="text-slate-600">{{ coupon.description }}</div>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Shipping Options -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h3 class="font-bold text-slate-900 mb-4">Kargo Seçenekleri</h3>
              
              <div class="space-y-3">
                <label 
                  v-for="option in shippingOptions" 
                  :key="option.id"
                  class="flex items-center justify-between p-3 border border-slate-200 rounded-xl cursor-pointer hover:border-blue-500 transition"
                  :class="selectedShipping === option.id ? 'border-blue-500 bg-blue-50' : ''"
                >
                  <div class="flex items-center gap-3">
                    <input 
                      type="radio" 
                      :value="option.id"
                      v-model="selectedShipping"
                      class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500"
                    >
                    <div>
                      <div class="font-medium text-slate-900 text-sm">{{ option.name }}</div>
                      <div class="text-xs text-slate-500">{{ option.duration }}</div>
                    </div>
                  </div>
                  <div class="font-bold text-slate-900">
                    {{ option.price === 0 ? 'Ücretsiz' : formatPrice(option.price) + ' TL' }}
                  </div>
                </label>
              </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
              <h3 class="font-bold text-slate-900 mb-4">Sipariş Özeti</h3>

              <div class="space-y-3">
                <!-- Subtotal -->
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-600">Ara Toplam ({{ cartStore.totalItems }} ürün)</span>
                  <span class="font-medium text-slate-900">{{ formatPrice(subtotal) }} TL</span>
                </div>

                <!-- Coupon Discount -->
                <div v-if="couponDiscount > 0" class="flex items-center justify-between text-sm">
                  <span class="text-green-600">Kupon İndirimi</span>
                  <span class="font-medium text-green-600">-{{ formatPrice(couponDiscount) }} TL</span>
                </div>

                <!-- Shipping -->
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-600">Kargo</span>
                  <span class="font-medium text-slate-900">
                    {{ shippingCost === 0 ? 'Ücretsiz' : formatPrice(shippingCost) + ' TL' }}
                  </span>
                </div>

                <!-- Tax -->
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-600">KDV (%{{ taxRate }})</span>
                  <span class="font-medium text-slate-900">{{ formatPrice(taxAmount) }} TL</span>
                </div>

                <!-- Total -->
                <div class="border-t border-slate-200 pt-3 flex items-center justify-between">
                  <span class="text-lg font-bold text-slate-900">Toplam</span>
                  <span class="text-2xl font-bold text-blue-600">{{ formatPrice(total) }} TL</span>
                </div>

                <!-- Savings Badge -->
                <div v-if="totalSavings > 0" class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                  <span class="text-sm font-medium text-green-700">
                    🎉 Toplam {{ formatPrice(totalSavings) }} TL tasarruf ediyorsunuz!
                  </span>
                </div>
              </div>

              <!-- Checkout Button -->
              <router-link
                to="/checkout"
                class="mt-6 flex w-full items-center justify-center rounded-xl border border-transparent bg-blue-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-blue-300 transition-all hover:-translate-y-1"
              >
                Sepeti Onayla
                <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </router-link>

              <!-- Security Badge -->
              <div class="mt-4 flex items-center justify-center gap-2 text-xs text-slate-500">
                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Güvenli Ödeme
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useCartStore } from '@/stores/cartStore'
import { useToast } from 'vue-toastification'
import CartItemEnhanced from '@/components/cart/CartItemEnhanced.vue'

const cartStore = useCartStore()
const toast = useToast()

// State
const couponCode = ref('')
const appliedCoupon = ref<any>(null)
const selectedShipping = ref('standard')
const taxRate = ref(20) // 20% KDV

// Mock Available Coupons
const availableCoupons = ref([
  { code: 'WELCOME50', description: '50 TL indirim (500 TL üzeri)', type: 'fixed', value: 50, minAmount: 500 },
  { code: 'SPORT20', description: '%20 indirim', type: 'percentage', value: 20, minAmount: 0 },
  { code: 'FREESHIP', description: 'Ücretsiz kargo', type: 'shipping', value: 0, minAmount: 0 }
])

// Shipping Options
const shippingOptions = ref([
  { id: 'standard', name: 'Standart Kargo', duration: '3-5 iş günü', price: 0 },
  { id: 'express', name: 'Hızlı Kargo', duration: '1-2 iş günü', price: 29.90 },
  { id: 'same-day', name: 'Aynı Gün Teslimat', duration: 'Bugün', price: 49.90 }
])

// Computed
const groupedItems = computed(() => {
  const groups: Record<string, any> = {
    product: { icon: '📦', title: 'Kargo ile Teslimat', subtitle: '1-3 iş günü', items: [] },
    food: { icon: '🛵', title: 'Yemek Siparişi', subtitle: '30-45 dakika', items: [] },
    service: { icon: '🎫', title: 'Hizmet & Rezervasyon', subtitle: 'Rezervasyon sonrası', items: [] }
  }

  cartStore.items.forEach(item => {
    const type = item.type || 'product'
    if (groups[type]) {
      groups[type].items.push(item)
    } else {
      groups.product.items.push(item)
    }
  })

  return groups
})

const subtotal = computed(() => {
  return cartStore.items.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const couponDiscount = computed(() => {
  if (!appliedCoupon.value) return 0

  const coupon = appliedCoupon.value
  
  // Check minimum amount
  if (coupon.minAmount && subtotal.value < coupon.minAmount) {
    return 0
  }

  if (coupon.type === 'fixed') {
    return Math.min(coupon.value, subtotal.value)
  } else if (coupon.type === 'percentage') {
    return subtotal.value * (coupon.value / 100)
  } else if (coupon.type === 'shipping') {
    return shippingCost.value
  }

  return 0
})

const shippingCost = computed(() => {
  const selected = shippingOptions.value.find(o => o.id === selectedShipping.value)
  return selected?.price || 0
})

const subtotalAfterCoupon = computed(() => {
  return subtotal.value - couponDiscount.value
})

const taxAmount = computed(() => {
  return subtotalAfterCoupon.value * (taxRate.value / 100)
})

const total = computed(() => {
  let sum = subtotalAfterCoupon.value + shippingCost.value + taxAmount.value
  
  // Free shipping coupon
  if (appliedCoupon.value?.type === 'shipping') {
    sum -= shippingCost.value
  }
  
  return Math.max(0, sum)
})

const totalSavings = computed(() => {
  let savings = 0
  
  // Product discounts
  cartStore.items.forEach(item => {
    if (item.originalPrice && item.originalPrice > item.price) {
      savings += (item.originalPrice - item.price) * item.quantity
    }
  })
  
  // Coupon discount
  savings += couponDiscount.value
  
  return savings
})

// Methods
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(price)
}

const getGroupHeaderClass = (type: string) => {
  const classes: Record<string, string> = {
    'product': 'bg-blue-50 text-blue-800 border-blue-100',
    'food': 'bg-orange-50 text-orange-800 border-orange-100',
    'service': 'bg-purple-50 text-purple-800 border-purple-100'
  }
  return classes[type] || 'bg-slate-50 text-slate-800 border-slate-100'
}

const applyCoupon = () => {
  const code = couponCode.value.trim().toUpperCase()
  if (!code) return

  const coupon = availableCoupons.value.find(c => c.code === code)
  
  if (!coupon) {
    toast.error('Geçersiz kupon kodu')
    return
  }

  if (coupon.minAmount && subtotal.value < coupon.minAmount) {
    toast.error(`Bu kupon ${formatPrice(coupon.minAmount)} TL ve üzeri alışverişlerde geçerlidir`)
    return
  }

  appliedCoupon.value = coupon
  couponCode.value = ''
  toast.success(`${coupon.code} kuponu uygulandı!`)
}

const quickApplyCoupon = (coupon: any) => {
  if (coupon.minAmount && subtotal.value < coupon.minAmount) {
    toast.error(`Bu kupon ${formatPrice(coupon.minAmount)} TL ve üzeri alışverişlerde geçerlidir`)
    return
  }

  appliedCoupon.value = coupon
  toast.success(`${coupon.code} kuponu uygulandı!`)
}

const removeCoupon = () => {
  appliedCoupon.value = null
  toast.info('Kupon kaldırıldı')
}
</script>
