<template>
  <div class="card">
    <div class="icon">{{ icon }}</div>
    <div class="label">{{ label }}</div>
    <div class="value">{{ value }}</div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  label: string
  value: string | number
  icon?: string
}>()
</script>

<style scoped>
.card {
  background: var(--card-bg, #f9fafb);
  border-radius: 8px;
  padding: 1rem;
  text-align: center;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.icon {
  font-size: 1.5rem;
}
.label {
  font-weight: 600;
  margin-top: 0.5rem;
}
.value {
  font-size: 1.25rem;
  font-weight: bold;
  margin-top: 0.25rem;
}
</style>