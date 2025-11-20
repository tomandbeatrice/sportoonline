<template>
  <section class="p-6 space-y-4">
    <h2 class="text-xl font-bold text-green-700">📅 Kişiselleştirilmiş Kampanya Takvimi</h2>
    <ul class="space-y-3 text-sm">
      <li v-for="item in plan" :key="item.campaign" class="border p-4 rounded">
        <strong>{{ item.campaign }}</strong> ({{ item.type }})  
        <br />Son: {{ item.lastRun }}  
        <br />Önerilen Tekrar: {{ item.nextRun }}  
        <br />Tahmini Dönüşüm: {{ item.expectedConversion }}%

        <button
          @click="restart(item.campaign)"
          class="mt-2 bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700"
        >
          Başlat
        </button>

        <p v-if="message[item.campaign]" class="text-green-700 mt-1">
          {{ message[item.campaign] }}
        </p>
      </li>
    </ul>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const plan = ref([])
const message = ref({})

onMounted(async () => {
  const userRes = await axios.get('/api/user')
  const sellerId = userRes.data.id
  const res = await axios.get(`/api/seller/${sellerId}/campaign-planner`)
  plan.value = res.data
})

const restart = async (campaign) => {
  const userRes = await axios.get('/api/user')
  const sellerId = userRes.data.id
  const res = await axios.post(`/api/seller/${sellerId}/restart-campaign`, {
    campaign
  })
  message.value[campaign] = res.data.message
}
</script>