<template>
  <div>
    <h2>Teşvik Kampanyası Logları</h2>

    <table>
      <thead>
        <tr>
          <th>Zaman</th>
          <th>Ürün ID</th>
          <th>Satıcı ID</th>
          <th>İndirim Oranı</th>
          <th>Refleks</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="entry in logs" :key="entry.timestamp">
          <td>{{ formatDate(entry.timestamp) }}</td>
          <td>{{ entry.product_id || '-' }}</td>
          <td>{{ entry.vendor_id || entry.seller_id }}</td>
          <td>{{ entry.discount_rate || entry.new_rate }}</td>
          <td>{{ entry.message }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const logs = ref([])

onMounted(async () => {
  const res = await axios.get('/api/admin/incentive-log')
  logs.value = res.data
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