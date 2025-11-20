<template>
  <div class="flex items-start gap-3 rounded-2xl border px-4 py-3" :class="variantClasses">
    <div class="flex h-8 w-8 items-center justify-center rounded-xl border" :class="iconClasses">
      <slot name="icon">⚡</slot>
    </div>
    <div class="space-y-1 text-sm">
      <p class="font-semibold">{{ title }}</p>
      <p class="text-slate-500">
        <slot />
      </p>
    </div>
    <slot name="action" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    title: string
    variant?: 'default' | 'emerald' | 'orange'
  }>(),
  { variant: 'default' }
)

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'emerald':
      return 'border-emerald-100 bg-emerald-50'
    case 'orange':
      return 'border-orange-100 bg-orange-50'
    default:
      return 'border-slate-100 bg-white'
  }
})

const iconClasses = computed(() => {
  switch (props.variant) {
    case 'emerald':
      return 'border-emerald-200 text-emerald-600'
    case 'orange':
      return 'border-orange-200 text-orange-600'
    default:
      return 'border-slate-200 text-slate-600'
  }
})
</script>
