<template>
  <component
    :is="props.as"
    v-bind="attrs"
    class="rounded-3xl border border-slate-100 bg-white/90 p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-2xl"
    :class="props.class"
  >
    <slot />
  </component>
</template>

<script setup lang="ts">
import { useAttrs } from 'vue'

defineOptions({ inheritAttrs: false })

const props = withDefaults(defineProps<{ class?: string; as?: string }>(), {
  class: '',
  as: 'div'
})

const attrs = useAttrs()
</script>
