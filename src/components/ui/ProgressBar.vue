<template>
  <div class="relative">
    <div class="overflow-hidden rounded-full bg-slate-200 h-2">
      <div
        class="h-full rounded-full transition-all duration-300 ease-out"
        :class="colorClass"
        :style="{ width: `${percentage}%` }"
      ></div>
    </div>
    <span v-if="showLabel" class="mt-1 text-xs text-slate-600">
      {{ label || `${percentage}%` }}
    </span>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  value: number
  max?: number
  color?: 'blue' | 'green' | 'red' | 'yellow' | 'purple'
  showLabel?: boolean
  label?: string
}

const props = withDefaults(defineProps<Props>(), {
  max: 100,
  color: 'blue',
  showLabel: false
})

const percentage = computed(() => {
  return Math.min(Math.max((props.value / props.max) * 100, 0), 100)
})

const colorClass = computed(() => {
  const colors = {
    blue: 'bg-blue-600',
    green: 'bg-green-600',
    red: 'bg-red-600',
    yellow: 'bg-yellow-500',
    purple: 'bg-purple-600'
  }
  return colors[props.color]
})
</script>
