<template>
  <div class="campaign-settings">
    <h2>🎯 Kampanya Öneri Sistemi</h2>
    <table v-if="suggestions.length">
      <thead>
        <tr>
          <th>Segment</th>
          <th>Öneri Başlığı</th>
          <th>Skor</th>
          <th>Export</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in suggestions" :key="index">
          <td>#{{ item.segmentId }}</td>
          <td>{{ item.title }}</td>
          <td>{{ item.score }}</td>
          <td>
            <button @click="exportItem(item)" class="export-btn">📤 Export Et</button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-else class="empty-message">Henüz öneri bulunamadı.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const suggestions = ref([])

onMounted(async () => {
  const res = await axios.get('/api/admin/export-campaign-suggestions')
  suggestions.value = res.data
})

function exportItem(item) {
  const blob = new Blob([JSON.stringify(item, null, 2)], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `segment-${item.segmentId}-suggestion.json`
  link.click()
  URL.revokeObjectURL(url)
}
</script>

<style scoped>
.campaign-settings {
  padding: 1rem;
  background-color: #fdfdfd;
  border-radius: 8px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}
th, td {
  padding: 8px;
  border-bottom: 1px solid #ccc;
}
.export-btn {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
}
.empty-message {
  margin-top: 1rem;
  color: #888;
}
</style>