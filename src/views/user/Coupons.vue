<template>
  <div class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-5xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Kuponlarım</h1>
        <p class="text-slate-500">{{ activeCoupons.length }} aktif kupon</p>
      </div>

      <!-- Add Coupon Section -->
      <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 mb-6 text-white">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
          <div>
            <h2 class="text-xl font-bold mb-1">🎁 Kupon Kodunuz Var mı?</h2>
            <p class="text-green-50 text-sm">Kupon kodunuzu girerek avantajlardan yararlanın</p>
          </div>
          <div class="flex gap-2 w-full md:w-auto">
            <input 
              v-model="couponCode"
              type="text" 
              placeholder="KUPON KODU" 
              class="px-4 py-3 rounded-xl text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-white uppercase w-full md:w-64"
              @keyup.enter="addCoupon"
            >
            <button 
              @click="addCoupon"
              class="px-6 py-3 bg-white text-green-600 font-semibold rounded-xl hover:bg-green-50 transition whitespace-nowrap"
            >
              Ekle
            </button>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="bg-white rounded-2xl shadow-sm mb-6 border border-slate-200">
        <div class="border-b border-slate-100 px-6">
          <nav class="flex gap-6">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'py-4 text-sm font-medium border-b-2 transition-colors',
                activeTab === tab.id
                  ? 'border-green-600 text-green-600'
                  : 'border-transparent text-slate-500 hover:text-slate-700'
              ]"
            >
              {{ tab.icon }} {{ tab.name }} ({{ getCouponCount(tab.id) }})
            </button>
          </nav>
        </div>

        <!-- Active Coupons -->
        <div v-if="activeTab === 'active'" class="p-6">
          <div v-if="activeCoupons.length === 0" class="text-center py-12">
            <span class="text-5xl mb-4 block">🎫</span>
            <p class="text-slate-500">Aktif kuponunuz yok</p>
          </div>
          <div v-else class="space-y-4">
            <CouponCard 
              v-for="coupon in activeCoupons" 
              :key="coupon.id"
              :coupon="coupon"
              @use="useCoupon"
              @details="showCouponDetails"
            />
          </div>
        </div>

        <!-- Used Coupons -->
        <div v-if="activeTab === 'used'" class="p-6">
          <div v-if="usedCoupons.length === 0" class="text-center py-12">
            <span class="text-5xl mb-4 block">📋</span>
            <p class="text-slate-500">Henüz kullanılmış kupon yok</p>
          </div>
          <div v-else class="space-y-4">
            <CouponCard 
              v-for="coupon in usedCoupons" 
              :key="coupon.id"
              :coupon="coupon"
              :used="true"
              @details="showCouponDetails"
            />
          </div>
        </div>

        <!-- Expired Coupons -->
        <div v-if="activeTab === 'expired'" class="p-6">
          <div v-if="expiredCoupons.length === 0" class="text-center py-12">
            <span class="text-5xl mb-4 block">⏰</span>
            <p class="text-slate-500">Süresi dolmuş kupon yok</p>
          </div>
          <div v-else class="space-y-4">
            <CouponCard 
              v-for="coupon in expiredCoupons" 
              :key="coupon.id"
              :coupon="coupon"
              :expired="true"
              @details="showCouponDetails"
            />
          </div>
        </div>
      </div>

      <!-- Discover Coupons -->
      <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200">
        <h3 class="font-bold text-lg text-slate-900 mb-4">🌟 Sizin İçin Önerilen Kuponlar</h3>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="coupon in recommendedCoupons" 
            :key="coupon.id"
            class="border border-slate-200 rounded-xl p-4 hover:border-green-500 hover:shadow-md transition cursor-pointer"
            @click="claimCoupon(coupon)"
          >
            <div class="flex items-start justify-between mb-3">
              <div class="text-3xl">{{ coupon.icon }}</div>
              <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full">
                {{ coupon.discount }}
              </span>
            </div>
            <h4 class="font-semibold text-slate-900 mb-1">{{ coupon.title }}</h4>
            <p class="text-xs text-slate-500 mb-3">{{ coupon.description }}</p>
            <button class="w-full py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
              Al
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useToast } from 'vue-toastification'

const toast = useToast()

const couponCode = ref('')
const activeTab = ref('active')

const tabs = [
  { id: 'active', name: 'Aktif Kuponlar', icon: '🎫' },
  { id: 'used', name: 'Kullanılanlar', icon: '✅' },
  { id: 'expired', name: 'Süresi Dolmuş', icon: '⏰' }
]

