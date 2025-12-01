<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { ModuleLog } from '@types/module'

import ModuleChart from '@components/ModuleChart.vue'
import ErrorChart from '@components/ErrorChart.vue'
import DurationChart from '@components/DurationChart.vue'
import StatusPieChart from '@components/StatusPieChart.vue'
import ModuleDetailModal from '@components/ModuleDetailModal.vue'

const logs = ref<ModuleLog[]>([])
const selectedLog = ref<ModuleLog | null>(null)
const showModal = ref(false)

function openModal(log: ModuleLog) {
  selectedLog.value = log
  showModal.value = true
}

function formatTime(timestamp: string): string {
  return new Date(timestamp).toLocaleTimeString('tr-TR', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  })
}

onMounted(() => {
  logs.value = [
    {
      module: 'ExportCleanup',
      status: 'active',
      duration: '12s',
      errorCount: 0,
      lastAction: 'deleteOldFiles',
      data: [
        { timestamp: '2025-08-12T15:00:00', count: 120 },
        { timestamp: '2025-08-12T15:05:00', count: 98 },
      ],
    },
    {
      module: 'CategorySync',
      status: 'pending',
      duration: '5s',
      errorCount: 2,
      lastAction: 'syncCategories',
      data: [
        { timestamp: '2025-08-12T15:00:00', count: 45 },
        { timestamp: '2025-08-12T15:05:00', count: 60 },
      ],
    },
  ]
})
</script>

<template>
  <div class="dashboard">
    <h2>📊 Modül Durumları</h2>

    <div v-if="logs.length === 0">Veri yükleniyor...</div>

    <div
      v-for="log in logs"
      :key="log.module"
      class="card"
      @click="openModal(log)"
      style="cursor: pointer"
    >
      <h3>{{ log.module }}</h3>
      <p>Status: <strong>{{ log.status }}</strong></p>
      <p>Errors: {{ log.errorCount }}</p>
      <p>Last Action: {{ log.lastAction }}</p>
      <p>Duration: {{ log.duration }}</p>

      <div class="data-list">
        <h4>Veri Akışı:</h4>
        <ul>
          <li v-for="point in log.data" :key="point.timestamp">
            {{ formatTime(point.timestamp) }} → <strong>{{ point.count }}</strong>
          </li>
        </ul>
      </div>

      <div class="chart">
        <ModuleChart :log="log" />
      </div>
    </div>

    <!-- Grafikler -->
    <div class="error-chart">
      <ErrorChart :logs="logs" />
    </div>

    <div class="duration-chart">
      <DurationChart :logs="logs" />
    </div>

    <div class="status-chart">
      <StatusPieChart :logs="logs" />
    </div>

    <!-- Modal -->
    <ModuleDetailModal
      v-if="selectedLog"
      :log="selectedLog"
      :visible="showModal"
      @close="showModal = false"
    />
  </div>
</template>

<style scoped>
.dashboard {
  padding: 1rem;
}
.card {
  border: 1px solid #ccc;
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 8px;
  background-color: #f9f9f9;
}
.card:hover {
  background-color: #eef;
}
.data-list ul {
  padding-left: 1rem;
  list-style-type: disc;
}
.chart {
  margin-top: 1rem;
}
.error-chart,
.duration-chart,
.status-chart {
  margin-top: 2rem;
}
</style>