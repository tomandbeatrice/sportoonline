<template>
  <div class="inline-flex items-center gap-2" :class="props.class">
    <slot />
  </div>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{ class?: string }>(), { class: '' })
</script>
