<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       ⚡ FLASH SALES - Süreli Fırsatlar
       ═══════════════════════════════════════════════════════════════════ -->
  <section class="flash-sales py-4">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
        <span class="text-2xl">⚡</span> Süper Fırsatlar
        <span class="ml-2 px-2 py-1 bg-red-600 text-white text-xs font-bold rounded">02:14:55</span>
      </h2>
      <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Tümünü Gör →</a>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div 
        v-for="item in flashSales" 
        :key="item.id" 
        class="bg-white rounded-xl border border-slate-100 p-4 hover:shadow-md transition-all cursor-pointer group"
        @click="addToCart(item)"
      >
        <div class="relative h-32 bg-slate-50 rounded-lg flex items-center justify-center text-5xl mb-3 group-hover:scale-105 transition-transform">
          {{ item.image }}
          <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
            -{{ Math.round((1 - item.price / item.oldPrice) * 100) }}%
          </span>
        </div>
        <h3 class="font-semibold text-slate-900 mb-1 truncate">{{ item.title }}</h3>
        <div class="flex items-end gap-2 mb-3">
          <span class="text-lg font-bold text-red-600">{{ formatCurrency(item.price) }}</span>
          <span class="text-sm text-slate-400 line-through mb-0.5">{{ formatCurrency(item.oldPrice) }}</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2 mb-1">
          <div class="bg-red-500 h-2 rounded-full" :style="{ width: (item.sold / item.total * 100) + '%' }"></div>
        </div>
        <div class="text-xs text-slate-500 flex justify-between">
          <span>{{ item.sold }} satıldı</span>
          <span>Stok: {{ item.total - item.sold }}</span>
        </div>
        <button 
          @click.stop="addToCart(item)"
          class="w-full mt-3 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 rounded-lg transition-colors"
        >
          🛒 Sepete Ekle
        </button>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useCartStore } from '@/stores/cartStore'

const cartStore = useCartStore()

// State
const flashSales = ref([
  { id: 'flash-1', name: 'Akıllı Bileklik', title: 'Akıllı Bileklik', price: 450, oldPrice: 600, image: '⌚', sold: 85, total: 100, type: 'product' as const },
  { id: 'flash-2', name: 'Kablosuz Mouse', title: 'Kablosuz Mouse', price: 250, oldPrice: 350, image: '🖱️', sold: 45, total: 120, type: 'product' as const },
  { id: 'flash-3', name: 'Powerbank 20000mAh', title: 'Powerbank 20000mAh', price: 550, oldPrice: 800, image: '🔋', sold: 92, total: 100, type: 'product' as const },
  { id: 'flash-4', name: 'Bluetooth Hoparlör', title: 'Bluetooth Hoparlör', price: 750, oldPrice: 1100, image: '🔊', sold: 20, total: 50, type: 'product' as const }
])

// Add to cart
const addToCart = (item: any) => {
  cartStore.addToCart({
    id: item.id,
    name: item.name,
    price: item.price,
    type: 'product',
    image: item.image
  }, 1)
}

// Helpers
const formatCurrency = (amount: number) => `₺${amount.toFixed(2)}`
</script>
