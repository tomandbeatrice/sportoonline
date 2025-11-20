<template>
  <div>
    <h2>Segment Strateji Durumu</h2>

    <p>Son strateji güncellemesi: <strong>{{ lastUpdated }}</strong></p>
    <button @click="refreshStrategy" :disabled="loading">
      <span v-if="loading">Güncelleniyor...</span>
      <span v-else>Stratejiyi Güncelle</span>
    </button>

    <div v-if="weights">
      <h3>Segment Ağırlıkları</h3>
      <ul>
        <li v-for="(value, key) in weights" :key="key">
          {{ key }}: {{ value }}
        </li>
      </ul>
    </div>

    <StrategyAccuracy />
    <SegmentAccuracyBreakdown />
    <SegmentAccuracyChart />
    <StrategyAccuracyTrend />
    <SuggestedCampaignCard :suggestion="suggestion" />
    <SuggestionHistoryTable />
    <SuggestionDeviationTable />
    <CalibratedConfidenceTable />
    <ABTestResultTable />
    <StrategyVersionComparison />

    <div v-if="history.length">
      <h3>Güncelleme Geçmişi</h3>
      <ul>
        <li v-for="(item, index) in history" :key="index">
          {{ item }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

import StrategyAccuracy from './StrategyAccuracy.vue'
import SegmentAccuracyBreakdown from './SegmentAccuracyBreakdown.vue'
import SegmentAccuracyChart from './SegmentAccuracyChart.vue'
import StrategyAccuracyTrend from './StrategyAccuracyTrend.vue'
import SuggestedCampaignCard from './SuggestedCampaignCard.vue'
import SuggestionHistoryTable from './SuggestionHistoryTable.vue'
import SuggestionDeviationTable from './SuggestionDeviationTable.vue'

const weights = ref(null)
const lastUpdated = ref('Yükleniyor...')
const history = ref([])
const suggestion = ref(null)
const loading = ref(false)

async function refreshStrategy() {
  loading.value = true
  try {
    const res = await axios.post('/api/admin/refresh-campaign-strategy')
    weights.value = res.data.weights
    suggestion.value = res.data.suggestion

    const now = new Date().toLocaleString('tr-TR')
    lastUpdated.value = now
    updateLocalStorage(now, res.data.weights)

    const accuracyLog = JSON.parse(localStorage.getItem('accuracyLog') || '{}')
    accuracyLog[now] = res.data.accuracy_percent
    localStorage.setItem('accuracyLog', JSON.stringify(accuracyLog))
  } catch (error) {
    console.error('Strateji güncellemesi başarısız:', error)
  } finally {
    loading.value = false
  }
}

function updateLocalStorage(now, newWeights) {
  localStorage.setItem('lastStrategyUpdate', now)

  const prevHistory = JSON.parse(localStorage.getItem('strategyHistory') || '[]')
  const updatedHistory = [now, ...prevHistory].slice(0, 10)
  localStorage.setItem('strategyHistory', JSON.stringify(updatedHistory))
  history.value = updatedHistory

  const weightsLog = JSON.parse(localStorage.getItem('weightsLog') || '{}')
  weightsLog[now] = newWeights
  localStorage.setItem('weightsLog', JSON.stringify(weightsLog))
}

onMounted(() => {
  lastUpdated.value = localStorage.getItem('lastStrategyUpdate') || 'Henüz güncellenmedi'
  history.value = JSON.parse(localStorage.getItem('strategyHistory') || '[]')
})
</script>