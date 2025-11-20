<template>
  <div class="inline-flex items-center gap-3 rounded-xl bg-slate-100 p-1">
    <button
      v-for="option in options"
      :key="option.value"
      @click="select(option.value)"
      :class="buttonClasses(option.value)"
      :disabled="disabled"
    >
      <component v-if="option.icon" :is="option.icon" class="h-4 w-4" />
      {{ option.label }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface SegmentOption {
  label: string
  value: string | number
  icon?: any
}

interface Props {
  modelValue: string | number
  options: SegmentOption[]
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
  change: [value: string | number]
}>()

const buttonClasses = (value: string | number) => {
  const base = 'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200'
  const active = 'bg-white text-slate-900 shadow-sm'
  const inactive = 'text-slate-600 hover:text-slate-900'
  const disabledClass = props.disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
  
  return [
    base,
    props.modelValue === value ? active : inactive,
    disabledClass
  ].join(' ')
}

const select = (value: string | number) => {
  if (props.disabled) return
  emit('update:modelValue', value)
  emit('change', value)
}
</script>
