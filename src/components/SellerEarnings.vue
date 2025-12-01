<template>
  <section class="p-4 bg-white rounded shadow">
    <h2 class="text-lg font-bold text-emerald-700">💰 Gelir Özeti</h2>
    <div class="mt-4">
      <p>Toplam Sipariş: ₺{{ total }}</p>
      <p>Komisyon Oranı: %{{ commission }}</p>
      <p>Net Gelir: ₺{{ net }}</p>
    </div>
    <canvas ref="chartCanvas" height="200" class="mt-6"></canvas>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Chart } from 'chart.js/auto'

const chartCanvas = ref(null)
const total = ref(0)
const commission = ref(0)
const net = ref(0)

onMounted(async () => {
  const sellerId = 1 // auth üzerinden dinamik alınabilir
  const res = await axios.get(`/api/seller/${sellerId}/invoice`)
  total.value = res.data.total
  commission.value = res.data.commission_rate
  net.value = res.data.net_income

  new Chart(chartCanvas.value, {
    type: 'bar',
    data: {
      labels: ['Toplam', 'Net'],
      datasets: [{
        label: '₺ Gelir',
        data: [total.value, net.value],
        backgroundColor: ['#10b981', '#3b82f6']
      }]
    },
    options: {
      scales: { y: { beginAtZero: true } },
      plugins: { legend: { display: false } }
    }
  })
})
</script>