<template>
  <svg :class="cls" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2l2.09 6.26L20 9l-4.91 3.74L16.18 19 12 15.77 7.82 19l1.09-6.26L4 9l5.91-.74L12 2z" />
  </svg>
</template>

<script setup>
const props = defineProps({ cls: { type: String, default: '' } });
</script>
