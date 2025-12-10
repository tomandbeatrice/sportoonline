<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       🎨 KOLEKSİYONLAR - Tematik Gruplar
       ═══════════════════════════════════════════════════════════════════ -->
  <section class="collections py-4">
    <h2 class="text-xl font-bold text-slate-900 mb-6">Özel Koleksiyonlar</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div v-for="collection in collections" :key="collection.id" 
        class="relative h-40 rounded-2xl overflow-hidden cursor-pointer group"
        :class="collection.color"
      >
        <div class="absolute inset-0 flex flex-col items-center justify-center p-4 text-center z-10">
          <span class="text-4xl mb-2 transform group-hover:scale-110 transition-transform">{{ collection.image }}</span>
          <h3 class="font-bold text-slate-900">{{ collection.title }}</h3>
          <span class="text-xs font-medium text-slate-600 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">İncele →</span>
        </div>
        <div class="absolute inset-0 bg-white/20 group-hover:bg-white/0 transition-colors"></div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// State
const collections = ref([
  { id: 1, title: 'Yaz Koleksiyonu', image: '🏖️', color: 'bg-orange-100' },
  { id: 2, title: 'Ev Ofis', image: '💻', color: 'bg-blue-100' },
  { id: 3, title: 'Fitness Hedefleri', image: '🏋️', color: 'bg-green-100' },
  { id: 4, title: 'Oyun Dünyası', image: '🎮', color: 'bg-purple-100' }
])
</script>
