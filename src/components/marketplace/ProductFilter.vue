<template>
  <div>
    <!-- Mobile Filter Toggle -->
    <div class="lg:hidden mb-4">
      <button 
        @click="isOpen = !isOpen"
        class="flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-sm"
      >
        <span class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
          </svg>
          Filtreler
        </span>
        <span v-if="activeFilterCount > 0" class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-xs text-white">
          {{ activeFilterCount }}
        </span>
        <svg 
          xmlns="http://www.w3.org/2000/svg" 
          class="h-5 w-5 text-gray-400 transition-transform"
          :class="{ 'rotate-180': isOpen }"
          fill="none" viewBox="0 0 24 24" stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
    </div>

    <!-- Filter Content -->
    <aside 
      class="space-y-6 rounded-2xl border border-gray-100 p-5 bg-white transition-all duration-300"
      :class="{ 'hidden lg:block': !isOpen }"
    >
      <!-- Categories -->
      <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Kategori</p>
        <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
          <label class="flex items-center gap-2 cursor-pointer group">
            <input 
              type="radio" 
              :value="null" 
              v-model="localFilters.category"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <span class="text-sm text-gray-700 group-hover:text-blue-600">Tümü</span>
          </label>
          <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-2 cursor-pointer group">
            <input 
              type="radio" 
              :value="cat.id" 
              v-model="localFilters.category"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <span class="text-sm text-gray-700 group-hover:text-blue-600">{{ cat.name }}</span>
          </label>
        </div>
      </div>

      <div class="h-px bg-gray-100"></div>

      <!-- Price Range -->
      <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Fiyat Aralığı</p>
        <div class="flex items-center gap-2">
          <input
            v-model.number="localFilters.minPrice"
            type="number"
            placeholder="Min"
            class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 outline-none"
          />
          <span class="text-gray-400">-</span>
          <input
            v-model.number="localFilters.maxPrice"
            type="number"
            placeholder="Max"
            class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 outline-none"
          />
        </div>
      </div>

      <div class="h-px bg-gray-100"></div>

      <!-- Colors -->
      <div v-if="colors.length">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Renk</p>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="color in colors"
            :key="color"
            @click="toggleColor(color)"
            class="h-8 w-8 rounded-full border-2 transition-all relative"
            :class="localFilters.colors.includes(color) ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-200 hover:border-gray-300'"
            :style="{ backgroundColor: getColorCode(color) }"
            :title="color"
          >
            <span v-if="localFilters.colors.includes(color)" class="absolute inset-0 flex items-center justify-center text-white drop-shadow-md">
              ✓
            </span>
          </button>
        </div>
      </div>

      <!-- Sizes -->
      <div v-if="sizes.length">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Beden</p>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="size in sizes"
            :key="size"
            @click="toggleSize(size)"
            class="min-w-[2.5rem] px-2 h-8 rounded-lg border text-xs font-medium transition-colors"
            :class="localFilters.sizes.includes(size) 
              ? 'bg-blue-600 text-white border-blue-600' 
              : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300'"
          >
            {{ size }}
          </button>
        </div>
      </div>

      <!-- Brands -->
      <div v-if="brands.length">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Marka</p>
        <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
          <label v-for="brand in brands" :key="brand" class="flex items-center gap-2 cursor-pointer group">
            <input 
              type="checkbox" 
              :value="brand" 
              v-model="localFilters.brands"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <span class="text-sm text-gray-700 group-hover:text-blue-600">{{ brand }}</span>
          </label>
        </div>
      </div>

      <button 
        @click="applyFilters"
        class="w-full rounded-xl bg-blue-600 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-200 hover:bg-blue-700 transition-colors"
      >
        Filtreleri Uygula
      </button>
      
      <button 
        @click="resetFilters"
        class="w-full text-xs text-gray-500 hover:text-gray-700 underline"
      >
        Temizle
      </button>
    </aside>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, reactive, computed } from 'vue'

const props = defineProps<{
  categories: any[]
  colors: string[]
  sizes: string[]
  brands: string[]
  initialFilters: any
}>()

const emit = defineEmits(['update:filters'])

const isOpen = ref(false)

const localFilters = reactive({
  category: props.initialFilters.category || null,
  minPrice: props.initialFilters.minPrice || null,
  maxPrice: props.initialFilters.maxPrice || null,
  colors: [...(props.initialFilters.colors || [])],
  sizes: [...(props.initialFilters.sizes || [])],
  brands: [...(props.initialFilters.brands || [])]
})

const activeFilterCount = computed(() => {
  let count = 0
  if (localFilters.category) count++
  if (localFilters.minPrice || localFilters.maxPrice) count++
  count += localFilters.colors.length
  count += localFilters.sizes.length
  count += localFilters.brands.length
  return count
})

function toggleColor(color: string) {
  const index = localFilters.colors.indexOf(color)
  if (index === -1) localFilters.colors.push(color)
  else localFilters.colors.splice(index, 1)
}

function toggleSize(size: string) {
  const index = localFilters.sizes.indexOf(size)
  if (index === -1) localFilters.sizes.push(size)
  else localFilters.sizes.splice(index, 1)
}

function getColorCode(colorName: string) {
  const map: Record<string, string> = {
    'Kırmızı': '#ef4444',
    'Mavi': '#3b82f6',
    'Yeşil': '#22c55e',
    'Siyah': '#171717',
    'Beyaz': '#ffffff',
    'Sarı': '#eab308',
    'Mor': '#a855f7',
    'Gri': '#6b7280',
    'Turuncu': '#f97316'
  }
  return map[colorName] || colorName
}

function applyFilters() {
  emit('update:filters', { ...localFilters })
}

function resetFilters() {
  localFilters.category = null
  localFilters.minPrice = null
  localFilters.maxPrice = null
  localFilters.colors = []
  localFilters.sizes = []
  localFilters.brands = []
  applyFilters()
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
}
</style>
