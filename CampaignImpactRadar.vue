<template>
  <section class="p-4">
    <h2 class="text-lg font-bold text-indigo-700">🧭 Kampanya Strateji Haritası</h2>
    <canvas ref="chartCanvas" height="300"></canvas>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Chart } from 'chart.js/auto'

const chartCanvas = ref(null)

onMounted(async () => {
  const res = await axios.get('/api/admin/campaign-impact-score?days=7')
  const data = res.data

  const labels = ['Kullanıcı', 'Dönüşüm', 'Dönüşüm Oranı', 'Sürdürülebilirlik', 'Etki Puanı']

  const datasets = data.map((d, i) => ({
    label: d.tag,
    data: [
      d.users,
      d.converted,
      d.conversion_rate,
      d.sustainability,
      d.impact_score
    ],
    backgroundColor: `rgba(${100 + i * 50}, ${150 - i * 30}, ${200 - i * 40}, 0.2)`,
    borderColor: `rgba(${100 + i * 50}, ${150 - i * 30}, ${200 - i * 40}, 1)`,
    borderWidth: 2
  }))

  new Chart(chartCanvas.value, {
    type: 'radar',
    data: {
      labels,
      datasets
    },
    options: {
      responsive: true,
      scales: {
        r: {
          beginAtZero: true,
          angleLines: { display: true },
          suggestedMax: 100
        }
      },
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  })
})
</script>