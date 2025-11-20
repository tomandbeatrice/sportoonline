<template>
  <div>
    <h3>Segment Bazlı Doğruluk Oranı</h3>
    <table>
      <thead>
        <tr>
          <th>Segment</th>
          <th>Doğruluk (%)</th>
          <th>Doğru</th>
          <th>Toplam</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(data, segment) in breakdown" :key="segment">
          <td>{{ segment }}</td>
          <td>{{ data.accuracy_percent }}</td>
          <td>{{ data.correct }}</td>
          <td>{{ data.total }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const breakdown = ref({})

onMounted(async () => {
  try {
    const res = await axios.get('/api/admin/segment-accuracy-breakdown')
    breakdown.value = res.data
  } catch (error) {
    console.error('Segment doğruluk verisi alınamadı:', error)
  }
})
</script>