<template>
  <section class="p-6 space-y-6">
    <h2 class="text-xl font-bold text-gray-800">Kampanya Tekrar Logları</h2>

    <ul class="space-y-2 text-sm text-gray-700">
      <li v-for="log in logs" :key="log.timestamp">
        <span class="font-semibold">{{ log.campaign }}</span> → {{ log.timestamp }}
      </li>
    </ul>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const logs = ref([])

onMounted(async () => {
  const userRes = await axios.get('/api/user')
  const sellerId = userRes.data.id
  const res = await axios.get(`/api/seller/${sellerId}/campaign-restart-log`)
  logs.value = res.data
})
</script>