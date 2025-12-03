<template>
  <section class="p-6 bg-white rounded shadow">
    <h2 class="text-xl font-bold text-purple-700">🧠 Kampanya Strateji Haritası</h2>
    <div class="grid grid-cols-2 gap-6 mt-6">
      <canvas ref="barChart" height="200"></canvas>
      <canvas ref="lineChart" height="200"></canvas>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Chart } from 'chart.js/auto'

const barChart = ref(null)
const lineChart = ref(null)

onMounted(async () => {
  const res = await axios.get('/api/admin/campaign-impact-trend')
  const data = res.data

  const tags = Object.keys(data)
  const weeks = data[tags[0]].map(d => d.week)

  const datasets = tags.map(tag => ({
    label: tag,
    data: data[tag].map(d => d.impact_score),
    borderColor: tag === 'SPORTOON100' ? '#10b981' : tag === 'BETA50' ? '#3b82f6' : '#f59e0b',
    fill: false
  }))

  new Chart(lineChart.value, {
    type: 'line',
    data: { labels: weeks, datasets },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  })

  const participationRes = await axios.get('/api/admin/campaign-participation-effect')
  const barData = participationRes.data

  new Chart(barChart.value, {
    type: 'bar',
    data: {
      labels: barData.map(d => d.tag),
      datasets: [{
        label: 'Dönüşüm Oranı (%)',
        data: barData.map(d => d.conversion_rate),
        backgroundColor: '#6366f1'
      }]
    },
    options: {
      scales: { y: { beginAtZero: true } },
      plugins: { legend: { display: false } }
    }
  })
})
</script>