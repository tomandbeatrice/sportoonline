<template>
  <section class="p-6 bg-yellow-50 border rounded shadow-sm space-y-4">
    <h2 class="text-xl font-bold text-yellow-700">Kampanya Tekrar Başlat</h2>

    <p class="text-gray-800">
      Önerilen kampanya: <span class="font-semibold">{{ campaign }}</span>
    </p>

    <button
      @click="restart"
      class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700"
    >
      Kampanyayı Tekrar Başlat
    </button>

    <p v-if="message" class="text-green-700 font-medium">{{ message }}</p>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const campaign = ref('')
const message = ref('')

onMounted(async () => {
  const userRes = await axios.get('/api/user')
  const sellerId = userRes.data.id
  const res = await axios.get(`/api/seller/${sellerId}/campaign-recommendation`)
  campaign.value = res.data.recommended
})

const restart = async () => {
  const userRes = await axios.get('/api/user')
  const sellerId = userRes.data.id
  const res = await axios.post(`/api/seller/${sellerId}/restart-campaign`, {
    campaign: campaign.value
  })
  message.value = res.data.message
}
</script>