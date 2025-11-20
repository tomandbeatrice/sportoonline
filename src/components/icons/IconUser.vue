<template>
  <svg :class="cls" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 14a4 4 0 10-8 0v1a3 3 0 003 3h2a3 3 0 003-3v-1zM12 7a4 4 0 110-8 4 4 0 010 8z"/>
  </svg>
</template>

<script setup>
const props = defineProps({ cls: { type: String, default: '' } });
</script>
