<template>
  <div class="group relative flex cursor-pointer items-center gap-2 rounded-full bg-gradient-to-r from-amber-100 to-orange-100 px-3 py-1.5 transition-all hover:shadow-md">
    <div class="flex items-center justify-center rounded-full bg-orange-500 p-1 text-white">
      <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
      </svg>
    </div>
    
    <div class="flex flex-col">
      <span class="text-[10px] font-bold text-orange-800 leading-none">{{ store.points }} Puan</span>
      <span class="text-[9px] text-orange-600/80 leading-none">Seviye {{ store.level }}</span>
    </div>

    <!-- Dropdown / Tooltip -->
    <div class="absolute right-0 top-full z-50 mt-2 hidden w-48 rounded-xl border border-orange-100 bg-white p-3 shadow-xl group-hover:block">
      <div class="mb-2 flex justify-between text-xs font-medium text-slate-600">
        <span>Sonraki Seviye</span>
        <span>{{ store.nextLevelPoints - store.points }} puan kaldı</span>
      </div>
      <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
        <div class="h-full bg-orange-500 transition-all duration-500" :style="{ width: store.progress + '%' }"></div>
      </div>
      <div class="mt-3 grid grid-cols-4 gap-1">
        <div 
          v-for="badge in store.badges" 
          :key="badge.id"
          class="flex aspect-square items-center justify-center rounded-lg bg-slate-50 text-lg"
          :class="{ 'opacity-40 grayscale': !badge.unlocked }"
          :title="badge.name"
        >
          {{ badge.icon }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useGamificationStore } from '@/stores/gamificationStore'

const store = useGamificationStore()
</script>
