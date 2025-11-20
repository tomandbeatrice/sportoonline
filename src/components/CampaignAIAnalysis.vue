<template>
  <div class="campaign-ai-analysis">
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>AI Analiz ediliyor...</p>
    </div>

    <div v-else-if="error" class="error-state">
      <i class="fas fa-exclamation-circle"></i>
      <p>{{ error }}</p>
      <button @click="loadAnalysis" class="retry-btn">Tekrar Dene</button>
    </div>

    <div v-else-if="analysis" class="analysis-content">
      <!-- AI Score Card -->
      <div class="score-card" :class="`grade-${analysis.grade.toLowerCase()}`">
        <div class="score-header">
          <i class="fas fa-brain"></i>
          <h3>AI Kampanya Skoru</h3>
        </div>
        <div class="score-display">
          <div class="score-value">{{ analysis.ai_score.toFixed(1) }}</div>
          <div class="score-grade">{{ analysis.grade }}</div>
        </div>
        <div class="score-breakdown">
          <div class="metric">
            <span class="label">Dönüşüm Etkisi</span>
            <div class="bar">
              <div class="fill" :style="{ width: `${analysis.metrics.conversion_rate}%` }"></div>
            </div>
          </div>
          <div class="metric">
            <span class="label">Gelir Etkisi</span>
            <div class="bar">
              <div class="fill" :style="{ width: `${analysis.metrics.revenue_impact}%` }"></div>
            </div>
          </div>
          <div class="metric">
            <span class="label">Katılım Oranı</span>
            <div class="bar">
              <div class="fill" :style="{ width: `${analysis.metrics.engagement_rate}%` }"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recommendations -->
      <div class="recommendations-section">
        <h4><i class="fas fa-lightbulb"></i> AI Önerileri</h4>
        <div class="recommendations-grid">
          <CampaignRecommendationCard
            v-for="(rec, idx) in analysis.recommendations"
            :key="idx"
            :recommendation="rec"
            :campaign-id="campaignId"
            @applied="handleRecommendationApplied"
          />
        </div>
      </div>

      <!-- Customer Segments -->
      <div class="segments-section">
        <h4><i class="fas fa-users"></i> Önerilen Müşteri Segmentleri</h4>
        <div class="segments-grid">
          <div
            v-for="(segment, idx) in analysis.suggested_segments"
            :key="idx"
            class="segment-card"
          >
            <div class="segment-header">
              <span class="segment-name">{{ segment.segment }}</span>
              <span class="segment-size">{{ segment.estimated_size }} müşteri</span>
            </div>
            <p class="segment-reason">{{ segment.reason }}</p>
            <button @click="applySegment(segment)" class="apply-segment-btn">
              Segmenti Uygula
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import CampaignRecommendationCard from './CampaignRecommendationCard.vue'

interface Props {
  campaignId: number
}

const props = defineProps<Props>()

interface AIAnalysis {
  ai_score: number
  grade: string
  metrics: {
    conversion_rate: number
    revenue_impact: number
    engagement_rate: number
    roi: number
  }
  recommendations: Array<{
    type: string
    priority: string
    suggestion: string
    action: string
    estimated_impact: string
  }>
  suggested_segments: Array<{
    segment: string
    reason: string
    estimated_size: number
  }>
}

const loading = ref(false)
const error = ref<string | null>(null)
const analysis = ref<AIAnalysis | null>(null)

const loadAnalysis = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get(`/api/admin/campaign-ai/analyze/${props.campaignId}`)
    analysis.value = response.data
  } catch (err: any) {
    error.value = err.response?.data?.message || 'AI analizi yüklenemedi'
  } finally {
    loading.value = false
  }
}

const handleRecommendationApplied = () => {
  loadAnalysis() // Refresh analysis after applying recommendation
}

const applySegment = async (segment: any) => {
  try {
    await axios.post(`/api/admin/campaigns/${props.campaignId}/apply-segment`, {
      segment: segment.segment
    })
    alert(`${segment.segment} segmenti kampanyaya uygulandı!`)
  } catch (err: any) {
    alert(err.response?.data?.message || 'Segment uygulanamadı')
  }
}

onMounted(() => {
  loadAnalysis()
})
</script>

<style scoped>
.campaign-ai-analysis {
  padding: 1.5rem;
}

.loading-state,
.error-state {
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

.error-state i {
  font-size: 3rem;
  color: #e74c3c;
  margin-bottom: 1rem;
}

.retry-btn {
  background: #3498db;
  color: white;
  border: none;
  padding: 0.5rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 1rem;
}

.score-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 2rem;
}

.grade-a-plus { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.grade-a { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.grade-b { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.grade-c { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.grade-d { background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%); }
.grade-f { background: linear-gradient(135deg, #c33764 0%, #1d2671 100%); }

.score-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.score-header i {
  font-size: 1.5rem;
}

.score-display {
  display: flex;
  align-items: baseline;
  gap: 1rem;
  margin-bottom: 2rem;
}

.score-value {
  font-size: 4rem;
  font-weight: 700;
}

.score-grade {
  font-size: 2rem;
  font-weight: 600;
  opacity: 0.9;
}

.score-breakdown .metric {
  margin-bottom: 1rem;
}

.score-breakdown .label {
  display: block;
  margin-bottom: 0.5rem;
  opacity: 0.9;
}

.score-breakdown .bar {
  background: rgba(255, 255, 255, 0.2);
  height: 8px;
  border-radius: 4px;
  overflow: hidden;
}

.score-breakdown .fill {
  background: white;
  height: 100%;
  transition: width 0.5s ease;
}

.recommendations-section,
.segments-section {
  margin-top: 2rem;
}

.recommendations-section h4,
.segments-section h4 {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  font-size: 1.25rem;
}

.recommendations-grid,
.segments-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
}

.segment-card {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 1.5rem;
}

.segment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.segment-name {
  font-weight: 600;
  color: #2c3e50;
}

.segment-size {
  background: #e3f2fd;
  color: #1976d2;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.875rem;
}

.segment-reason {
  color: #666;
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.apply-segment-btn {
  width: 100%;
  background: #3498db;
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: background 0.2s;
}

.apply-segment-btn:hover {
  background: #2980b9;
}
</style>
