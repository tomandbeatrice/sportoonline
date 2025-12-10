<template>
  <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <div 
      v-for="stat in stats" 
      :key="stat.id" 
      class="relative overflow-hidden bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition"
    >
      <div class="space-y-1 mb-2">
        <p class="text-xs uppercase tracking-wider text-slate-400">{{ stat.label }}</p>
        <p class="text-3xl font-bold text-slate-900">{{ stat.value }}</p>
      </div>
      <div class="flex items-center justify-between text-sm text-slate-500">
        <span>{{ stat.hint }}</span>
        <span 
          class="flex items-center gap-1 font-medium"
          :class="stat.trend === 'up' ? 'text-emerald-600' : 'text-orange-600'"
        >
          <TrendingUp v-if="stat.trend === 'up'" class="w-4 h-4" />
          <TrendingDown v-else class="w-4 h-4" />
          {{ stat.delta }}
        </span>
      </div>
      <div 
        class="pointer-events-none absolute -right-4 top-1/2 h-16 w-16 -translate-y-1/2 rounded-full opacity-50" 
        :class="stat.trend === 'up' ? 'bg-emerald-100' : 'bg-orange-100'" 
      />
    </div>
  </section>
</template>

<script setup lang="ts">
import { TrendingUp, TrendingDown } from 'lucide-vue-next'

export interface StatItem {
  id: string
  label: string
  value: string
  hint: string
  delta: string
  trend: 'up' | 'down'
}

defineProps<{
  stats: StatItem[]
}>()
</script>
