<template>
  <section class="p-6 bg-white rounded shadow">
    <h3 class="text-xl font-bold text-indigo-700 mb-6">🧠 Karar Simülasyonu</h3>

    <!-- Sprint Simülasyonu -->
    <div v-if="simulatedActions.length" class="mb-6">
      <h4 class="text-md font-semibold text-gray-700 mb-2">Sprint Aksiyonları</h4>
      <ul class="list-disc pl-5 space-y-1">
        <li v-for="action in simulatedActions" :key="action.id">
          ✅ {{ action.title }} → {{ action.impact }}
        </li>
      </ul>
    </div>

    <!-- Kampanya Önerisi -->
    <div v-if="suggestion" class="space-y-2">
      <h4 class="text-md font-semibold text-gray-700">Kampanya Önerisi</h4>
      <p><strong>🏷️ Kampanya:</strong> {{ suggestion.campaign }}</p>
      <p><strong>📌 Tür:</strong> {{ suggestion.type }}</p>
      <p><strong>📈 Skor:</strong> {{ suggestion.score }} / 100</p>
      <p><strong>🔁 Dönüşüm:</strong> {{ suggestion.conversionRate }}%</p>
      <p><strong><IconStar cls="w-4 h-4 text-yellow-400 inline-block mr-1" :filled="true" /> Ortalama Puan:</strong> {{ suggestion.avgRating }}</p>
      <p><strong>🕒 Son Restart:</strong> {{ suggestion.lastRestart }}</p>
      <button @click="joinCampaign" class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded">
        Kampanyaya Katıl
      </button>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import IconStar from '@/components/icons/IconStar.vue'

const simulatedActions = ref([])
const suggestion = ref(null)

onMounted(async () => {
  try {
    const sprintRes = await fetch('/api/sprint/simulate')
    simulatedActions.value = await sprintRes.json()

    const sellerId = 1 // auth üzerinden dinamik alınabilir
    const campaignRes = await axios.get(`/api/seller/${sellerId}/campaign-suggestion-list`)
    suggestion.value = campaignRes.data[0]
  } catch (err) {
    console.error('Karar simülasyonu verisi alınamadı:', err)
  }
})

async function joinCampaign() {
  await axios.post('/api/seller/join-suggested-campaign')
  alert('Kampanyaya başarıyla katıldınız ✅')
}
</script>

<style scoped>
section {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
</style>