<template>
  <svg :class="cls" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 20l14-8-7-7-7 15z" />
  </svg>
</template>

<script setup>
const props = defineProps({ cls: { type: String, default: '' } });
</script>
