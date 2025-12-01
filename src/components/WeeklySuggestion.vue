<template>
  <section class="p-6 space-y-4">
    <h2 class="text-lg font-bold text-indigo-700">Haftalık Kampanya Önerileri</h2>
    <ul class="space-y-2 text-sm">
      <li v-for="item in suggestions" :key="item.campaign" class="border p-3 rounded">
        <strong>{{ item.campaign }}</strong> → Skor: {{ item.score }}
      </li>
    </ul>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const suggestions = ref([])

onMounted(async () => {
  const userRes = await axios.get('/api/user')
  const sellerId = userRes.data.id
  const res = await axios.get(`/api/seller/${sellerId}/campaign-suggestion-list`)
  suggestions.value = res.data
})
</script>