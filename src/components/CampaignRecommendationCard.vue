<template>
  <div class="recommendation-card" :class="`priority-${recommendation.priority}`">
    <div class="card-header">
      <div class="type-badge" :class="`type-${recommendation.type.replace('_', '-')}`">
        <i :class="getIcon(recommendation.type)"></i>
        {{ getTypeLabel(recommendation.type) }}
      </div>
      <span class="priority-badge">{{ getPriorityLabel(recommendation.priority) }}</span>
    </div>

    <div class="card-body">
      <p class="suggestion">{{ recommendation.suggestion }}</p>
      <div class="action-info">
        <i class="fas fa-cogs"></i>
        <span>{{ recommendation.action }}</span>
      </div>
      <div class="impact-info">
        <i class="fas fa-chart-line"></i>
        <span>{{ recommendation.estimated_impact }}</span>
      </div>
    </div>

    <div class="card-footer">
      <button
        @click="applyRecommendation"
        :disabled="applying"
        class="apply-btn"
      >
        <i class="fas" :class="applying ? 'fa-spinner fa-spin' : 'fa-check'"></i>
        {{ applying ? 'Uygulanıyor...' : 'Uygula' }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'

interface Props {
  recommendation: {
    type: string
    priority: string
    suggestion: string
    action: string
    estimated_impact: string
  }
  campaignId: number
}

const props = defineProps<Props>()
const emit = defineEmits<{
  applied: []
}>()

const applying = ref(false)

const getIcon = (type: string): string => {
  const icons: Record<string, string> = {
    conversion_optimization: 'fas fa-bullseye',
    engagement_enhancement: 'fas fa-heart',
    timing_optimization: 'fas fa-clock',
    budget_reallocation: 'fas fa-dollar-sign'
  }
  return icons[type] || 'fas fa-lightbulb'
}

const getTypeLabel = (type: string): string => {
  const labels: Record<string, string> = {
    conversion_optimization: 'Dönüşüm',
    engagement_enhancement: 'Katılım',
    timing_optimization: 'Zamanlama',
    budget_reallocation: 'Bütçe'
  }
  return labels[type] || type
}

const getPriorityLabel = (priority: string): string => {
  const labels: Record<string, string> = {
    high: 'Yüksek Öncelik',
    medium: 'Orta Öncelik',
    low: 'Düşük Öncelik'
  }
  return labels[priority] || priority
}

const applyRecommendation = async () => {
  if (applying.value) return

  applying.value = true
  try {
    await axios.post(`/api/admin/campaign-ai/apply/${props.campaignId}`, {
      recommendation_type: props.recommendation.type,
      action: props.recommendation.action
    })
    
    alert('Öneri başarıyla uygulandı!')
    emit('applied')
  } catch (err: any) {
    alert(err.response?.data?.message || 'Öneri uygulanamadı')
  } finally {
    applying.value = false
  }
}
</script>

<style scoped>
.recommendation-card {
  background: white;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}

.recommendation-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.priority-high {
  border-left: 4px solid #e74c3c;
}

.priority-medium {
  border-left: 4px solid #f39c12;
}

.priority-low {
  border-left: 4px solid #95a5a6;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: #f8f9fa;
  border-bottom: 1px solid #e0e0e0;
}

.type-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  font-size: 0.875rem;
}

.type-badge i {
  font-size: 1rem;
}

.type-conversion-optimization {
  color: #e74c3c;
}

.type-engagement-enhancement {
  color: #9b59b6;
}

.type-timing-optimization {
  color: #3498db;
}

.type-budget-reallocation {
  color: #27ae60;
}

.priority-badge {
  background: #e0e0e0;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

.priority-high .priority-badge {
  background: #fee;
  color: #e74c3c;
}

.priority-medium .priority-badge {
  background: #fff3e0;
  color: #f39c12;
}

.card-body {
  padding: 1.5rem;
}

.suggestion {
  font-size: 0.95rem;
  color: #2c3e50;
  margin-bottom: 1rem;
  line-height: 1.5;
}

.action-info,
.impact-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #666;
  margin-bottom: 0.5rem;
}

.action-info i,
.impact-info i {
  color: #3498db;
}

.card-footer {
  padding: 1rem;
  background: #f8f9fa;
  border-top: 1px solid #e0e0e0;
}

.apply-btn {
  width: 100%;
  background: #3498db;
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: background 0.2s;
}

.apply-btn:hover:not(:disabled) {
  background: #2980b9;
}

.apply-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
