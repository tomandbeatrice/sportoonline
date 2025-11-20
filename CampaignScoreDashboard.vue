<template>
  <div>
    <h2>Satıcı Kampanya Skorları</h2>
    <table>
      <thead>
        <tr>
          <th>Satıcı ID</th>
          <th>Skor</th>
          <th>Dönüşüm Oranı</th>
          <th>Outlet Ürün Sayısı</th>
          <th>Komisyon Oranı</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="seller in sellers" :key="seller.id">
          <td>{{ seller.id }}</td>
          <td>{{ seller.score }}</td>
          <td>{{ seller.conversion_rate }}%</td>
          <td>{{ seller.outlet_count }}</td>
          <td>{{ seller.commission_rate }}%</td>
        </tr>
      </tbody>
    </table>

    <h2 style="margin-top: 40px;">Kampanya Skorları</h2>
    <table>
      <thead>
        <tr>
          <th>Kampanya</th>
          <th>Tip</th>
          <th>Son Restart</th>
          <th>Sipariş</th>
          <th>Görüntüleme</th>
          <th>Dönüşüm (%)</th>
          <th>Ortalama Puan</th>
          <th>Restart Sonrası Dönüşüm</th>
          <th>Skor</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in scores" :key="item.campaign">
          <td>{{ item.campaign }}</td>
          <td>{{ item.type }}</td>
          <td>{{ item.lastRestart }}</td>
          <td>{{ item.orders }}</td>
          <td>{{ item.views }}</td>
          <td>{{ item.conversionRate }}</td>
          <td>{{ item.avgRating }}</td>
          <td>{{ item.postRestartConversion }}</td>
          <td><strong>{{ item.score }}</strong></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const sellers = ref([])
const scores = ref([])

onMounted(async () => {
  const sellerId = 45 // test ID

  const [sellerRes, scoreRes] = await Promise.all([
    axios.get('/api/admin/campaign-score-dashboard'),
    axios.get(`/api/seller/${sellerId}/campaign-suggestion-list`)
  ])

  sellers.value = sellerRes.data
  scores.value = scoreRes.data
})
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 40px;
}
th, td {
  padding: 8px;
  border-bottom: 1px solid #ccc;
}
strong {
  color: #2c3e50;
}
</style>