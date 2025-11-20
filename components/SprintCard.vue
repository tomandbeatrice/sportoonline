<template>
  <div class="sprint-card">
    <img :src="demoImage" alt="Sprint Görseli" />
    <div class="info">
      <p><strong>Tarih:</strong> {{ tarih }}</p>
      <p><strong>Durum:</strong> <span :style="{ color: testColor }">{{ testStatus }}</span></p>
    </div>
    <button @click="exportSprint">Export Et</button>
  </div>
</template>

<script setup>
import { computed } from 'vue'
const emit = defineEmits(['exported'])

defineProps({
  demoImage: String,
  testStatus: String,
  tarih: String
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
    tarih,
    demoImage,
    moduller: {
      demo: 'Render alındı',
      export: 'Buton aktif',
      test: testStatus
    },
    ekipKatkisi: {
      frontend: '40%',
      backend: '35%',
      QA: '25%'
    },
    tamamlanma: testStatus === 'Yeşil'
  }

  const blob = new Blob([JSON.stringify(sprintData, null, 2)], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `sprint-${tarih}.json`
  link.click()
  URL.revokeObjectURL(url)

  emit('exported', {
    tarih,
    test: testStatus,
    demoImage
  })
}
</script>

<style scoped>
.sprint-card {
  border: 1px solid #ccc;
  padding: 1rem;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  align-items: center;
}
.info {
  text-align: center;
}
</style>