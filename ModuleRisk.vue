<template>
  <section class="risk-panel">
    <h3>🧠 Modül Risk Analizi</h3>
    <canvas ref="riskCanvas" class="chart-canvas" />
    <ul class="risk-list">
      <li v-for="mod in combinedModules" :key="mod.name">
        ⚠️ {{ mod.name }} – Risk Skoru: {{ mod.riskScore }} → Öneri: {{ mod.suggestion }}
      </li>
    </ul>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import Chart from 'chart.js/auto'
import { useSprintStore } from '@/stores/sprint'

const sprint = useSprintStore()
const riskCanvas = ref<HTMLCanvasElement | null>(null)
const apiRisks = ref<{ name: string; riskScore: number }[]>([])

const combinedModules = computed(() =>
  sprint.modules.map(mod => {
    const apiMod = apiRisks.value.find(m => m.name === mod.name)
    const score = apiMod?.riskScore ?? mod.riskScore
    return {
      name: mod.name,
      riskScore: score,
      suggestion: score > 80 ? 'Refactor önerilir' : score > 60 ? 'Kod gözden geçirilmeli' : 'Risk düşük'
    }
  }).filter(m => m.riskScore > 60)
)

onMounted(async () => {
  const res = await fetch('/api/module/risk')
  apiRisks.value = await res.json()

  if (!riskCanvas.value) return

  new Chart(riskCanvas.value, {
    type: 'radar',
    data: {
      labels: combinedModules.value.map(m => m.name),
      datasets: [{
        label: 'Risk Skoru',
        data: combinedModules.value.map(m => m.riskScore),
        backgroundColor: 'rgba(255,111,60,0.3)',
        borderColor: '#FF6F3C',
        pointBackgroundColor: '#FF6F3C'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { mode: 'nearest', intersect: true }
      },
      scales: {
        r: {
          angleLines: { display: false },
          suggestedMin: 0,
          suggestedMax: 100,
          pointLabels: { color: 'var(--text)' },
          ticks: { color: 'var(--text)' }
        }
      }
    }
  })
})
</script>

<style scoped>
.risk-panel {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1.2rem;
  background-color: var(--card);
  color: var(--text);
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  gap: 1rem;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.chart-canvas {
  width: 100%;
  max-width: 500px;
  height: 260px;
}

.risk-list {
  list-style: none;
  padding: 0;
  margin: 0;
  width: 100%;
  max-width: 500px;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  font-size: 0.95rem;
}
</style>