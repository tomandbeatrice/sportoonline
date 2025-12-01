<template>
  <div class="card" :class="statusClass">
    <h4>{{ module.module }}</h4>
    <p><strong>Son İşlem:</strong> {{ lastTimestamp }}</p>
    <p><strong>Toplam:</strong> {{ totalCount }}</p>
    <p><strong>Süre:</strong> {{ module.duration }}</p>
    <p><strong>Durum:</strong> {{ module.status }}</p>
    <p><strong>Hata Sayısı:</strong> {{ module.errorCount }}</p>
    <p><strong>Son Aksiyon:</strong> {{ module.lastAction }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface ModuleLog {
  module: string
  status: string
  duration: string
  errorCount: number
  lastAction: string
  data: { timestamp: string; count: number }[]
}

const { module } = defineProps<{ module: ModuleLog }>()

const lastTimestamp = computed(() => module.data.slice(-1)[0]?.timestamp || 'Yok')
const totalCount = computed(() => module.data.reduce((sum, d) => sum + d.count, 0))

const statusClass = computed(() => {
  switch (module.status) {
    case 'active': return 'active'
    case 'pending': return 'pending'
    case 'error': return 'error'
    default: return ''
  }
})
</script>

<style scoped>
.card {
  background: #f9f9f9;
  padding: 1rem;
  border-radius: 6px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.1);
  width: 220px;
  transition: all 0.3s ease;
}
.card:hover {
  transform: scale(1.03);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.card.active {
  border-left: 6px solid #42A5F5;
}
.card.pending {
  border-left: 6px solid #FFA726;
}
.card.error {
  border-left: 6px solid #EF5350;
}
</style>