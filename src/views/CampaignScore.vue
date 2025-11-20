<!-- src/views/CampaignScore.vue -->
<template>
  <section class="p-6 bg-white rounded shadow min-h-screen">
    <h2 class="text-xl font-bold mb-4">📊 Kampanya Skor Detayları</h2>

    <div class="flex gap-4 mb-6">
      <select v-model="selectedSource" class="select select-bordered">
        <option value="">Tüm Kaynaklar</option>
        <option value="organic">Organik</option>
        <option value="beta">Beta</option>
        <option value="early">Early Access</option>
      </select>
      <button @click="applyFilter" class="btn btn-primary">Filtrele</button>
    </div>

    <canvas ref="scoreChart" height="120"></canvas>

    <table class="table table-zebra w-full mt-6 text-sm">
      <thead>
        <tr>
          <th>Tarih</th>
          <th>Skor</th>
          <th>Kaynak</th>
          <th>Kategori</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in filteredScores" :key="item.date + item.source">
          <td>{{ item.date }}</td>
          <td>{{ item.score }}</td>
          <td>{{ item.source }}</td>
          <td>{{ item.category }}</td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'
import Chart from 'chart.js/auto'

type ScoreItem = {
  date: string
  score: number
  source: string
  category: string
}

const scores = ref<ScoreItem[]>([])
const filteredScores = ref<ScoreItem[]>([])
const selectedSource = ref('')
const scoreChart = ref<HTMLCanvasElement | null>(null)
let chartInstance: Chart | null = null

const fetchScores = async () => {
  try {
    const { data } = await axios.get('/admin/campaign-score-dashboard')
    scores.value = data
    filteredScores.value = data
    renderChart(data)
  } catch (err) {
    console.error('Skor verisi alınamadı:', err)
  }
}

const applyFilter = () => {
  filteredScores.value = selectedSource.value
    ? scores.value.filter(s => s.source === selectedSource.value)
    : scores.value
  renderChart(filteredScores.value)
}

const renderChart = (data: ScoreItem[]) => {
  const labels = data.map(item => item.date)
  const values = data.map(item => item.score)

  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(scoreChart.value!, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Skor',
        data: values,
        backgroundColor: '#3B82F6',
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Skor'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Tarih'
          }
        }
      }
    }
  })
}

onMounted(fetchScores)
</script>

<style scoped>
canvas {
  max-width: 100%;
}
</style>