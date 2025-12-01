<template>
  <section class="p-4 bg-white rounded shadow">
    <h3 class="text-lg font-bold text-emerald-700 mb-4">🧠 Satıcı Davranış Haritası</h3>
    <canvas ref="radarCanvas" height="300"></canvas>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Chart } from 'chart.js/auto'

const radarCanvas = ref(null)

onMounted(async () => {
  try {
    const sellerId = 1 // auth üzerinden dinamik alınabilir
    const { data } = await axios.get(`/api/seller/${sellerId}/behavior-summary`)

    const chartData = {
      labels: ['Katılım', 'Dönüşüm', 'Çeşitlilik', 'Geri Bildirim', 'Davet'],
      datasets: [{
        label: 'Satıcı Davranışı',
        data: [
          data.participation_rate,
          data.conversion_rate,
          data.campaign_diversity,
          data.feedback_score,
          data.invitation_effect
        ],
        backgroundColor: 'rgba(16, 185, 129, 0.2)',
        borderColor: '#10b981',
        pointBackgroundColor: '#10b981'
      }]
    }

    new Chart(radarCanvas.value, {
      type: 'radar',
      data: chartData,
      options: {
        responsive: true,
        scales: {
          r: {
            beginAtZero: true,
            max: 100,
            ticks: {
              stepSize: 20,
              backdropColor: 'transparent'
            },
            pointLabels: {
              font: {
                size: 12
              }
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    })
  } catch (err) {
    console.error('Davranış verisi alınamadı:', err)
  }
})
</script>