<template>
  <div>
    <h2>Segment Bazlı Kampanya Skor Trendleri</h2>
    <table>
      <thead>
        <tr>
          <th>Tarih</th>
          <th>Tip</th>
          <th>İndirim</th>
          <th>Durum</th>
          <th>Skor</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in filtered" :key="item.date + item.type">
          <td>{{ item.date }}</td>
          <td>{{ item.type }}</td>
          <td>%{{ item.discount }}</td>
          <td>{{ item.status }}</td>
          <td><strong>{{ item.score }}</strong></td>
        </tr>
      </tbody>
    </table>

    <label>Segment Filtresi:</label>
    <select v-model="segment">
      <option value="">Tümü</option>
      <option value="Outlet Boost">Davet</option>
      <option value="Flash Sale">Trend</option>
    </select>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const data = ref([])
const segment = ref('')
const sellerId = 45

onMounted(async () => {
  const res = await axios.get(`/api/seller/${sellerId}/campaign-segment-trend`)
  data.value = res.data
})

const filtered = computed(() => {
  return segment.value
    ? data.value.filter(item => item.type === segment.value)
    : data.value
})
</script>