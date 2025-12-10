<template>
  <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden border border-gray-100">
    <div class="relative h-48">
      <img :src="business.image" :alt="business.name" class="w-full h-full object-cover" />
      <div class="absolute top-3 right-3">
        <span 
          :class="business.isOpen ? 'bg-green-500' : 'bg-red-500'"
          class="px-2 py-1 text-xs font-bold text-white rounded-full"
        >
          {{ business.isOpen ? 'AÇIK' : 'KAPALI' }}
        </span>
      </div>
      <div class="absolute bottom-3 left-3">
        <span class="px-2 py-1 bg-white/90 text-gray-800 text-xs font-bold rounded-lg shadow-sm">
          {{ business.distance }} km
        </span>
      </div>
    </div>
    
    <div class="p-4">
      <div class="flex justify-between items-start mb-2">
        <div>
          <h3 class="text-lg font-bold text-gray-900">{{ business.name }}</h3>
          <p class="text-sm text-gray-500">{{ business.address }}</p>
        </div>
        <div class="flex items-center bg-yellow-50 px-2 py-1 rounded-lg">
          <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
          <span class="font-bold text-gray-700 text-sm">{{ business.rating }}</span>
          <span class="text-xs text-gray-400 ml-1">({{ business.reviewCount }})</span>
        </div>
      </div>

      <div class="flex flex-wrap gap-2 mb-4">
        <span v-for="tag in business.tags" :key="tag" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-md">
          {{ tag }}
        </span>
      </div>

      <div class="flex items-center justify-between pt-3 border-t border-gray-50">
        <div class="text-sm text-gray-600">
          <span v-if="business.deliveryTime" class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ business.deliveryTime }}
          </span>
        </div>
        <div class="text-sm font-bold text-gray-900">
          {{ business.priceRange }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Business } from '@/types/location'

defineProps<{
  business: Business
}>()
</script>
