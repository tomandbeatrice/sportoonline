<template>
  <div class="p-6 bg-arkaplan min-h-screen">
    <h1 class="text-2xl font-bold mb-6">📦 Export Log Dashboard</h1>

    <div v-if="status === 'success'" class="bg-başarı text-white p-4 rounded mb-4">
      İşlem başarıyla tamamlandı!
    </div>
    <div v-else-if="status === 'error'" class="bg-hata text-white p-4 rounded mb-4">
      Bir hata oluştu!
    </div>

    <LogChart :data="chartData" />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
      <LogÖzet :logs="logs" />
      <LogDetay :logs="logs" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import LogChart from './LogChart.vue'
import LogÖzet from './LogÖzet.vue'
import LogDetay from './LogDetay.vue'

const logs = ref([])
const status = ref('')
const chartData = ref([])

onMounted(async () => {
  try {
    const response = await axios.get('/api/export-logs')
    logs.value = response.data.data
    status.value = response.data.status

    // Grafik için veri işleme
    const counts = { başarı: 0, hata: 0, uyarı: 0, bilgi: 0 }
    logs.value.forEach(log => {
      if (counts[log.status] !== undefined) counts[log.status]++
    })
    chartData.value = Object.values(counts)
  } catch (error) {
    status.value = 'error'
  }
})
</script>