<template>
  <p class="text-lg font-semibold text-slate-900" :class="props.class">
    <slot />
  </p>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{ class?: string }>(), { class: '' })
</script>
