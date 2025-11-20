<template>
  <div class="mb-3 space-y-1" :class="props.class">
    <slot />
  </div>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{ class?: string }>(), { class: '' })
</script>
