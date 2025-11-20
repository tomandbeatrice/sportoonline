<template>
  <div class="export-log">
    <h2>Export Logları</h2>

    <div class="filters">
      <label>Segment:</label>
      <select v-model="filter.segment">
        <option value="">Tümü</option>
        <option value="customer">Customer</option>
        <option value="vendor">Vendor</option>
      </select>

      <label>Durum:</label>
      <select v-model="filter.status">
        <option value="">Tümü</option>
        <option value="completed">Tamamlandı</option>
        <option value="failed">Başarısız</option>
      </select>

      <button @click="fetchLogs">Filtrele</button>
      <button @click="exportCSV">Export Al</button>
    </div>

    <table>
      <thead>
        <tr>
          <th>Segment</th>
          <th>Tip</th>
          <th>Durum</th>
          <th>Süre (sn)</th>
          <th>Dosya</th>
          <th>Boyut (MB)</th>
          <th>Tarih</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="log in logs" :key="log.id">
          <td>{{ log.segment }}</td>
          <td>{{ log.export_type }}</td>
          <td>{{ log.status }}</td>
          <td>{{ log.duration }}</td>
          <td>{{ log.file_name }}</td>
          <td>{{ log.file_size }}</td>
          <td>{{ formatDate(log.exported_at) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const logs = ref([])
const filter = ref({ segment: '', status: '' })

const fetchLogs = async () => {
  const res = await axios.get('/api/export-logs', { params: filter.value })
  logs.value = res.data.data
}

const exportCSV = () => {
  const params = new URLSearchParams(filter.value).toString()
  window.open(`/api/export-logs/export?${params}`)
}

const formatDate = date => new Date(date).toLocaleString()

onMounted(fetchLogs)
</script>

<style scoped>
.export-log {
  padding: 20px;
}
.filters {
  margin-bottom: 10px;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 8px;
  border: 1px solid #ccc;
}
</style>