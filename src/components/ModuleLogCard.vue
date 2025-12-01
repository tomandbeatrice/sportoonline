<template>
  <div class="card">
    <h3>{{ module.module }}</h3>
    <ProgressBar :value="module.progress" />
    <StatusTag :status="module.status" />
    <p>Son işlem: {{ module.lastAction }}</p>
    <p>Hata: <span class="error">{{ module.errorCount }}</span></p>
    <p>Süre: {{ module.duration }}</p>
    <MiniChart :data="module.data" />
  </div>
</template>

<script setup lang="ts">
interface ModuleLog {
  module: string
  progress: number
  status: string
  duration: string
  errorCount: number
  lastAction: string
  data: { timestamp: string; count: number }[]
}

defineProps<{ module: ModuleLog }>()
</script>

<style scoped>
.card {
  background: #fff;
  border-radius: 8px;
  padding: 1rem;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.error {
  color: red;
  font-weight: bold;
}
</style>