<template>
  <div>
    <h2>Satıcı Davranış Özeti + Kampanya Skoru</h2>
    <table>
      <thead>
        <tr>
          <th>Satıcı ID</th>
          <th>Skor</th>
          <th>Yanıt Süresi (dk)</th>
          <th>Ürün Güncelleme</th>
          <th>Restart Sayısı</th>
          <th>Yorum Yanıt Oranı</th>
          <th>Export Kullanımı</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="seller in sellers" :key="seller.id">
          <td>{{ seller.id }}</td>
          <td><strong>{{ seller.score }}</strong></td>
          <td>{{ seller.response_time }}</td>
          <td>{{ seller.update_count }}</td>
          <td>{{ seller.restart_count }}</td>
          <td>{{ seller.comment_response_rate }}%</td>
          <td>{{ seller.export_usage }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const sellers = ref([])

onMounted(async () => {
  const res = await axios.get('/api/admin/seller-behavior-score-summary')
  sellers.value = res.data
})
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}
th, td {
  padding: 8px;
  border-bottom: 1px solid #ccc;
}
strong {
  color: #2c3e50;
}
</style>