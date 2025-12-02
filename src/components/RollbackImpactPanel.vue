<template>
  <div>
    <h2>Geri Alınan Kampanyaların Etkisi</h2>
    <div v-if="impact">
      <p><strong>Sipariş Öncesi:</strong> {{ impact.orders_before }}</p>
      <p><strong>Sipariş Sonrası:</strong> {{ impact.orders_after }}</p>
      <p><strong>Puan Öncesi:</strong> {{ impact.rating_before }}</p>
      <p><strong>Puan Sonrası:</strong> {{ impact.rating_after }}</p>
      <p><strong>Skor:</strong> {{ impact.score }}</p>
      <p><strong>AI Önerisi:</strong> {{ impact.ai_suggestion }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const impact = ref(null)
const sellerId = 45

onMounted(async () => {
  const res = await axios.get(`/api/seller/${sellerId}/rollback-impact-summary`)
  impact.value = res.data
})
</script>