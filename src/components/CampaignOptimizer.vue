<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4">🧠 Kampanya Optimizasyon Önerileri</h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedId" class="select select-bordered">
        <option v-for="c in campaigns" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button @click="fetchSuggestions" class="btn btn-success">Önerileri Getir</button>
    </div>

    <table class="table table-zebra w-full text-sm">
      <thead>
        <tr>
          <th>Aksiyon</th>
          <th>Önerilen Tarih</th>
          <th>Beklenen Etki</th>
          <th>Tip</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in suggestions" :key="item.action + item.date">
          <td>{{ item.action }}</td>
          <td>{{ item.date }}</td>
          <td>{{ item.impact }}%</td>
          <td>
            <span :class="typeClass(item.type)">
              {{ item.type }}
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'

type Campaign = { id: number; name: string }
type Suggestion = {
  action: string
  date: string
  impact: number
  type: 'boost' | 'fix' | 'test'
}

const campaigns = ref<Campaign[]>([])
const selectedId = ref<number | null>(null)
const suggestions = ref<Suggestion[]>([])

const fetchCampaigns = async () => {
  const { data } = await axios.get('/admin/campaign-list')
  campaigns.value = data
}

const fetchSuggestions = async () => {
  if (!selectedId.value) return
  const { data } = await axios.get('/admin/campaign-optimizer', {
    params: { campaignId: selectedId.value }
  })
  suggestions.value = data
}

const typeClass = (type: Suggestion['type']) => {
  return {
    boost: 'text-green-600 font-semibold',
    fix: 'text-red-600 font-semibold',
    test: 'text-yellow-600 font-semibold'
  }[type]
}

onMounted(fetchCampaigns)
</script>

<style scoped>
.table td {
  vertical-align: top;
}
</style>