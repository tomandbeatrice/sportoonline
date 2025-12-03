<template>
  <nav class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide py-3">
        <!-- Ana Mağaza -->
        <router-link 
          to="/"
          class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
          :class="isActive('/') 
            ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg shadow-indigo-200' 
            : 'bg-white text-slate-700 border-slate-200 hover:border-indigo-300 hover:bg-indigo-50'"
        >
          <span class="text-xl">🛒</span>
          <span>Mağaza</span>
        </router-link>

        <!-- Yemek -->
        <router-link 
          to="/food"
          class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
          :class="isActive('/food') 
            ? 'bg-orange-500 text-white border-orange-500 shadow-lg shadow-orange-200' 
            : 'bg-white text-slate-700 border-slate-200 hover:border-orange-300 hover:bg-orange-50'"
        >
          <span class="text-xl">🍔</span>
          <span>Yemek</span>
          <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-600 font-bold">Yeni</span>
        </router-link>

        <!-- Otel -->
        <router-link 
          to="/hotels"
          class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
          :class="isActive('/hotels') 
            ? 'bg-blue-500 text-white border-blue-500 shadow-lg shadow-blue-200' 
            : 'bg-white text-slate-700 border-slate-200 hover:border-blue-300 hover:bg-blue-50'"
        >
          <span class="text-xl">🏨</span>
          <span>Otel</span>
        </router-link>

        <!-- Ulaşım -->
        <router-link 
          to="/rides"
          class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
          :class="isActive('/rides') 
            ? 'bg-green-500 text-white border-green-500 shadow-lg shadow-green-200' 
            : 'bg-white text-slate-700 border-slate-200 hover:border-green-300 hover:bg-green-50'"
        >
          <span class="text-xl">🚗</span>
          <span>Ulaşım</span>
        </router-link>

        <!-- Hizmet -->
        <router-link 
          to="/services"
          class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
          :class="isActive('/services') 
            ? 'bg-purple-500 text-white border-purple-500 shadow-lg shadow-purple-200' 
            : 'bg-white text-slate-700 border-slate-200 hover:border-purple-300 hover:bg-purple-50'"
        >
          <span class="text-xl">🔧</span>
          <span>Hizmet</span>
        </router-link>

        <!-- Kariyer -->
        <router-link 
          to="/career"
          class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
          :class="isActive('/career') 
            ? 'bg-amber-500 text-white border-amber-500 shadow-lg shadow-amber-200' 
            : 'bg-white text-slate-700 border-slate-200 hover:border-amber-300 hover:bg-amber-50'"
        >
          <span class="text-xl">💼</span>
          <span>Kariyer</span>
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router'

const route = useRoute()

const isActive = (path: string) => {
  if (path === '/') {
    return route.path === '/'
  }
  return route.path.startsWith(path)
}
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
