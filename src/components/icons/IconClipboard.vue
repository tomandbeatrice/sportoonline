<template>
  <svg :class="cls" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 2h6a2 2 0 012 2v1H7V4a2 2 0 012-2z" />
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10v12a2 2 0 01-2 2H9a2 2 0 01-2-2V8z" />
  </svg>
</template>

<script setup>
const props = defineProps({ cls: { type: String, default: '' } });
</script>
