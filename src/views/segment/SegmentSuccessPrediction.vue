<template>
  <div>
    <h3>Segment Başarı Tahmini</h3>
    
    <div v-if="error" class="error-banner">
      ⚠️ {{ error }}
    </div>
    
    <div v-else-if="loading" class="loading">
      Yükleniyor...
    </div>
    
    <table v-else>
      <thead>
        <tr>
          <th>Segment</th>
          <th>Tahmin Skoru</th>
          <th>Gerçekleşen Skor</th>
          <th>Sapma</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in predictions" :key="index">
          <td>{{ item.segment }}</td>
          <td>{{ item.predicted }}%</td>
          <td>{{ item.actual }}%</td>
          <td :style="{ color: Math.abs(item.predicted - item.actual) > 10 ? 'red' : 'green' }">
            {{ (item.actual - item.predicted).toFixed(2) }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useErrorHandler } from '@/composables/useErrorHandler'
import axios from 'axios'

const predictions = ref([])
const { error, loading, withLoading } = useErrorHandler()

onMounted(async () => {
  await withLoading(async () => {
    const res = await axios.get('/api/admin/segment-success-predictions')
    predictions.value = res.data
  })
})
</script>