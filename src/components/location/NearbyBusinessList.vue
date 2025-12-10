<template>
  <div>
    <!-- Filters -->
    <div class="flex overflow-x-auto pb-4 gap-2 mb-6 no-scrollbar">
      <button 
        v-for="cat in categories" 
        :key="cat.id"
        @click="activeCategory = cat.id"
        :class="[
          'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors',
          activeCategory === cat.id 
            ? 'bg-blue-600 text-white shadow-md' 
            : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'
        ]"
      >
        {{ cat.label }}
      </button>
    </div>

    <!-- List -->
    <div v-if="filteredBusinesses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <BusinessCard 
        v-for="business in filteredBusinesses" 
        :key="business.id" 
        :business="business" 
      />
    </div>
    
    <!-- Empty State -->
    <div v-else class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
      <p class="text-gray-500">Bu kategoride yakınınızda işletme bulunamadı.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { Business } from '@/types/location'
import BusinessCard from './BusinessCard.vue'

const props = defineProps<{
  businesses: Business[]
}>()

const activeCategory = ref('all')

const categories = [
  { id: 'all', label: 'Tümü' },
  { id: 'restaurant', label: 'Restoranlar' },
  { id: 'hotel', label: 'Oteller' },
  { id: 'store', label: 'Mağazalar' },
  { id: 'gym', label: 'Spor Salonları' }
]

const filteredBusinesses = computed(() => {
  if (activeCategory.value === 'all') return props.businesses
  return props.businesses.filter(b => b.category === activeCategory.value)
})
</script>
