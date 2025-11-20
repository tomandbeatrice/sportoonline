<template>
  <div
    v-show="isOpen"
    class="overflow-hidden text-sm text-slate-500 transition-all"
    :class="props.class"
  >
    <div class="pt-3">
      <slot />
    </div>
  </div>
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
</script>
