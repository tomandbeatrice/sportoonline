<template>
  <section class="p-4 bg-white rounded shadow">
    <h2 class="text-lg font-bold text-indigo-700">📜 Öneri Geçmişi</h2>
    <table class="mt-4 w-full text-sm">
      <thead>
        <tr>
          <th>Kampanya</th>
          <th>Öneri Zamanı</th>
          <th>Katılım</th>
          <th>Dönüşüm (%)</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="log in logs" :key="log.id">
          <td>{{ log.campaign_tag }}</td>
          <td>{{ log.suggested_at }}</td>
          <td>{{ log.joined ? '✅' : '❌' }}</td>
          <td>{{ log.conversion_rate }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const logs = ref([])

onMounted(async () => {
  const sellerId = 1 // auth üzerinden alınabilir
  const res = await axios.get(`/api/seller/${sellerId}/suggestion-logs`)
  logs.value = res.data
})
</script>