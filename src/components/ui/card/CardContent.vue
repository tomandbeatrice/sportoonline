<template>
  <div :class="props.class">
    <slot />
  </div>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{ class?: string }>(), { class: '' })
</script>
