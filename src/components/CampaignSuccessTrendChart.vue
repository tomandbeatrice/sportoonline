<template>
  <section class="p-4">
    <h2 class="text-lg font-bold text-purple-700">📈 Kampanya Türü Başarı Eğrisi</h2>
    <canvas ref="chartCanvas" height="300"></canvas>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Chart } from 'chart.js/auto'

const chartCanvas = ref(null)

onMounted(async () => {
  const res = await axios.get('/api/admin/campaign-success-trend?days=14')
  const data = res.data

  const labels = data.map(d => d.date)

  const sportoonUsers = data.map(d => d.SPORTOON100.users)
  const sportoonConverted = data.map(d => d.SPORTOON100.converted)

  const betaUsers = data.map(d => d.BETA50.users)
  const betaConverted = data.map(d => d.BETA50.converted)

  const organicUsers = data.map(d => d.Organik.users)
  const organicConverted = data.map(d => d.Organik.converted)

  new Chart(chartCanvas.value, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          label: 'SPORTOON100 Kullanıcı',
          data: sportoonUsers,
          borderColor: '#4f46e5',
          backgroundColor: '#4f46e5',
          fill: false
        },
        {
          label: 'SPORTOON100 Dönüşüm',
          data: sportoonConverted,
          borderColor: '#a78bfa',
          backgroundColor: '#a78bfa',
          fill: false
        },
        {
          label: 'BETA50 Kullanıcı',
          data: betaUsers,
          borderColor: '#059669',
          backgroundColor: '#059669',
          fill: false
        },
        {
          label: 'BETA50 Dönüşüm',
          data: betaConverted,
          borderColor: '#34d399',
          backgroundColor: '#34d399',
          fill: false
        },
        {
          label: 'Organik Kullanıcı',
          data: organicUsers,
          borderColor: '#f59e0b',
          backgroundColor: '#f59e0b',
          fill: false
        },
        {
          label: 'Organik Dönüşüm',
          data: organicConverted,
          borderColor: '#fbbf24',
          backgroundColor: '#fbbf24',
          fill: false
        }
      ]
    },
    options: {
      responsive: true,
      interaction: { mode: 'index', intersect: false },
      stacked: false,
      plugins: {
        legend: { position: 'bottom' }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  })
})
</script>