<template>
  <section class="history-panel">
    <h3>📚 Önceki Sprintler</h3>
    <canvas ref="historyChart" class="chart-canvas" />
    <ul class="history-list">
      <li v-for="s in sprint.history" :key="s.id" class="history-item">
        <div class="history-header">
          <strong>{{ s.title }}</strong>
          <span class="history-date">🗓️ {{ s.start }} – {{ s.end }}</span>
        </div>
        <div class="history-progress">
          Başarı Oranı:
          <span :class="rateClass(s.successRate)">%{{ s.successRate }}</span>
        </div>
        <div class="history-meta">
          Modül Sayısı: {{ s.moduleCount }} | Hata: {{ s.errorCount }}
        </div>
      </li>
    </ul>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import Chart from 'chart.js/auto'
import { useSprintStore } from '@/stores/sprint'

const sprint = useSprintStore()
const historyChart = ref<HTMLCanvasElement | null>(null)

const sprintLabels = computed(() => sprint.history.map(h => h.title))
const sprintProgress = computed(() => sprint.history.map(h => h.successRate))

const rateClass = (rate: number) => {
  if (rate > 80) return 'rate-high'
  if (rate > 60) return 'rate-medium'
  return 'rate-low'
}

onMounted(() => {
  if (!historyChart.value) return

  new Chart(historyChart.value, {
    type: 'bar',
    data: {
      labels: sprintLabels.value,
      datasets: [{
        label: 'Başarı Oranı',
        data: sprintProgress.value,
        backgroundColor: sprintProgress.value.map(rate =>
          rate > 80 ? '#2ecc71' : rate > 60 ? '#f39c12' : '#e74c3c'
        )
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { mode: 'index', intersect: false }
      },
      scales: {
        y: { beginAtZero: true, max: 100 }
      }
    }
  })
})
</script>

<style scoped>
.history-panel {
  background-color: var(--card);
  color: var(--text);
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  transition: background-color 0.3s ease, color 0.3s ease;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.chart-canvas {
  width: 100%;
  max-width: 600px;
  height: 280px;
}

.history-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.history-item {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  padding: 0.8rem;
  border-left: 4px solid var(--accent);
  background-color: var(--card-light, #f9f9f9);
  border-radius: 8px;
}

.history-header {
  display: flex;
  justify-content: space-between;
  font-size: 1rem;
  font-weight: 600;
}

.history-date {
  font-size: 0.9rem;
  font-weight: 400;
  color: var(--text-secondary, #888);
}

.history-progress {
  font-size: 0.95rem;
  font-weight: 500;
}

.history-meta {
  font-size: 0.85rem;
  color: var(--text-secondary, #666);
}

.rate-high {
  color: #2ecc71;
}
.rate-medium {
  color: #f39c12;
}
.rate-low {
  color: #e74c3c;
}
</style>