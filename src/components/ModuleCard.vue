<template>
  <div class="card">
    <h4>{{ name }}</h4>
    <p>İlerleme: {{ progress }}%</p>
    <p>Başarı: {{ successRate }}%</p>
    <p>Öneri Kabul: {{ suggestionsAccepted }}</p>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  name: string
  progress: number
  successRate: number
  suggestionsAccepted: number
}>()
</script>

<style scoped>
.card {
  background: #1C2F4D;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 0 8px #00000055;
  transition: transform 0.2s ease;
}
.card:hover {
  transform: scale(1.02);
}
</style>