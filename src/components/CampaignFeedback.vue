<template>
  <div>
    <h2>Kampanya Geri Bildirimi</h2>
    <textarea v-model="feedback" placeholder="Görüşünüzü yazın..."></textarea>
    <button @click="sendFeedback">Gönder</button>
    <p v-if="status">{{ status }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const feedback = ref('')
const status = ref('')
const sellerId = 45

async function sendFeedback() {
  const res = await axios.post(`/api/seller/${sellerId}/replan-campaign`, { feedback: feedback.value })
  status.value = res.data.message
}
</script>