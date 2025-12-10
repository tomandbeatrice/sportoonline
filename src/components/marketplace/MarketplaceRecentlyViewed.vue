<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       👁️ SON GÖRÜNTÜLEDİKLERİNİZ - AI Takip Sistemi
       ═══════════════════════════════════════════════════════════════════ -->
  <section v-if="recentlyViewedProducts && recentlyViewedProducts.length > 0" class="recently-viewed py-4">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
        <span class="text-2xl">👁️</span> Son Görüntüledikleriniz
      </h2>
      <span class="text-xs font-medium text-slate-500 flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
        AI ile kişiselleştirilmiş
      </span>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
      <router-link 
        v-for="product in recentlyViewedProducts" 
        :key="product.id" 
        :to="`/products/${product.id}`"
        class="bg-white rounded-xl border border-slate-100 overflow-hidden hover:shadow-md transition-all group"
      >
        <div class="relative h-32 bg-slate-50 flex items-center justify-center overflow-hidden">
          <img 
            v-if="product.image_url" 
            :src="product.image_url" 
            :alt="product.name"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
          />
          <span v-else class="text-4xl">📦</span>
        </div>
        <div class="p-3">
          <h3 class="font-medium text-slate-900 text-sm truncate">{{ product.name }}</h3>
          <p class="text-sm font-bold text-indigo-600 mt-1">{{ formatCurrency(product.price) }}</p>
        </div>
      </router-link>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useTracking } from '@/composables/useTracking'

// State
const recentlyViewedProducts = ref<any[]>([])
const loadingRecentlyViewed = ref(false)

// Composables
const { getRecentViews } = useTracking()

// Helpers
const formatCurrency = (amount: number) => `₺${amount.toFixed(2)}`

// Methods
const fetchRecentlyViewed = async () => {
  loadingRecentlyViewed.value = true
  try {
    const views = await getRecentViews(6)
    recentlyViewedProducts.value = views || []
  } catch (error) {
    console.error('Son görüntülemeler alınamadı:', error)
    recentlyViewedProducts.value = []
  } finally {
    loadingRecentlyViewed.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchRecentlyViewed()
})
</script>
