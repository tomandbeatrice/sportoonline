<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       🎁 BUNDLE OFFERS - Paket Teklifleri
       ═══════════════════════════════════════════════════════════════════ -->
  <section class="bundle-offers py-4" ref="bundlesSection">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
        <span class="text-2xl">🎁</span> {{ t('product.bundleOffers') }}
      </h2>
      <div class="flex gap-2">
        <button 
          @click="scrollBundles('left')"
          class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 hover:border-indigo-300 transition-colors"
        >
          ←
        </button>
        <button 
          @click="scrollBundles('right')"
          class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 hover:border-indigo-300 transition-colors"
        >
          →
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loadingBundles" class="flex gap-4 overflow-hidden">
      <div v-for="i in 3" :key="i" class="flex-shrink-0 w-[320px] md:w-[380px] bg-white rounded-2xl border border-slate-100 p-4">
        <div class="flex items-center gap-4 mb-4">
          <div class="w-20 h-20 bg-slate-200 rounded-xl animate-pulse"></div>
          <div class="text-2xl text-slate-300">+</div>
          <div class="w-20 h-20 bg-slate-200 rounded-xl animate-pulse"></div>
        </div>
        <div class="flex-1 space-y-3">
          <div class="h-4 bg-slate-200 rounded w-3/4"></div>
          <div class="h-4 bg-slate-200 rounded w-1/2"></div>
          <div class="h-8 bg-slate-200 rounded w-full mt-4"></div>
        </div>
      </div>
    </div>

    <!-- Bundle Cards - Horizontal Carousel -->
    <div 
      v-else-if="bundles.length > 0" 
      ref="bundleCarousel"
      class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-hide"
      style="scroll-behavior: smooth;"
    >
      <div 
        v-for="bundle in bundles"
        :key="bundle.id"
        class="bundle-card flex-shrink-0 w-[320px] md:w-[380px] snap-start bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-all group"
      >
        <!-- Bundle Header -->
        <div class="relative bg-gradient-to-r from-indigo-50 to-purple-50 p-4">
          <span class="absolute top-3 right-3 px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
            %{{ bundle.discount }} {{ t('product.discount') }}
          </span>
          
          <div class="flex items-center gap-3">
            <img 
              :src="bundle.main?.image" 
              :alt="bundle.main?.title"
              class="w-20 h-20 rounded-xl object-cover shadow-md group-hover:scale-105 transition-transform"
              loading="lazy"
            />
            <div class="text-2xl font-bold text-indigo-600">+</div>
            <img 
              :src="bundle.extra?.image" 
              :alt="bundle.extra?.title"
              class="w-20 h-20 rounded-xl object-cover shadow-md group-hover:scale-105 transition-transform"
              loading="lazy"
            />
          </div>
        </div>

        <!-- Bundle Content -->
        <div class="p-4">
          <h3 class="font-semibold text-slate-900 mb-1">
            {{ bundle.main?.title }} + {{ bundle.extra?.title }}
          </h3>
          <p class="text-sm text-slate-500 mb-3">
            {{ bundle.description || t('bundle.saveMoreTogether') }}
          </p>

          <div class="flex items-center justify-between">
            <div>
              <span class="text-2xl font-bold text-indigo-600">
                {{ formatCurrency(bundle.totalPrice) }}
              </span>
              <span class="text-sm text-slate-400 line-through ml-2">
                {{ formatCurrency(bundle.originalPrice) }}
              </span>
            </div>
            
            <button 
              @click="addBundleToCart(bundle)"
              :disabled="addingBundleId === bundle.id"
              class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors"
            >
              <svg v-if="addingBundleId === bundle.id" class="w-4 h-4 animate-spin" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              {{ t('product.addBundle') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 bg-white rounded-2xl">
      <div class="text-4xl mb-3">📦</div>
      <p class="text-slate-500">{{ t('bundle.noBundles') }}</p>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import type { Bundle } from '@/types/marketplace'

// State
const bundles = ref<Bundle[]>([])
const loadingBundles = ref(true)
const addingBundleId = ref<number | null>(null)
const bundlesSection = ref<HTMLElement | null>(null)
const bundleCarousel = ref<HTMLElement | null>(null)

const { t } = useI18n()
const formatCurrency = (amount: number) => `₺${amount.toFixed(2)}`

// Methods
const scrollBundles = (direction: 'left' | 'right') => {
  if (!bundleCarousel.value) return
  const scrollAmount = 400
  bundleCarousel.value.scrollBy({
    left: direction === 'right' ? scrollAmount : -scrollAmount,
    behavior: 'smooth'
  })
}

const addBundleToCart = async (bundle: Bundle) => {
  addingBundleId.value = bundle.id
  try {
    // Simulate adding to cart
    await new Promise(resolve => setTimeout(resolve, 500))
    // Emit event to parent to update cart count if needed, or use store
    // For now just console log
    console.log('Bundle added to cart:', bundle.id)
  } catch (error) {
    console.error('Bundle eklenemedi:', error)
  } finally {
    addingBundleId.value = null
  }
}

const loadBundles = async () => {
  loadingBundles.value = true
  try {
    const response = await fetch('/api/marketplace/bundles')
    if (response.ok) {
      const data = await response.json()
      // Map backend data to frontend Bundle type if needed
      bundles.value = data.map((b: any) => ({
        id: b.id,
        discount: Math.round((1 - b.price / b.original_price) * 100),
        main: { image: b.image, title: b.products[0] || 'Ürün 1', price: b.original_price / 2 }, // Mock split
        extra: { image: b.image, title: b.products[1] || 'Ürün 2', price: b.original_price / 2 },
        description: b.description,
        totalPrice: b.price,
        originalPrice: b.original_price
      }))
    }
  } catch (error) {
    console.error('Bundlelar yüklenemedi:', error)
  } finally {
    loadingBundles.value = false
  }
}

onMounted(() => {
  loadBundles()
})
</script>

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
