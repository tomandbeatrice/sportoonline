<template>
  <div class="animate-pulse rounded bg-slate-200" :class="props.class" />
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{ class?: string }>(), {
  class: ''
})
</script>
