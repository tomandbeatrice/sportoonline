<template>
  <div>
    <h2>Kampanya Başarısı Regresyon Analizi</h2>
    <p><strong>Gerçek Skor:</strong> {{ result.actual_score }}</p>
    <p><strong>Tahmini Skor:</strong> {{ result.predicted_score }}</p>

    <table>
      <thead>
        <tr>
          <th>Değişken</th>
          <th>Girdi</th>
          <th>Katsayı</th>
          <th>Etki</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(value, key) in result.inputs" :key="key">
          <td>{{ key }}</td>
          <td>{{ value }}</td>
          <td>{{ result.coefficients[key] }}</td>
          <td>{{ (value * result.coefficients[key]).toFixed(2) }}</td>
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
  const res = await axios.get(`/api/seller/${sellerId}/campaign-regression`)
  result.value = res.data
})
</script>