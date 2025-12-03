<template>
  <div>
    <h2>Canlı Kampanya Başarı Tahmini</h2>
    <p><strong>Tahmini Skor:</strong> {{ result.predicted_score }}</p>
    <p><strong>Önerilen Kampanya:</strong> {{ result.suggested_campaign }}</p>
    <p><strong>İndirim Oranı:</strong> %{{ result.suggested_discount }}</p>

    <table>
      <thead>
        <tr>
          <th>Davranış Metriği</th>
          <th>Girdi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(value, key) in result.inputs" :key="key">
          <td>{{ key }}</td>
          <td>{{ value }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const result = ref({})
const sellerId = 45

onMounted(async () => {
  const res = await axios.get(`/api/seller/${sellerId}/predict-campaign-success`)
  result.value = res.data
})
</script>