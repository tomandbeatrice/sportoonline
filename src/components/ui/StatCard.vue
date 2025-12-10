<template>
  <div :class="rootClass" role="group" :aria-label="`Kart: ${label}`" tabindex="0">
    <div class="flex items-center justify-between mb-3">
      <div class="flex items-center gap-3">
        <slot name="icon">
          <span class="text-slate-500 text-sm">{{ label }}</span>
        </slot>
      </div>
      <div class="flex items-center gap-2">
        <slot name="action" />
        <div class="text-2xl">{{ icon }}</div>
      </div>
    </div>

    <p class="text-3xl font-bold" :class="valueClass" aria-live="polite">{{ value }}</p>

    <p class="text-sm mt-1" :class="deltaClass">
      <span v-if="trend === 'up'">↑</span>
      <span v-else-if="trend === 'down'">↓</span>
      <span v-if="delta">{{ delta }}</span>
      <span v-if="hint"> · {{ hint }}</span>
    </p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  label: string
  value: string | number
  icon?: string
  delta?: string
  trend?: 'up' | 'down' | 'neutral'
  hint?: string
  status?: 'neutral' | 'success' | 'warning' | 'danger'
}>()

const rootClass = computed(() => {
  const base = 'rounded-2xl p-6 shadow-sm border flex flex-col justify-between hover:shadow-md transition'
  const statusClass = props.status === 'success' ? 'bg-white border-emerald-100' : props.status === 'warning' ? 'bg-white border-amber-100' : props.status === 'danger' ? 'bg-white border-rose-100' : 'bg-white border-slate-100'
  return `${base} ${statusClass}`
})

const valueClass = computed(() => {
  return 'text-slate-900'
})

const deltaClass = computed(() => {
  if (props.trend === 'up') return 'text-emerald-600'
  if (props.trend === 'down') return 'text-rose-600'
  return 'text-slate-500'
})
</script>
