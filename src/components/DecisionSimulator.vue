<template>
  <section class="p-6 bg-white rounded shadow">
    <h3 class="text-xl font-bold text-indigo-700 mb-6">🧠 Karar Simülasyonu</h3>

    <!-- Sprint Simülasyonu -->
    <div v-if="simulatedActions.length" class="mb-6">
      <h4 class="text-md font-semibold text-gray-700 mb-2">Sprint Aksiyonları</h4>
      <ul class="list-disc pl-5 space-y-1">
        <li v-for="action in simulatedActions" :key="action.id" class="flex items-center gap-2">
          <BadgeIcon name="check" cls="w-4 h-4 text-green-600" /> {{ action.title }} → {{ action.impact }}
        </li>
      </ul>
    </div>

    <!-- Kampanya Önerisi -->
    <div v-if="suggestion" class="space-y-2">
      <h4 class="text-md font-semibold text-gray-700">Kampanya Önerisi</h4>
      <p class="flex items-center gap-2"><BadgeIcon name="tag" cls="w-4 h-4" /> <strong>Kampanya:</strong> {{ suggestion.campaign }}</p>
      <p class="flex items-center gap-2"><BadgeIcon name="bookmark" cls="w-4 h-4" /> <strong>Tür:</strong> {{ suggestion.type }}</p>
      <p class="flex items-center gap-2"><BadgeIcon name="trending-up" cls="w-4 h-4" /> <strong>Skor:</strong> {{ suggestion.score }} / 100</p>
      <p class="flex items-center gap-2"><BadgeIcon name="refresh-cw" cls="w-4 h-4" /> <strong>Dönüşüm:</strong> {{ suggestion.conversionRate }}%</p>
      <p class="flex items-center gap-2"><IconStar cls="w-4 h-4 text-yellow-400" :filled="true" /> <strong>Ortalama Puan:</strong> {{ suggestion.avgRating }}</p>
      <p class="flex items-center gap-2"><BadgeIcon name="clock" cls="w-4 h-4" /> <strong>Son Restart:</strong> {{ suggestion.lastRestart }}</p>
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
import BadgeIcon from '@/components/icons/BadgeIcon.vue'
import { useToast } from 'vue-toastification'

const toast = useToast()
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
  toast.success('Kampanyaya başarıyla katıldınız')
}
</script>

<style scoped>
section {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
</style>