<template>
  <div class="pulse">
    <label>Motivasyon</label>
    <progress :value="value" max="100"></progress>
    <span>{{ value }}%</span>
  </div>
</template>

<script setup lang="ts">
defineProps<{ value: number }>()
</script>

<style scoped>
.pulse {
  width: 200px;
}
progress {
  width: 100%;
  height: 20px;
  accent-color: #00C48C;
}
span {
  display: block;
  margin-top: 0.5rem;
  font-weight: bold;
}
</style>