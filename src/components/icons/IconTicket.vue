<template>
  <svg :class="cls" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10V6a2 2 0 00-2-2h-4M3 14v4a2 2 0 002 2h4M21 14v4a2 2 0 01-2 2h-4M3 6V6a2 2 0 012-2h4" />
  </svg>
</template>

<script setup>
const props = defineProps({ cls: { type: String, default: '' } });
</script>
