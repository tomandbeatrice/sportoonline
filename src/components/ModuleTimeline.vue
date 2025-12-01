<script setup lang="ts">
import type { ModuleLog } from '@types/module'

defineProps<{ log: ModuleLog }>()

// Simülasyon: Gerçek geçmiş backend'den alınabilir
const timeline = [
  { time: '2025-08-12T14:50:00', action: 'initModule' },
  { time: '2025-08-12T14:55:00', action: 'validateInput' },
  { time: '2025-08-12T15:00:00', action: log.lastAction },
]
</script>

<template>
  <div>
    <h4>🕒 Çalıştırma Zaman Çizelgesi</h4>
    <ul class="timeline">
      <li v-for="(step, index) in timeline" :key="index">
        <span class="time">{{ new Date(step.time).toLocaleTimeString('tr-TR') }}</span>
        <span class="action">{{ step.action }}</span>
      </li>
    </ul>
  </div>
</template>

<style scoped>
.timeline {
  list-style: none;
  padding-left: 0;
}
.timeline li {
  margin-bottom: 0.5rem;
  padding-left: 1rem;
  border-left: 2px solid #2196f3;
  position: relative;
}
.timeline li::before {
  content: '';
  position: absolute;
  left: -6px;
  top: 6px;
  width: 10px;
  height: 10px;
  background: #2196f3;
  border-radius: 50%;
}
.time {
  font-weight: bold;
  margin-right: 0.5rem;
}
.action {
  color: #333;
}
</style>