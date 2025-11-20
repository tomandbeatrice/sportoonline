<template>
  <div class="campaign-comparison">
    <div class="comparison-header">
      <h3><i class="fas fa-balance-scale"></i> Kampanya Karşılaştırma</h3>
      <button @click="showSelector = true" class="add-campaign-btn">
        <i class="fas fa-plus"></i> Kampanya Ekle
      </button>
    </div>

    <!-- Campaign Selector Modal -->
    <div v-if="showSelector" class="modal-overlay" @click="showSelector = false">
      <div class="modal-content" @click.stop>
        <h4>Karşılaştırılacak Kampanyaları Seçin</h4>
        <div class="campaign-list">
          <label
            v-for="camp in availableCampaigns"
            :key="camp.id"
            class="campaign-item"
          >
            <input
              type="checkbox"
              :value="camp.id"
              v-model="selectedCampaigns"
              :disabled="selectedCampaigns.length >= 5 && !selectedCampaigns.includes(camp.id)"
            />
            <span>{{ camp.name }}</span>
            <span class="campaign-date">{{ formatDate(camp.created_at) }}</span>
          </label>
        </div>
        <div class="modal-actions">
          <button @click="showSelector = false" class="cancel-btn">İptal</button>
          <button
            @click="loadComparison"
            :disabled="selectedCampaigns.length < 2"
            class="compare-btn"
          >
            Karşılaştır ({{ selectedCampaigns.length }})
          </button>
        </div>
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Kampanyalar analiz ediliyor...</p>
    </div>

    <div v-else-if="comparison" class="comparison-content">
      <!-- Summary Stats -->
      <div class="summary-section">
        <div class="stat-card">
          <i class="fas fa-trophy"></i>
          <div class="stat-data">
            <span class="stat-label">En Yüksek Skor</span>
            <span class="stat-value">{{ comparison.highest_score.toFixed(1) }}</span>
          </div>
        </div>
        <div class="stat-card">
          <i class="fas fa-chart-bar"></i>
          <div class="stat-data">
            <span class="stat-label">Ortalama Skor</span>
            <span class="stat-value">{{ comparison.average_score.toFixed(1) }}</span>
          </div>
        </div>
        <div class="stat-card">
          <i class="fas fa-bullseye"></i>
          <div class="stat-data">
            <span class="stat-label">En Düşük Skor</span>
            <span class="stat-value">{{ comparison.lowest_score.toFixed(1) }}</span>
          </div>
        </div>
      </div>

      <!-- Ranking Table -->
      <div class="ranking-section">
        <h4><i class="fas fa-ranking-star"></i> Kampanya Sıralaması</h4>
        <div class="ranking-table">
          <div
            v-for="(campaign, idx) in comparison.campaigns"
            :key="campaign.id"
            class="ranking-row"
            :class="{ 'top-rank': idx === 0 }"
          >
            <div class="rank-position">
              <span class="rank-number">{{ idx + 1 }}</span>
              <i v-if="idx === 0" class="fas fa-crown"></i>
            </div>
            
            <div class="campaign-info">
              <h5>{{ campaign.name }}</h5>
              <div class="campaign-meta">
                <span><i class="fas fa-calendar"></i> {{ formatDate(campaign.created_at) }}</span>
                <span><i class="fas fa-tag"></i> {{ campaign.type }}</span>
              </div>
            </div>

            <div class="score-display">
              <div class="score-circle" :class="`grade-${campaign.grade.toLowerCase()}`">
                <span class="score-value">{{ campaign.ai_score.toFixed(1) }}</span>
                <span class="score-grade">{{ campaign.grade }}</span>
              </div>
            </div>

            <div class="metrics-grid">
              <div class="metric">
                <span class="metric-label">Dönüşüm</span>
                <span class="metric-value">{{ campaign.metrics.conversion_rate }}%</span>
              </div>
              <div class="metric">
                <span class="metric-label">Gelir</span>
                <span class="metric-value">{{ formatCurrency(campaign.metrics.revenue) }}</span>
              </div>
              <div class="metric">
                <span class="metric-label">Katılım</span>
                <span class="metric-value">{{ campaign.metrics.engagement_rate }}%</span>
              </div>
              <div class="metric">
                <span class="metric-label">ROI</span>
                <span class="metric-value">{{ campaign.metrics.roi }}%</span>
              </div>
            </div>

            <button @click="viewDetails(campaign.id)" class="details-btn">
              <i class="fas fa-eye"></i> Detaylar
            </button>
          </div>
        </div>
      </div>

      <!-- Comparison Chart -->
      <div class="chart-section">
        <h4><i class="fas fa-chart-radar"></i> Karşılaştırmalı Analiz</h4>
        <canvas ref="comparisonChart"></canvas>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue'
import axios from 'axios'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

interface Campaign {
  id: number
  name: string
  type: string
  created_at: string
  ai_score: number
  grade: string
  metrics: {
    conversion_rate: number
    revenue: number
    engagement_rate: number
    roi: number
  }
}

interface Comparison {
  campaigns: Campaign[]
  average_score: number
  highest_score: number
  lowest_score: number
}

const loading = ref(false)
const showSelector = ref(false)
const selectedCampaigns = ref<number[]>([])
const availableCampaigns = ref<Campaign[]>([])
const comparison = ref<Comparison | null>(null)
const comparisonChart = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const loadAvailableCampaigns = async () => {
  try {
    const response = await axios.get('/api/admin/campaigns')
    availableCampaigns.value = response.data.data || response.data
  } catch (err) {
    console.error('Kampanyalar yüklenemedi:', err)
  }
}

