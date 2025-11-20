<template>
  <button
    type="button"
    class="flex w-full items-center justify-between gap-4 text-left text-sm font-semibold text-slate-900"
    :class="props.class"
    @click="handleClick"
  >
    <span><slot /></span>
    <svg
      class="h-4 w-4 text-slate-400 transition"
      :class="isOpen ? 'rotate-90' : ''"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="1.5"
    >
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
    </svg>
  </button>
</template>

<script setup lang="ts">
import { computed, inject } from 'vue'
import { accordionItemKey, accordionKey } from './tokens'

const props = withDefaults(defineProps<{ class?: string }>(), { class: '' })

const accordion = inject(accordionKey)
const itemValue = inject(accordionItemKey)

const isOpen = computed(() => {
  if (!accordion || !itemValue) return false
  return accordion.openValues.value.includes(itemValue)
})

function handleClick() {
  if (!accordion || !itemValue) return
  accordion.toggle(itemValue)
}
</script>
