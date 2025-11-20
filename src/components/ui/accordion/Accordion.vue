<template>
  <div :class="['space-y-3', props.class]">
    <slot />
  </div>
</template>

<script setup lang="ts">
import { provide, ref, watch } from 'vue'
import type { AccordionType } from './tokens'
import { accordionKey } from './tokens'

const props = withDefaults(
  defineProps<{
    type?: AccordionType
    collapsible?: boolean
    defaultValue?: string | string[]
    class?: string
  }>(),
  {
    type: 'single',
    collapsible: true,
    defaultValue: undefined,
    class: ''
  }
)

const openValues = ref<string[]>([])

watch(
  () => props.defaultValue,
  value => {
    if (!value) {
      openValues.value = []
      return
    }
    openValues.value = Array.isArray(value) ? value : [value]
  },
  { immediate: true }
)

function toggle(value: string) {
  const current = openValues.value
  const isOpen = current.includes(value)

  if (props.type === 'multiple') {
    openValues.value = isOpen ? current.filter(item => item !== value) : [...current, value]
    return
  }

  if (isOpen) {
    if (props.collapsible) {
      openValues.value = []
    }
    return
  }
  openValues.value = [value]
}

provide(accordionKey, { type: props.type, openValues, toggle })
</script>