const coupons = ref([
  {
    id: 1,
    code: 'WELCOME50',
    title: 'Hoşgeldin İndirimi',
    discount: '50 TL',
    description: '500 TL üzeri alışverişlerde geçerli',
    minAmount: 500,
    expiryDate: '2025-12-31',
    status: 'active',
    type: 'fixed',
    icon: '🎁',
    terms: ['Minimum 500 TL alışveriş', 'Tek kullanımlık', 'Tüm kategorilerde geçerli']
  },
  {
    id: 2,
    code: 'SPORT20',
    title: '%20 Spor Ürünleri',
    discount: '%20',
    description: 'Spor kategorisinde geçerli',
    minAmount: 0,
    expiryDate: '2025-12-15',
    status: 'active',
    type: 'percentage',
    icon: '⚽',
    terms: ['Sadece spor kategorisi', 'Maksimum 200 TL indirim', 'İndirimli ürünler dahil değil']
  },
  {
    id: 3,
    code: 'FREE SHIPPING',
    title: 'Ücretsiz Kargo',
    discount: 'Kargo Bedava',
    description: 'Tüm siparişlerde geçerli',
    minAmount: 0,
    expiryDate: '2025-12-20',
    status: 'active',
    type: 'shipping',
    icon: '🚚',
    terms: ['Kargo bedeli yansıtılmaz', 'Tüm kargo firmaları']
  },
  {
    id: 4,
    code: 'BLACKFRIDAY',
    title: 'Black Friday',
    discount: '%30',
    description: 'Kampanya sona erdi',
    minAmount: 1000,
    expiryDate: '2025-11-30',
    status: 'used',
    usedDate: '2025-11-29',
    type: 'percentage',
    icon: '🖤',
    terms: ['Minimum 1000 TL', 'Tek kullanımlık']
  },
  {
    id: 5,
    code: 'SUMMER25',
    title: 'Yaz İndirimi',
    discount: '%25',
    description: 'Geçerlilik süresi doldu',
    minAmount: 300,
    expiryDate: '2025-09-30',
    status: 'expired',
    type: 'percentage',
    icon: '☀️',
    terms: ['Minimum 300 TL', 'Yaz ürünleri']
  }
])

const recommendedCoupons = ref([
  {
    id: 101,
    code: 'NEW USER100',
    title: '100 TL İndirim',
    discount: '100 TL',
    description: 'İlk siparişinizde 1000 TL üzeri',
    icon: '🎉',
    minAmount: 1000
  },
  {
    id: 102,
    code: 'WEEKEND15',
    title: 'Hafta Sonu %15',
    discount: '%15',
    description: 'Cumartesi-Pazar geçerli',
    icon: '🎊',
    minAmount: 500
  },
  {
    id: 103,
    code: 'LOYALTY50',
    title: 'Sadakat Kuponu',
    discount: '50 TL',
    description: 'Sadık müşterilere özel',
    icon: '💎',
    minAmount: 300
  }
])

const activeCoupons = computed(() => coupons.value.filter(c => c.status === 'active'))
const usedCoupons = computed(() => coupons.value.filter(c => c.status === 'used'))
const expiredCoupons = computed(() => coupons.value.filter(c => c.status === 'expired'))

const getCouponCount = (tab: string) => {
  if (tab === 'active') return activeCoupons.value.length
  if (tab === 'used') return usedCoupons.value.length
  if (tab === 'expired') return expiredCoupons.value.length
  return 0
}

const addCoupon = () => {
  if (!couponCode.value.trim()) {
    toast.error('Lütfen bir kupon kodu girin')
    return
  }
  
  // Simulate API call
  toast.success(`${couponCode.value} kuponu eklendi!`)
  couponCode.value = ''
}

const useCoupon = (coupon: any) => {
  toast.success(`${coupon.code} kuponu sepetinizde aktif`)
  // Redirect to cart with coupon
}

const showCouponDetails = (coupon: any) => {
  toast.info(`${coupon.title} - ${coupon.description}`)
}

const claimCoupon = (coupon: any) => {
  coupons.value.push({
    ...coupon,
    id: Date.now(),
    status: 'active',
    expiryDate: '2025-12-31',
    terms: ['Yeni kazanılan kupon', 'Tek kullanımlık']
  })
  toast.success(`${coupon.code} kuponu hesabınıza eklendi!`)
}
</script>
