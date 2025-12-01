<template>
  <div>
    <h2>Export Edilen Kampanya Önerileri</h2>
    <table>
      <thead>
        <tr>
          <th>Satıcı ID</th>
          <th>Dosya Adı</th>
          <th>Zaman</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="entry in exports" :key="entry.timestamp">
          <td>{{ entry.seller_id }}</td>
          <td>{{ entry.file }}</td>
          <td>{{ formatDate(entry.timestamp) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const exports = ref([])

onMounted(async () => {
  const res = await axios.get('/api/admin/export-campaign-suggestions')
  exports.value = res.data
})

function formatDate(date) {
  return new Date(date).toLocaleString('tr-TR')
}
</script>