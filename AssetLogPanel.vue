<template>
  <div class="asset-log-panel">
    <h2>📊 CDN Asset Performans Logları</h2>

    <select v-model="selectedSegment">
      <option value="">Tüm Segmentler</option>
      <option v-for="seg in segments" :key="seg" :value="seg">Segment {{ seg }}</option>
    </select>

    <table v-if="filteredLogs.length">
      <thead>
        <tr>
          <th>Dosya</th>
          <th>Süre (ms)</th>
          <th>Boyut (KB)</th>
          <th>Segment</th>
          <th>Zaman</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(log, index) in filteredLogs" :key="index">
          <td>{{ log.filename }}</td>
          <td>{{ log.loadTime }}</td>
          <td>{{ log.size }}</td>
          <td>{{ log.segment }}</td>
          <td>{{ formatDate(log.created_at) }}</td>
        </tr>
      </tbody>
    </table>
    <p v-else>Seçilen segment için veri bulunamadı.</p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const logs = ref([])
const selectedSegment = ref('')
const segments = ref([])

onMounted(async () => {
  const res = await axios.get('/api/cockpit/asset-performance')
  logs.value = res.data
  segments.value = [...new Set(logs.value.map(log => log.segment))]
})

const filteredLogs = computed(() => {
  return selectedSegment.value
    ? logs.value.filter(log => log.segment === selectedSegment.value)
    : logs.value
})

function formatDate(date) {
  return new Date(date).toLocaleString('tr-TR')
}
</script>

<style scoped>
.asset-log-panel {
  padding: 1rem;
  background-color: #fdfdfd;
  border-radius: 8px;
}
select {
  margin-bottom: 1rem;
  padding: 6px;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 8px;
  border-bottom: 1px solid #ccc;
}
</style>