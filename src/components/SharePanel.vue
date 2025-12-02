<template>
  <div class="share-panel">
    <h2>📤 Son Export Edilen Veri</h2>
    <pre v-if="lastExport">{{ lastExport }}</pre>
    <p v-else>Henüz export edilmiş veri bulunamadı.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const lastExport = ref(null)

onMounted(() => {
  const stored = localStorage.getItem('lastExport')
  if (stored) {
    lastExport.value = stored
  }
})
</script>

<style scoped>
.share-panel {
  padding: 1rem;
  background-color: #fff8e1;
  border-radius: 8px;
}
pre {
  background-color: #f4f4f4;
  padding: 1rem;
  border-radius: 6px;
  overflow-x: auto;
}
</style>