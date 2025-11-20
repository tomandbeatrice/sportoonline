<template>
  <section class="gauge-panel">
    <h3>Roadmap İlerlemesi</h3>
    <canvas ref="gaugeCanvas" class="gauge-canvas" />
    <p class="gauge-label">
      Toplam İlerleme: <span class="gauge-value">%{{ progress }}</span>
    </p>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import Chart from 'chart.js/auto'
import { useSprintStore } from '@/stores/sprint'

const sprint = useSprintStore()
const gaugeCanvas = ref<HTMLCanvasElement | null>(null)

const progress = computed(() =>
  Math.round(
    sprint.modules.reduce((acc, m) => acc + m.progress, 0) / sprint.modules.length
  )
)

onMounted(() => {
  if (!gaugeCanvas.value) return

  new Chart(gaugeCanvas.value, {
    type: 'doughnut',
    data: {
      labels: ['İlerleme', 'Kalan'],
      datasets: [{
        data: [progress.value, 100 - progress.value],
        backgroundColor: ['var(--accent)', 'rgba(200,200,200,0.3)'],
        borderWidth: 0
      }]
    },
    options: {
      cutout: '80%',
      plugins: {
        legend: { display: false },
        tooltip: { enabled: false }
      }
    }
  })
})
</script>

<style scoped>
.gauge-panel {
  background-color: var(--card);
  color: var(--text);
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.gauge-canvas {
  width: 160px;
  height: 160px;
}

.gauge-label {
  font-size: 1rem;
  font-weight: 600;
}

.gauge-value {
  color: var(--accent);
}
</style>