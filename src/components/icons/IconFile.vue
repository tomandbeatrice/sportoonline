<template>
  <svg :class="cls" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M9 16h6M7 8h6l2 2v8a1 1 0 01-1 1H7a1 1 0 01-1-1V8a1 1 0 011-1z" />
  </svg>
</template>

<script setup>
const props = defineProps({ cls: { type: String, default: '' } });
</script>
