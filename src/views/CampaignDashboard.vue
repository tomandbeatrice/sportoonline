<template>
  <div>
    <h2>Kampanya Skorları</h2>

    <label>Kampanya Tipi:</label>
    <select v-model="selectedType">
      <option value="">Tümü</option>
      <option value="davet">Davet</option>
      <option value="trend">Trend</option>
      <option value="organik">Organik</option>
    </select>

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
        <tr v-for="item in filteredScores" :key="item.campaign">
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
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const scores = ref([])
const selectedType = ref('')
const sellerId = 45

onMounted(async () => {
  const res = await axios.get(`/api/seller/${sellerId}/campaign-suggestion-list`)
  scores.value = res.data
})

const filteredScores = computed(() => {
  return selectedType.value
    ? scores.value.filter(item => item.type === selectedType.value)
    : scores.value
})
</script>