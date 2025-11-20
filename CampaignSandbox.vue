<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4">🧪 Kampanya Sandbox Simülasyonu</h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <select v-model="selectedAction" class="select select-bordered">
        <option value="boost-discount">İndirim Artışı</option>
        <option value="change-banner">Banner Değişimi</option>
        <option value="push-reminder">Push Hatırlatma</option>
      </select>
      <button @click="simulate" class="btn btn-accent">Simülasyonu Başlat</button>
    </div>

    <div v-if="result" class="mt-6 bg-gray-50 p-4 rounded border">
      <h3 class="text-lg font-semibold mb-2">📊 Simülasyon Sonucu</h3>
      <p><strong>Beklenen Skor Artışı:</strong> {{ result.score_increase }}%</p>
      <p><strong>Beklenen Dönüşüm Artışı:</strong> {{ result.conversion_increase }}%</p>
      <p><strong>Risk:</strong> {{ result.risk_level }}</p>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'

type Campaign = { id: number; name: string }
type SimulationResult = {
  score_increase: number
  conversion_increase: number
  risk_level: 'low' | 'medium' | 'high'
}

const campaigns = ref<Campaign[]>([])
const selectedId = ref<number | null>(null)
const selectedAction = ref<string>('boost-discount')
const result = ref<SimulationResult | null>(null)

const fetchCampaigns = async () => {
  const { data } = await axios.get('/admin/campaign-list')
  campaigns.value = data
}

const simulate = async () => {
  if (!selectedId.value || !selectedAction.value) return
  const { data } = await axios.post('/admin/campaign-sandbox-simulate', {
    campaignId: selectedId.value,
    action: selectedAction.value
  })
  result.value = data
}

onMounted(fetchCampaigns)
</script>

<style scoped>
.select {
  min-width: 200px;
}
</style>