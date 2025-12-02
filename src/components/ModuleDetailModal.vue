<script setup lang="ts">
import { ref, computed } from 'vue'
import type { ModuleLog } from '@types/module'
import ModuleChart from '@components/ModuleChart.vue'
import ErrorLogViewer from '@components/ErrorLogViewer.vue'
import ModuleTimeline from '@components/ModuleTimeline.vue'

defineProps<{
  log: ModuleLog
  visible: boolean
}>()

const props = defineProps<{
  log: { data: Array<{ timestamp: string; count: number }> }
  visible: boolean
}>()

defineEmits(['close'])

const activeTab = ref<'data' | 'errors' | 'info'>('data')

const formattedData = computed(() =>
  props.log.data.map((point) => ({
    time: new Date(point.timestamp).toLocaleTimeString('tr-TR'),
    count: point.count,
  }))
)
</script>

<template>
  <div v-if="visible" class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <h3>{{ log.module }} Detayları</h3>

      <!-- Tab Menüsü -->
      <div class="tabs">
        <button @click="activeTab = 'data'" :class="{ active: activeTab === 'data' }">📈 Veri</button>
        <button @click="activeTab = 'errors'" :class="{ active: activeTab === 'errors' }">❌ Hatalar</button>
        <button @click="activeTab = 'info'" :class="{ active: activeTab === 'info' }">⚙️ Bilgi</button>
      </div>

      <!-- Tab İçeriği -->
      <div v-if="activeTab === 'data'" class="tab-content">
        <ModuleChart :log="log" />
        <ul>
          <li v-for="point in formattedData" :key="point.time">
            {{ point.time }} → <strong>{{ point.count }}</strong>
          </li>
        </ul>
      </div>

      <div v-if="activeTab === 'errors'" class="tab-content">
        <ErrorLogViewer :log="log" />
      </div>

      <div v-if="activeTab === 'info'" class="tab-content">
        <p>Status: <strong>{{ log.status }}</strong></p>
        <p>Duration: {{ log.duration }}</p>
        <p>Last Action: {{ log.lastAction }}</p>
        <p>Toplam Hata: <strong>{{ log.errorCount }}</strong></p>

        <ModuleTimeline :log="log" />
      </div>

      <button class="close-btn" @click="$emit('close')">Kapat</button>
    </div>
  </div>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}
.tabs {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}
.tabs button {
  padding: 0.5rem 1rem;
  border: none;
  background: #eee;
  cursor: pointer;
  border-radius: 4px;
}
.tabs button.active {
  background: #2196f3;
  color: white;
}
.tab-content {
  margin-bottom: 1rem;
}
.close-btn {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  background: #f44336;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>