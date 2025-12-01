<script setup lang="ts">
import type { ModuleLog } from '@types/module'

defineProps<{ log: ModuleLog }>()

// Simülasyon: Gerçek hata logları backend'den alınabilir
const errorLogs = log.errorCount > 0
  ? [
      { time: '2025-08-12T15:02:00', message: 'Kategori eşleşmesi başarısız.' },
      { time: '2025-08-12T15:04:00', message: 'API yanıtı geçersiz formatta.' },
    ]
  : []
</script>

<template>
  <div>
    <h4>Hata Geçmişi</h4>
    <div v-if="errorLogs.length === 0">🚀 Bu modülde hata kaydı bulunmuyor.</div>
    <ul v-else class="log-list">
      <li v-for="(err, index) in errorLogs" :key="index">
        <strong>{{ new Date(err.time).toLocaleTimeString('tr-TR') }}</strong> — {{ err.message }}
      </li>
    </ul>
  </div>
</template>

<style scoped>
.log-list {
  padding-left: 1rem;
  list-style-type: square;
}
</style>