<template>
  <div class="relative h-[calc(100vh-4rem)] w-full overflow-hidden bg-slate-100">
    <!-- Map Placeholder -->
    <div class="absolute inset-0 bg-[#e5e7eb] opacity-50" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 20px 20px;"></div>
    
    <!-- Simulated Map Content -->
    <div class="absolute inset-0">
      <!-- Markers -->
      <div 
        v-for="place in places" 
        :key="place.id"
        class="absolute cursor-pointer transition-transform hover:scale-110"
        :style="{ top: place.y + '%', left: place.x + '%' }"
        @click="selectedPlace = place"
      >
        <div 
          class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-white shadow-lg"
          :class="getMarkerColor(place.type)"
        >
          <span class="text-lg">{{ getMarkerIcon(place.type) }}</span>
        </div>
      </div>
    </div>

    <!-- Search Bar Overlay -->
    <div class="absolute left-4 right-4 top-4 z-10">
      <div class="flex items-center rounded-xl bg-white p-3 shadow-lg">
        <svg class="mr-3 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input 
          type="text" 
          placeholder="Restoran, market veya araç ara..." 
          class="w-full bg-transparent text-sm outline-none placeholder:text-slate-400"
        />
      </div>
      
      <!-- Filter Chips -->
      <div class="mt-3 flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
        <button 
          v-for="filter in filters" 
          :key="filter.id"
          class="whitespace-nowrap rounded-full bg-white px-4 py-2 text-xs font-medium shadow-sm transition-colors hover:bg-slate-50"
          :class="activeFilter === filter.id ? 'ring-2 ring-blue-500 text-blue-600' : 'text-slate-600'"
          @click="activeFilter = filter.id"
        >
          {{ filter.label }}
        </button>
      </div>
    </div>

    <!-- Bottom Sheet Detail -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="translate-y-full"
      enter-to-class="translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="translate-y-0"
      leave-to-class="translate-y-full"
    >
      <div 
        v-if="selectedPlace" 
        class="absolute bottom-0 left-0 right-0 z-20 rounded-t-3xl bg-white p-6 shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.1)]"
      >
        <div class="mb-4 flex items-start justify-between">
          <div>
            <h3 class="text-lg font-bold text-slate-900">{{ selectedPlace.name }}</h3>
            <p class="text-sm text-slate-500">{{ selectedPlace.address }}</p>
            <div class="mt-2 flex items-center gap-2">
              <span class="flex items-center text-xs font-medium text-green-600">
                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                {{ selectedPlace.rating }}
              </span>
              <span class="text-xs text-slate-400">• {{ selectedPlace.distance }}</span>
              <span class="text-xs text-slate-400">• {{ selectedPlace.deliveryTime }}</span>
            </div>
          </div>
          <button 
            @click="selectedPlace = null"
            class="rounded-full bg-slate-100 p-2 text-slate-400 hover:bg-slate-200"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="flex gap-3">
          <button class="flex-1 rounded-xl bg-blue-600 py-3 text-sm font-bold text-white shadow-lg shadow-blue-200 transition-transform active:scale-95">
            Sipariş Ver
          </button>
          <button class="flex-1 rounded-xl border border-slate-200 py-3 text-sm font-bold text-slate-700 transition-colors hover:bg-slate-50">
            Yol Tarifi
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const activeFilter = ref('all')
const selectedPlace = ref(null)

const filters = [
  { id: 'all', label: 'Tümü' },
  { id: 'food', label: 'Yemek' },
  { id: 'market', label: 'Market' },
  { id: 'taxi', label: 'Taksi' },
]

const places = [
  { id: 1, name: 'Burger King', type: 'food', x: 20, y: 30, rating: 4.5, distance: '1.2 km', deliveryTime: '20-30 dk', address: 'Atatürk Cad. No:12' },
  { id: 2, name: 'Migros Jet', type: 'market', x: 50, y: 50, rating: 4.8, distance: '0.5 km', deliveryTime: '15-20 dk', address: 'Cumhuriyet Meydanı' },
  { id: 3, name: 'Taksi Durağı', type: 'taxi', x: 70, y: 20, rating: 4.9, distance: '2 dk', deliveryTime: 'Hemen', address: 'Merkez Durak' },
  { id: 4, name: 'Starbucks', type: 'food', x: 40, y: 70, rating: 4.7, distance: '2.5 km', deliveryTime: '30-40 dk', address: 'AVM Girişi' },
  { id: 5, name: 'Şok Market', type: 'market', x: 80, y: 60, rating: 4.2, distance: '0.8 km', deliveryTime: '10-15 dk', address: 'Lale Sokak' },
]

const getMarkerColor = (type: string) => {
  switch (type) {
    case 'food': return 'bg-orange-500 text-white'
    case 'market': return 'bg-green-500 text-white'
    case 'taxi': return 'bg-yellow-400 text-black'
    default: return 'bg-blue-500 text-white'
  }
}

const getMarkerIcon = (type: string) => {
  switch (type) {
    case 'food': return '🍔'
    case 'market': return '🛒'
    case 'taxi': return '🚕'
    default: return '📍'
  }
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
