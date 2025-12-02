<template>
  <div>
    <h3>Kalibre Edilmiş Güven Katsayıları</h3>
    <table>
      <thead>
        <tr>
          <th>Tarih</th>
          <th>Segment</th>
          <th>Kampanya</th>
          <th>Orijinal Güven</th>
          <th>Başarı</th>
          <th>Kalibre Güven</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in adjusted" :key="index">
          <td>{{ item.date }}</td>
          <td>{{ item.segment }}</td>
          <td>{{ item.campaign_type }}</td>
          <td>{{ item.original_confidence }}%</td>
          <td>{{ item.actual_success }}</td>
          <td :style="{ color: item.adjusted_confidence > item.original_confidence ? 'green' : item.adjusted_confidence < item.original_confidence ? 'red' : 'gray' }">
            {{ item.adjusted_confidence }}%
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const adjusted = ref([])

onMounted(async () => {
  try {
    const res = await axios.get('/api/admin/calibrated-confidence')
    adjusted.value = res.data
  } catch (error) {
    console.error('Kalibrasyon verisi alınamadı:', error)
  }
})
</script>