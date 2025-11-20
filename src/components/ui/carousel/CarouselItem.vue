<template>
  <div class="min-w-[240px] flex-1 snap-start" :class="props.class">
    <slot />
  </div>
</template>

<script setup lang="ts">
const props = withDefaults(defineProps<{ class?: string }>(), { class: '' })
</script>
