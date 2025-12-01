<template>
  <section class="p-4">
    <h2 class="text-lg font-bold text-purple-700 flex items-center gap-2">
      <BadgeIcon name="trending-up" cls="w-6 h-6" /> Haftalık Planlama Başarı Trendi
    </h2>
    <canvas ref="chartCanvas" height="200"></canvas>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { Chart } from 'chart.js/auto'
import BadgeIcon from '@/components/icons/BadgeIcon.vue'

const chartCanvas = ref(null)

onMounted(async () => {
  const res = await axios.get('/api/admin/auto-plan-trend')
  const logs = res.data

  const labels = logs.map(log => log.run_at.split('T')[0])
  const data = logs.map(log => log.planned_count)

  new Chart(chartCanvas.value, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Planlanan Kampanya Sayısı',
        data,
        backgroundColor: '#7c3aed'
      }]
    },
    options: {
      scales: {
        y: { beginAtZero: true }
      }
    }
  })
})
</script>