<template>
  <div class="card bg-white shadow-md p-4 mb-6">
    <h2 class="text-lg font-semibold mb-2">🧩 Modül Bazlı Log Analizi</h2>
    <Bar :data="chartData" :options="chartOptions" />

    <button @click="exportCSV" class="btn btn-outline btn-sm mt-4">
      📤 CSV Olarak Dışa Aktar
    </button>
  </div>
</template>

<script setup lang="ts">
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

import { computed } from 'vue'

defineProps<{
  logs: Array<{ message: string; status: string }>
}>()

// Modül anahtar kelimeleri
const moduleKeywords: Record<string, string[]> = {
  Export: ['export', 'csv', 'xlsx'],
  Payment: ['payment', 'checkout', 'card'],
  Shipment: ['shipment', 'cargo', 'tracking']
}

// Sağlam keyword eşleşmesi
const keywordMatch = (message: string, keywords: string[]) =>
  keywords.some(k => new RegExp(`\\b${k}\\b`, 'i').test(message))

// Modül istatistiklerini hesapla
const moduleStats = computed(() => {
  const stats: Record<string, { success: number; error: number }> = {}
  Object.keys(moduleKeywords).forEach(modul => {
    stats[modul] = { success: 0, error: 0 }
  })

  logs.forEach(log => {
    for (const [modul, keywords] of Object.entries(moduleKeywords)) {
      if (keywordMatch(log.message, keywords)) {
        if (log.status === 'success') stats[modul].success++
        if (log.status === 'error') stats[modul].error++
      }
    }
  })

  return stats
})

// Reaktif grafik verisi
const chartData = computed(() => ({
  labels: Object.keys(moduleStats.value),
  datasets: [
    {
      label: 'Başarılı',
      backgroundColor: '#2ecc71',
      data: Object.values(moduleStats.value).map(s => s.success)
    },
    {
      label: 'Hatalı',
      backgroundColor: '#e74c3c',
      data: Object.values(moduleStats.value).map(s => s.error)
    }
  ]
}))

// Grafik ayarları
const chartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' },
    tooltip: {
      callbacks: {
        label: (ctx: any) => `${ctx.dataset.label}: ${ctx.raw} adet`
      }
    }
  }
}

// CSV export fonksiyonu
function exportCSV() {
  const rows = Object.entries(moduleStats.value).map(([modul, data]) => ({
    Modül: modul,
    Başarılı: data.success,
    Hatalı: data.error,
    Toplam: data.success + data.error,
    BaşarıOranı:
      data.success + data.error === 0
        ? '0%'
        : ((data.success / (data.success + data.error)) * 100).toFixed(1) + '%'
  }))

  const csv = [
    Object.keys(rows[0]).join(','),
    ...rows.map(row => Object.values(row).join(','))
  ].join('\n')

  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = 'modul-analiz.csv'
  link.click()
}
</script>