<template>
  <p class="text-sm text-slate-500" :class="props.class">
    <slot />
  </p>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{ class?: string }>(), { class: '' })
</script>
