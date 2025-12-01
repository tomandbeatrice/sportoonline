<template>
  <section class="p-4 bg-yellow-50 border rounded">
    <h3 class="text-md font-bold text-yellow-700">📢 Haftalık Öneri Bildirimi</h3>
    <ul class="mt-2 space-y-1 text-sm text-gray-800">
      <li v-for="item in suggestions" :key="item.campaign">
        ✅ {{ item.campaign }} → Skor: {{ item.score }}
      </li>
    </ul>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const suggestions = ref([])

onMounted(async () => {
  const res = await axios.get('/api/user/notifications')
  suggestions.value = res.data[0]?.data?.suggestions || []
})
</script>