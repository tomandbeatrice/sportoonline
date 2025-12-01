<template>
  <div>
    <h2>CDN Asset Performans Logları</h2>

    <table>
      <thead>
        <tr>
          <th>Dosya</th>
          <th>Boyut</th>
          <th>Yüklenme Süresi (ms)</th>
          <th>Zaman</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="log in logs" :key="log.name + log.timestamp">
          <td>{{ log.name }}</td>
          <td>{{ log.size }}</td>
          <td>{{ log.duration_ms }}</td>
          <td>{{ formatDate(log.timestamp) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const logs = ref([])

onMounted(async () => {
  const res = await fetch('/api/cockpit/asset-performance')
  logs.value = await res.json()
})

function formatDate(date) {
  return new Date(date).toLocaleString('tr-TR')
}
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 8px;
  border-bottom: 1px solid #ccc;
}
</style>