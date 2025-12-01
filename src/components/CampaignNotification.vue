<template>
  <div>
    <h2>Kampanya Bildirimleri</h2>
    <ul>
      <li v-for="note in notifications" :key="note.id">
        <strong>{{ note.title }}</strong> — {{ note.message }}
        <span style="color: gray;">({{ formatDate(note.created_at) }})</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const notifications = ref([])
const sellerId = 45

onMounted(async () => {
  const res = await axios.get(`/api/seller/${sellerId}/notifications`)
  notifications.value = res.data
})

function formatDate(date) {
  return new Date(date).toLocaleString('tr-TR')
}
</script>