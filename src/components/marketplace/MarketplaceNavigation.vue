<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       🔗 HİZMET SEKMELERİ - Mağaza, Yemek, Otel, Hizmet, Turbo
       ═══════════════════════════════════════════════════════════════════ -->
  <nav class="bg-white border-b border-slate-200 sticky top-[120px] sm:top-[140px] lg:top-[130px] z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="flex flex-nowrap items-center gap-2 overflow-x-auto scrollbar-hide py-3">
        <!-- Ana Mağaza -->
        <router-link 
          to="/"
          class="flex-shrink-0 flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
          :class="$route.path === '/' 
            ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg shadow-indigo-200' 
            : 'bg-white text-slate-700 border-slate-200 hover:border-indigo-300 hover:bg-indigo-50'"
        >
          <span class="text-xl">🛒</span>
          <span>Mağaza</span>
        </router-link>

        <!-- Yemek -->
        <router-link 
          to="/food"
          class="flex-shrink-0 flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
          :class="$route.path === '/food' 
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
          class="flex-shrink-0 flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
          :class="$route.path.startsWith('/hotels') 
            ? 'bg-blue-500 text-white border-blue-500 shadow-lg shadow-blue-200' 
            : 'bg-white text-slate-700 border-slate-200 hover:border-blue-300 hover:bg-blue-50'"
        >
          <span class="text-xl">🏨</span>
          <span>Otel</span>
        </router-link>

        <!-- Hizmet -->
        <router-link 
          to="/services"
          class="flex-shrink-0 flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
          :class="$route.path === '/services' 
            ? 'bg-purple-500 text-white border-purple-500 shadow-lg shadow-purple-200' 
            : 'bg-white text-slate-700 border-slate-200 hover:border-purple-300 hover:bg-purple-50'"
        >
          <span class="text-xl">🔧</span>
          <span>Hizmet</span>
        </router-link>

        <!-- Turbo Mod -->
        <router-link 
          to="/turbo"
          class="flex-shrink-0 flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
          :class="$route.path === '/turbo' 
            ? 'bg-gradient-to-r from-violet-500 to-purple-600 text-white border-violet-500 shadow-lg shadow-violet-200' 
            : 'bg-white text-slate-700 border-slate-200 hover:border-violet-300 hover:bg-violet-50'"
        >
          <span class="text-xl">🚀</span>
          <span>Turbo</span>
          <span class="px-2 py-0.5 text-xs rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold animate-pulse">CANLI</span>
        </router-link>
      </div>
    </div>
  </nav>

  <!-- Currency Selector -->
  <div class="bg-slate-50 border-b border-slate-200 sticky top-[180px] sm:top-[200px] z-39">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2">
      <CurrencySelector />
    </div>
  </div>
</template>

<script setup lang="ts">
import CurrencySelector from '@/components/common/CurrencySelector.vue'
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
