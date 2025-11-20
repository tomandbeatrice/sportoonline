<template>
  <div class="inline-flex items-center gap-2">
    <input
      :id="id"
      type="checkbox"
      :checked="modelValue"
      :disabled="disabled"
      @change="handleChange"
      class="peer sr-only"
    />
    <label
      :for="id"
      class="relative inline-flex h-6 w-11 cursor-pointer items-center rounded-full transition-colors"
      :class="[
        modelValue ? 'bg-blue-600' : 'bg-slate-300',
        disabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-opacity-90'
      ]"
    >
      <span
        class="inline-block h-4 w-4 transform rounded-full bg-white shadow-lg transition-transform"
        :class="modelValue ? 'translate-x-6' : 'translate-x-1'"
      ></span>
    </label>
    <span v-if="label" class="text-sm font-medium text-slate-700">
      {{ label }}
    </span>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  modelValue: boolean
  label?: string
  disabled?: boolean
  id?: string
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
  id: () => `toggle-${Math.random().toString(36).slice(2, 9)}`
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  change: [value: boolean]
}>()

const handleChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.checked)
  emit('change', target.checked)
}
</script>
