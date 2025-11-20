<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4">🤖 AI Destekli Kampanya Stratejisi</h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button @click="generateStrategy" class="btn btn-info">Strateji Oluştur</button>
    </div>

    <div v-if="strategy" class="bg-gray-50 p-4 rounded border mt-6">
      <h3 class="text-lg font-semibold mb-2">📋 AI Strateji Planı</h3>
      <ul class="list-disc pl-4 space-y-1">
        <li v-for="(step, index) in strategy.steps" :key="index">{{ step }}</li>
      </ul>
      <p class="mt-4 text-sm text-gray-500">Tahmini Etki Skoru: {{ strategy.impact_score }}%</p>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'

type Campaign = { id: number; name: string }
type Strategy = {
  steps: string[]
  impact_score: number
}

const campaigns = ref<Campaign[]>([])
const selectedId = ref<number | null>(null)
const strategy = ref<Strategy | null>(null)

const fetchCampaigns = async () => {
  const { data } = await axios.get('/admin/campaign-list')
  campaigns.value = data
}

const generateStrategy = async () => {
  if (!selectedId.value) return
  const { data } = await axios.post('/admin/campaign-ai-strategy', {
    campaignId: selectedId.value
  })
  strategy.value = data
}

onMounted(fetchCampaigns)
</script>

<style scoped>
.select {
  min-width: 200px;
}
</style>