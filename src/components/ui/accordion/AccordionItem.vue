<template>
  <div :class="['group rounded-2xl border border-slate-100 bg-white/90 shadow-sm', props.class]">
    <slot />
  </div>
</template>

<script setup lang="ts">
import { provide } from 'vue'
import { accordionItemKey } from './tokens'

const props = withDefaults(
  defineProps<{ value: string; class?: string }>(),
  { class: '' }
)

provide(accordionItemKey, props.value)
</script>
