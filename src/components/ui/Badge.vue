<template>
  <span
    :class="badgeClasses"
    class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
  >
    <span v-if="dot" class="h-1.5 w-1.5 rounded-full" :class="dotColor"></span>
    <slot></slot>
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  variant?: 'default' | 'primary' | 'success' | 'warning' | 'danger' | 'info'
  dot?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
  dot: false
})

const badgeClasses = computed(() => {
  const variants = {
    default: 'bg-slate-100 text-slate-800',
    primary: 'bg-blue-100 text-blue-800',
    success: 'bg-green-100 text-green-800',
    warning: 'bg-yellow-100 text-yellow-800',
    danger: 'bg-red-100 text-red-800',
    info: 'bg-cyan-100 text-cyan-800'
  }
  return variants[props.variant]
})

const dotColor = computed(() => {
  const colors = {
    default: 'bg-slate-500',
    primary: 'bg-blue-500',
    success: 'bg-green-500',
    warning: 'bg-yellow-500',
    danger: 'bg-red-500',
    info: 'bg-cyan-500'
  }
  return colors[props.variant]
})
</script>
