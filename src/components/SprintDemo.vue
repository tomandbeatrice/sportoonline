<template>
  <div class="demo-container">
    <img :src="demoImage" alt="Sprint Görseli" />
    <button @click="exportSprint">Export Et</button>
    <div class="test-panel" :style="{ color: testColor }">
      Test Paneli: {{ testStatus }}
    </div>

    <!-- 🧠 Planlama bileşeni -->
    <scheduled-export-list />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import ScheduledExportList from './ScheduledExportList.vue'

defineProps({
  demoImage: String,
  testStatus: {
    type: String,
    default: 'Yeşil'
  }
})

const testColor = computed(() => {
  switch (testStatus) {
    case 'Yeşil': return 'green'
    case 'Sarı': return 'orange'
    case 'Kırmızı': return 'red'
    default: return 'gray'
  }
})

function exportSprint() {
  const sprintData = {
    tarih: new Date().toISOString(),
    moduller: {
      demo: 'Render alındı',
      export: 'Buton aktif',
      test: testStatus
    },
    tamamlanma: testStatus === 'Yeşil'
  }

  const blob = new Blob([JSON.stringify(sprintData, null, 2)], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = 'sprint-export.json'
  link.click()
  URL.revokeObjectURL(url)
}
</script>

<style scoped>
.demo-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}
.test-panel {
  font-weight: bold;
}
</style>