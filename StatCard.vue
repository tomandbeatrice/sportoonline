<template>
  <div class="bg-white p-4 rounded shadow text-center">
    <div class="text-3xl mb-2">{{ icon }}</div>
    <h3 class="text-lg font-semibold">{{ title }}</h3>
    <p class="text-2xl font-bold text-blue-600">{{ value }}</p>
  </div>
</template>

<script setup>
defineProps(['title', 'value', 'icon'])
</script>