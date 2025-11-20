<template>
  <section class="export-panel">
    <h2>Sprint Trend Grafiği</h2>
    <canvas ref="trendChart" class="chart-canvas" />
    <button @click="generatePDF">📄 PDF Export</button>
    <a v-if="reportUrl" :href="reportUrl" target="_blank" class="report-link">🔗 Raporu Görüntüle</a>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import Chart from 'chart.js/auto'
import jsPDF from 'jspdf'
import html2canvas from 'html2canvas'
import { useSprintStore } from '@/stores/sprint'

const sprint = useSprintStore()
const trendChart = ref<HTMLCanvasElement | null>(null)
const reportUrl = ref<string | null>(null)

const progress = computed(() =>
  sprint.modules.reduce((acc, m) => acc + m.progress, 0) / sprint.modules.length
)

const predictedSuccess = computed(() =>
  Math.round(progress.value * 0.7 + 30)
)

onMounted(() => {
  if (!trendChart.value) return

  new Chart(trendChart.value, {
    type: 'line',
    data: {
      labels: ['Sprint 1', 'Sprint 2', 'Sprint 3'],
      datasets: [{
        label: 'Modül Aktivitesi',
        data: [12, 19, 14],
        borderColor: '#4A90E2',
        backgroundColor: 'rgba(74,144,226,0.2)',
        tension: 0.4,
        fill: true,
        pointRadius: 4,
        pointBackgroundColor: '#4A90E2'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { mode: 'index', intersect: false }
      },
      scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true }
      }
    }
  })
})

const generatePDF = async () => {
  if (!trendChart.value) return

  const canvas = await html2canvas(trendChart.value)
  const imgData = canvas.toDataURL('image/png')
  const pdf = new jsPDF()

  pdf.setFontSize(14)
  pdf.text('Sprint Trend Raporu', 10, 10)
  pdf.addImage(imgData, 'PNG', 10, 20, 180, 80)

  pdf.setFontSize(12)
  pdf.text(`Roadmap İlerleme: %${Math.round(progress.value)}`, 10, 110)
  pdf.text(`Tahmini Sprint Başarısı: %${predictedSuccess.value}`, 10, 120)

  const blob = pdf.output('blob')
  const formData = new FormData()
  formData.append('file', blob, 'sprint-report.pdf')

  const response = await fetch('/api/upload-sprint-report', {
    method: 'POST',
    body: formData
  })

  const { url } = await response.json()
  reportUrl.value = url
}
</script>

<style scoped>
.export-panel {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1.5rem;
  background-color: var(--card);
  color: var(--text);
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  gap: 1.5rem;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.chart-canvas {
  width: 100%;
  max-width: 600px;
  height: 300px;
}

button {
  background-color: var(--accent);
  color: #fff;
  font-weight: 600;
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
button:hover {
  background-color: #e65c2a;
}

.report-link {
  font-weight: 500;
  color: var(--accent);
  text-decoration: underline;
}
</style>
