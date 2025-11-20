<template>
  <svg :class="cls" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v18" />
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3c2 0 3 1 5 1s3-1 5-1 3 1 5 1v14c-2 0-3-1-5-1s-3 1-5 1-3-1-5-1V3z" />
  </svg>
</template>

<script setup>
const props = defineProps({ cls: { type: String, default: '' } });
</script>
