<!-- src/components/LogTable.vue -->
<template>
  <div class="overflow-x-auto bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-2">📋 Log Tablosu</h2>
    <table class="table table-zebra w-full text-sm">
      <thead>
        <tr>
          <th>#</th>
          <th>Mesaj</th>
          <th>Tip</th>
          <th>Süre ⏱</th>
          <th>Veri Sayısı 📦</th>
          <th>Kaynak</th>
          <th>Detay</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="log in logs" :key="log.id">
          <td>{{ log.id }}</td>
          <td>{{ log.message }}</td>
          <td>
            <span :class="typeClass(log.type)">
              {{ log.type }}
            </span>
          </td>
          <td>
            <span v-if="log.meta?.duration" class="tooltip" :data-tip="`${log.meta.duration} saniye`">
              {{ log.meta.duration }}s
            </span>
            <span v-else>-</span>
          </td>
          <td>
            <span v-if="log.meta?.count" class="tooltip" :data-tip="`${log.meta.count} kayıt`">
              {{ log.meta.count }}
            </span>
            <span v-else>-</span>
          </td>
          <td>{{ log.source || 'manual' }}</td>
          <td>
            <button class="btn btn-sm btn-outline" @click="openModal(log)">🔍</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Detay Modal -->
    <dialog v-if="selectedLog" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg">Log Detayı #{{ selectedLog.id }}</h3>
        <div class="py-2 space-y-1">
          <p><strong>Mesaj:</strong> {{ selectedLog.message }}</p>
          <p><strong>Tip:</strong> {{ selectedLog.type }}</p>
          <p><strong>Süre:</strong> {{ selectedLog.meta?.duration || '-' }} saniye</p>
          <p><strong>Veri Sayısı:</strong> {{ selectedLog.meta?.count || '-' }}</p>
          <p><strong>Kaynak:</strong> {{ selectedLog.source || 'manual' }}</p>
        </div>
        <div class="modal-action">
          <button class="btn" @click="selectedLog = null">Kapat</button>
        </div>
      </div>
    </dialog>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

type LogItem = {
  id: number
  message: string
  type: 'başarı' | 'hata' | 'uyarı' | 'bilgi'
  meta?: { duration?: string; count?: number }
  source?: string
}

const props = defineProps<{ logs: LogItem[] }>()
const selectedLog = ref<LogItem | null>(null)

const openModal = (log: LogItem) => {
  selectedLog.value = log
}

const typeClass = (type: LogItem['type']) => {
  return {
    başarı: 'text-green-600 font-semibold',
    hata: 'text-red-600 font-semibold',
    uyarı: 'text-yellow-600 font-semibold',
    bilgi: 'text-blue-600 font-semibold'
  }[type] || 'text-gray-600'
}
</script>