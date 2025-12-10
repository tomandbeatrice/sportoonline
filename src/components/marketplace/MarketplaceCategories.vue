<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       🔥 POPÜLER KATEGORİLER - Yeni Özellik
       ═══════════════════════════════════════════════════════════════════ -->
  <section class="py-8 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
        <span class="text-xl">🔥</span> Popüler Kategoriler
      </h2>
      <div class="flex gap-6 overflow-x-auto pb-4 scrollbar-hide">
        <router-link 
          v-for="cat in popularCategories" 
          :key="cat.id"
          :to="cat.link || '/search?category=' + cat.id"
          class="flex flex-col items-center gap-3 min-w-[80px] group"
        >
          <div class="w-20 h-20 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-3xl shadow-sm group-hover:shadow-md group-hover:scale-110 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-all duration-300">
            {{ cat.icon }}
          </div>
          <span class="text-sm font-medium text-slate-600 group-hover:text-indigo-600 text-center leading-tight">
            {{ cat.name }}
          </span>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// State
const popularCategories = ref([
  { id: 1, name: 'Elektronik', icon: '🎧', itemCount: '1.2k+', color: 'bg-blue-100 text-blue-600', link: '/category/electronics' },
  { id: 2, name: 'Giyim', icon: '👕', itemCount: '3.4k+', color: 'bg-purple-100 text-purple-600', link: '/category/clothing' },
  { id: 3, name: 'Spor', icon: '⚽', itemCount: '850+', color: 'bg-green-100 text-green-600', link: '/category/sports' },
  { id: 4, name: 'Ev & Yaşam', icon: '🏠', itemCount: '2.1k+', color: 'bg-orange-100 text-orange-600', link: '/category/home' },
  { id: 5, name: 'Kozmetik', icon: '💄', itemCount: '900+', color: 'bg-pink-100 text-pink-600', link: '/category/beauty' },
  { id: 6, name: 'Kitap', icon: '📚', itemCount: '5.6k+', color: 'bg-yellow-100 text-yellow-600', link: '/category/books' },
  { id: 7, name: 'Oyun', icon: '🎮', itemCount: '450+', color: 'bg-indigo-100 text-indigo-600', link: '/category/gaming' },
  { id: 8, name: 'Aksesuar', icon: '⌚', itemCount: '1.5k+', color: 'bg-teal-100 text-teal-600', link: '/category/accessories' }
])
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
