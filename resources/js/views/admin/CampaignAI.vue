<template>
  <div class="campaign-ai-page">
    <div class="page-header">
      <h1><i class="fas fa-robot"></i> AI Kampanya Yönetimi</h1>
      <p class="page-subtitle">Yapay zeka destekli kampanya analizi ve optimizasyonu</p>
    </div>

    <div class="tabs-container">
      <div class="tabs">
        <button
          @click="activeTab = 'analysis'"
          :class="{ active: activeTab === 'analysis' }"
          class="tab-btn"
        >
          <i class="fas fa-brain"></i> Kampanya Analizi
        </button>
        <button
          @click="activeTab = 'comparison'"
          :class="{ active: activeTab === 'comparison' }"
          class="tab-btn"
        >
          <i class="fas fa-balance-scale"></i> Karşılaştırma
        </button>
        <button
          @click="activeTab = 'prediction'"
          :class="{ active: activeTab === 'prediction' }"
          class="tab-btn"
        >
          <i class="fas fa-crystal-ball"></i> Tahmin
        </button>
      </div>
    </div>

    <!-- Campaign Selector -->
    <div v-if="activeTab === 'analysis' || activeTab === 'prediction'" class="campaign-selector">
      <label>Kampanya Seçin:</label>
      <select v-model.number="selectedCampaignId" class="campaign-select">
        <option :value="null">-- Kampanya Seçin --</option>
        <option
          v-for="campaign in campaigns"
          :key="campaign.id"
          :value="campaign.id"
        >
          {{ campaign.name }} ({{ campaign.type }})
        </option>
      </select>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
      <!-- Analysis Tab -->
      <div v-if="activeTab === 'analysis' && selectedCampaignId" class="tab-pane">
        <CampaignAIAnalysis :campaign-id="selectedCampaignId" />
      </div>

      <!-- Comparison Tab -->
      <div v-if="activeTab === 'comparison'" class="tab-pane">
        <CampaignComparison />
      </div>

      <!-- Prediction Tab -->
      <div v-if="activeTab === 'prediction' && selectedCampaignId" class="tab-pane">
        <CampaignPredictionChart :campaign-id="selectedCampaignId" />
      </div>

      <!-- Empty State -->
      <div
        v-if="(activeTab === 'analysis' || activeTab === 'prediction') && !selectedCampaignId"
        class="empty-state"
      >
        <i class="fas fa-chart-line"></i>
        <h3>Kampanya Seçin</h3>
        <p>AI analizi için yukarıdan bir kampanya seçin</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import CampaignAIAnalysis from '../../components/CampaignAIAnalysis.vue'
import CampaignComparison from '../../components/CampaignComparison.vue'
import CampaignPredictionChart from '../../components/CampaignPredictionChart.vue'

interface Campaign {
  id: number
  name: string
  type: string
}

const activeTab = ref('analysis')
const campaigns = ref<Campaign[]>([])
const selectedCampaignId = ref<number | null>(null)

const loadCampaigns = async () => {
  try {
    const response = await axios.get('/api/admin/campaigns')
    campaigns.value = response.data.data || response.data
    if (campaigns.value.length > 0) {
      selectedCampaignId.value = campaigns.value[0].id
    }
  } catch (err) {
    console.error('Kampanyalar yüklenemedi:', err)
  }
}

onMounted(() => {
  loadCampaigns()
})
</script>

<style scoped>
.campaign-ai-page {
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin: 0 0 0.5rem 0;
  font-size: 2rem;
  color: #2c3e50;
}

.page-subtitle {
  color: #666;
  font-size: 1rem;
  margin: 0;
}

.tabs-container {
  margin-bottom: 2rem;
  border-bottom: 2px solid #e0e0e0;
}

.tabs {
  display: flex;
  gap: 0.5rem;
}

.tab-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: transparent;
  border: none;
  border-bottom: 3px solid transparent;
  padding: 1rem 1.5rem;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  color: #666;
  transition: all 0.2s;
}

.tab-btn:hover {
  color: #3498db;
  background: #f8f9fa;
}

.tab-btn.active {
  color: #3498db;
  border-bottom-color: #3498db;
}

.campaign-selector {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.campaign-selector label {
  font-weight: 600;
  color: #2c3e50;
}

.campaign-select {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  background: white;
  cursor: pointer;
}

.tab-content {
  min-height: 400px;
}

.empty-state {
  text-align: center;
  padding: 5rem 2rem;
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
}

.empty-state i {
  font-size: 4rem;
  color: #bdc3c7;
  margin-bottom: 1.5rem;
}

.empty-state h3 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #7f8c8d;
}
</style>
