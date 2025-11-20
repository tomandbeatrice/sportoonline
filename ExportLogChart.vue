<template>
  <section class="export-log-chart">
    <h2>📊 Export Log Analizi</h2>

    <div class="filters">
      <label>
        Başlangıç Tarihi:
        <input type="date" v-model="dateFrom" />
      </label>
      <label>
        Bitiş Tarihi:
        <input type="date" v-model="dateTo" />
      </label>
      <label>
        Admin Seç:
        <select v-model="selectedAdminId">
          <option value="">Tümü</option>
          <option v-for="id in adminIds" :key="id" :value="id">Admin #{{ id }}</option>
        </select>
      </label>
      <label>
        Dosya Adı:
        <input type="text" v-model="filename" placeholder="export_2025..." />
      </label>
      <label>
        Kampanya ID:
        <input type="text" v-model="campaignId" placeholder="Örn: 42" />
      </label>
      <label>
        Export Tipi:
        <select v-model="exportType">
          <option value="">Tümü</option>
          <option value="csv">CSV</option>
          <option value="xlsx">XLSX</option>
          <option value="pdf">PDF</option>
        </select>
      </label>
      <button @click="fetchLogs" :disabled="loading">
        {{ loading ? 'Yükleniyor...' : 'Filtrele' }}
      </button>
    </div>

    <div v-if="error" class="error">{{ error }}</div>
    <canvas ref="chartCanvas" v-show="!error && !loading"></canvas>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

const chartCanvas = ref<HTMLCanvasElement | null>(null)
const dateFrom = ref('')
const dateTo = ref('')
const selectedAdminId = ref('')
const filename = ref('')
const campaignId = ref('')
const exportType = ref('')
const adminIds = ref<number[]>([])
const loading = ref(false)
const error = ref('')
let chartInstance: Chart | null = null

const fetchLogs = async () => {
  loading.value = true
  error.value = ''

  try {
    const res = await axios.get('/api/admin/payment-export-logs', {
      headers: {
        Authorization: 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...', // gerçek token
        Accept: 'application/json'
      },
      params: {
        date_from: dateFrom.value,
        date_to: dateTo.value,
        search: selectedAdminId.value,
        filename: filename.value,
        campaign_id: campaignId.value,
        type: exportType.value
      }
    })

    const logs = res.data.data

    if (!logs.length) {
      error.value = 'Veri bulunamadı.'
      loading.value = false
      return
    }

    adminIds.value = Array.from(new Set(logs.map((log: any) => log.admin_id)))

    const grouped: Record<string, number> = {}

    logs.forEach((log: any) => {
      const date = new Date(log.exported_at).toLocaleDateString('tr-TR')
      grouped[date] = (grouped[date] || 0) + 1
    })

    const labels = Object.keys(grouped).sort()
    const data = Object.values(grouped)

    if (chartInstance) chartInstance.destroy()

    chartInstance = new Chart(chartCanvas.value!, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: 'Export Sayısı',
            data,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0,123,255,0.1)',
            fill: true,
            tension: 0.3
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' },
          title: { display: true, text: 'Export Log Zaman Analizi' }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    })
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Veri alınırken hata oluştu.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchLogs)
</script>

<style scoped>
.export-log-chart {
  max-width: 960px;
  margin: auto;
  padding: 1rem;
}
.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
  align-items: flex-end;
}
label {
  display: flex;
  flex-direction: column;
  font-size: 0.9rem;
}
input, select {
  padding: 0.4rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}
button {
  padding: 0.5rem 1rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
button:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}
canvas {
  width: 100%;
  height: 450px;
}
.error {
  color: #c0392b;
  font-weight: bold;
  margin-top: 1rem;
  text-align: center;
}
</style>