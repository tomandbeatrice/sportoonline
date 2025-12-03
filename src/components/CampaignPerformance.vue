<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
      <BadgeIcon name="rocket" class="w-6 h-6 text-red-600" />
      Kampanya Performans Özeti
    </h2>

    <table class="table table-zebra w-full text-sm">
      <thead>
        <tr>
          <th>Kampanya</th>
          <th>Skor</th>
          <th>Dönüşüm %</th>
          <th>Tıklama</th>
          <th>Ortalama Süre</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in performance" :key="item.id">
          <td>{{ item.name }}</td>
          <td>{{ item.score }}</td>
          <td>{{ item.conversion_rate }}%</td>
          <td>{{ item.clicks }}</td>
          <td>{{ item.avg_duration }}s</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import BadgeIcon from '@/components/BadgeIcon.vue'
import axios from '@/utils/axios'

type PerformanceItem = {
  id: number
  name: string
  score: number
  conversion_rate: number
  clicks: number
  avg_duration: number
}

const performance = ref<PerformanceItem[]>([])

const fetchPerformance = async () => {
  try {
    const { data } = await axios.get('/admin/campaign-performance')
    performance.value = data
  } catch (err) {
    console.error('Performans verisi alınamadı:', err)
  }
}

onMounted(fetchPerformance)
</script>

<style scoped>
.table td {
  vertical-align: middle;
}
</style>