const loadComparison = async () => {
  if (selectedCampaigns.value.length < 2) {
    alert('En az 2 kampanya seçmelisiniz')
    return
  }

  loading.value = true
  showSelector.value = false

  try {
    const response = await axios.post('/api/admin/campaign-ai/compare', {
      campaign_ids: selectedCampaigns.value
    })
    comparison.value = response.data
    await nextTick()
    renderChart()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Karşılaştırma yapılamadı')
  } finally {
    loading.value = false
  }
}

const renderChart = () => {
  if (!comparisonChart.value || !comparison.value) return

  if (chartInstance) {
    chartInstance.destroy()
  }

  const ctx = comparisonChart.value.getContext('2d')
  if (!ctx) return

  const labels = comparison.value.campaigns.map(c => c.name)
  const scores = comparison.value.campaigns.map(c => c.ai_score)
  const conversions = comparison.value.campaigns.map(c => c.metrics.conversion_rate)
  const engagement = comparison.value.campaigns.map(c => c.metrics.engagement_rate)
  const roi = comparison.value.campaigns.map(c => c.metrics.roi)

  chartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label: 'AI Skor',
          data: scores,
          backgroundColor: 'rgba(52, 152, 219, 0.7)',
          borderColor: '#3498db',
          borderWidth: 2
        },
        {
          label: 'Dönüşüm %',
          data: conversions,
          backgroundColor: 'rgba(46, 204, 113, 0.7)',
          borderColor: '#2ecc71',
          borderWidth: 2
        },
        {
          label: 'Katılım %',
          data: engagement,
          backgroundColor: 'rgba(155, 89, 182, 0.7)',
          borderColor: '#9b59b6',
          borderWidth: 2
        },
        {
          label: 'ROI %',
          data: roi,
          backgroundColor: 'rgba(241, 196, 15, 0.7)',
          borderColor: '#f1c40f',
          borderWidth: 2
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top'
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  })
}

const viewDetails = (campaignId: number) => {
  window.location.href = `/admin/campaigns/${campaignId}`
}

const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('tr-TR')
}

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY'
  }).format(value)
}

onMounted(() => {
  loadAvailableCampaigns()
})
</script>

<style scoped>
.campaign-comparison {
  padding: 1.5rem;
}

.comparison-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.comparison-header h3 {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
}

.add-campaign-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #3498db;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}

.add-campaign-btn:hover {
  background: #2980b9;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 8px;
  padding: 2rem;
  max-width: 600px;
  width: 90%;
  max-height: 80vh;
  overflow-y: auto;
}

.modal-content h4 {
  margin-top: 0;
  margin-bottom: 1.5rem;
}

.campaign-list {
  max-height: 400px;
  overflow-y: auto;
  margin-bottom: 1.5rem;
}

.campaign-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  margin-bottom: 0.5rem;
  cursor: pointer;
}

.campaign-item:hover {
  background: #f8f9fa;
}

.campaign-item input[type="checkbox"] {
  cursor: pointer;
}

.campaign-date {
  margin-left: auto;
  font-size: 0.875rem;
  color: #666;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.cancel-btn,
.compare-btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.cancel-btn {
  background: #e0e0e0;
  color: #333;
}

.compare-btn {
  background: #3498db;
  color: white;
}

.compare-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.loading-state {
  text-align: center;
  padding: 3rem;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.summary-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 1.5rem;
}

.stat-card i {
  font-size: 2rem;
  color: #3498db;
}

.stat-data {
  display: flex;
  flex-direction: column;
}

.stat-label {
  font-size: 0.875rem;
  color: #666;
  margin-bottom: 0.25rem;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: #2c3e50;
}

.ranking-section {
  margin-bottom: 2rem;
}

.ranking-section h4 {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.ranking-row {
  display: grid;
  grid-template-columns: 60px 2fr 120px 3fr 120px;
  gap: 1.5rem;
  align-items: center;
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  transition: transform 0.2s, box-shadow 0.2s;
}

.ranking-row:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.top-rank {
  border: 2px solid #f1c40f;
  background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%);
}

.rank-position {
  text-align: center;
}

.rank-number {
  display: block;
  font-size: 2rem;
  font-weight: 700;
  color: #3498db;
}

.top-rank .rank-number {
  color: #f1c40f;
}

.fa-crown {
  color: #f1c40f;
  font-size: 1.25rem;
  margin-top: 0.25rem;
}

.campaign-info h5 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
}

.campaign-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.875rem;
  color: #666;
}

.campaign-meta i {
  margin-right: 0.25rem;
}

.score-circle {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.grade-a-plus { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.grade-a { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.grade-b { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.grade-c { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.grade-d { background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%); }
.grade-f { background: linear-gradient(135deg, #c33764 0%, #1d2671 100%); }

.score-value {
  font-size: 1.75rem;
  font-weight: 700;
}

.score-grade {
  font-size: 1rem;
  font-weight: 600;
  opacity: 0.9;
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
}

.metric {
  display: flex;
  flex-direction: column;
}

.metric-label {
  font-size: 0.75rem;
  color: #666;
  margin-bottom: 0.25rem;
}

.metric-value {
  font-weight: 600;
  color: #2c3e50;
}

.details-btn {
  background: #3498db;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background 0.2s;
}

.details-btn:hover {
  background: #2980b9;
}

.chart-section {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 1.5rem;
}

.chart-section h4 {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.chart-section canvas {
  max-height: 400px;
}
</style>
