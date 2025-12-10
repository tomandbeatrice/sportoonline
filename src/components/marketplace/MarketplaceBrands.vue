<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       🌟 ÖNE ÇIKAN MARKALAR - Yeni Özellik
       ═══════════════════════════════════════════════════════════════════ -->
  <section class="featured-brands py-4">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
        <span class="text-2xl">🌟</span> Öne Çıkan Markalar
      </h2>
      <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Tümünü Gör →</a>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
      <div v-for="brand in featuredBrands" :key="brand.id" class="group cursor-pointer">
        <div class="bg-white rounded-xl border border-slate-100 p-6 flex items-center justify-center h-24 shadow-sm group-hover:shadow-md group-hover:border-indigo-100 transition-all">
          <span class="text-xl font-bold text-slate-400 group-hover:text-indigo-600 transition-colors">{{ brand.name }}</span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// State
const featuredBrands = ref([
  { id: 1, name: 'Nike' },
  { id: 2, name: 'Adidas' },
  { id: 3, name: 'Puma' },
  { id: 4, name: 'Under Armour' },
  { id: 5, name: 'Reebok' },
  { id: 6, name: 'New Balance' }
])
</script>
