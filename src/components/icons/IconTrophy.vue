<template>
  <svg :class="cls" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2l2 6h6l-5 4 2 6-5-3-5 3 2-6-5-4h6z" />
  </svg>
</template>

<script setup>
const props = defineProps({ cls: { type: String, default: '' } });
</script>
