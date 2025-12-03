<template>
  <div class="stat-card">
    <slot />
  </div>
</template>

<script setup lang="ts">
</script>

<style scoped>
.stat-card {
  padding: 1.5rem;
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
</style>
