<template>
  <svg :class="['inline-block', cls]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" :fill="filled ? 'currentColor' : 'none'" stroke="currentColor" aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17.3L6.16 20l1.1-6.41L2.5 9.36l6.46-.94L12 2l3.04 6.42 6.46.94-4.76 4.23L17.84 20 12 17.3z" />
  </svg>
</template>

<script setup>
const props = defineProps({ cls: { type: String, default: '' }, filled: { type: Boolean, default: false } });
const { filled } = props
</script>
