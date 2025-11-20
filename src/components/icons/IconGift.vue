<template>
  <svg :class="cls" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6" />
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 12v9M12 3l3 3m-6 0l3-3" />
  </svg>
</template>

<script setup>
const props = defineProps({ cls: { type: String, default: '' } });
</script>
