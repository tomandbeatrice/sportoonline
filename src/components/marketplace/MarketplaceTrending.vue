<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       🔥 TREND ÜRÜNLER - Yeni Özellik
       ═══════════════════════════════════════════════════════════════════ -->
  <section class="trending-products py-4">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
        <span class="text-2xl">🔥</span> Trend Ürünler
      </h2>
      <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Tümünü Gör →</a>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="product in trendingProducts" :key="product.id" class="bg-white rounded-xl border border-slate-100 overflow-hidden hover:shadow-md transition-all group">
        <div class="relative h-48 bg-slate-50 flex items-center justify-center text-6xl group-hover:scale-105 transition-transform duration-500">
          {{ product.image }}
          <span v-if="product.discount > 0" class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
            %{{ product.discount }}
          </span>
        </div>
        <div class="p-4">
          <div class="flex items-center gap-1 mb-2">
            <span class="text-yellow-400 text-sm">⭐</span>
            <span class="text-sm font-medium text-slate-700">{{ product.rating }}</span>
            <span class="text-xs text-slate-400">({{ product.reviews }})</span>
          </div>
          <h3 class="font-semibold text-slate-900 mb-2 line-clamp-2">{{ product.title }}</h3>
          <div class="flex items-center justify-between">
            <span class="text-lg font-bold text-indigo-600">{{ formatPrice(product.price) }}</span>
            <button class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-colors">
              +
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { formatCurrencyWithConversion } from '@/utils/currencyConverter'

// State
const trendingProducts = ref([
  { id: 1, title: 'Akıllı Saat Series 7', price: 4500, image: '⌚', rating: 4.8, reviews: 124, discount: 10 },
  { id: 2, title: 'Koşu Ayakkabısı Pro', price: 2100, image: '👟', rating: 4.6, reviews: 89, discount: 0 },
  { id: 3, title: 'Yoga Matı Premium', price: 450, image: '🧘‍♀️', rating: 4.9, reviews: 256, discount: 15 },
  { id: 4, title: 'Bluetooth Kulaklık', price: 1200, image: '🎧', rating: 4.5, reviews: 67, discount: 5 }
])

// Helpers - Gerçek zamanlı kur dönüşümü ile
const formatPrice = (amount: number) => formatCurrencyWithConversion(amount, 'TRY', 2)
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
