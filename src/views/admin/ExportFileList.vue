<template>
  <div class="bg-slate-50 px-4 py-10 md:px-8">
    <div v-if="isLoading" class="flex h-[60vh] flex-col items-center justify-center gap-3 text-slate-500">
      <span class="h-10 w-10 animate-spin rounded-full border-2 border-slate-200 border-t-slate-500" />
      <p>Export verileri yükleniyor...</p>
    </div>
    <div v-else-if="loadError" class="rounded-3xl border border-orange-100 bg-white px-6 py-4 text-center text-sm text-orange-700">
      {{ loadError }}
    </div>
    <template v-else>
      <section class="grid gap-6 lg:grid-cols-[1.4fr_0.8fr]">
      <Card class="bg-gradient-to-r from-slate-900 via-indigo-900 to-violet-900 text-white">
        <CardHeader class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <CardTitle class="text-3xl">Dışa Aktarım Akışı</CardTitle>
            <CardDescription class="text-white/70">
              Son 24 saatte {{ summary.lastDay }} dosya üretildi. Kuyruktaki job sayısı {{ summary.inProgress }}.
            </CardDescription>
          </div>
          <button class="rounded-2xl bg-white px-5 py-2 text-sm font-semibold text-slate-900 shadow-lg shadow-slate-900/40 transition hover:-translate-y-[2px]">
            Yeni Segment Exportu
          </button>
        </CardHeader>
        <CardContent class="grid gap-6 md:grid-cols-3">
          <div v-for="metric in heroMetrics" :key="metric.label" class="space-y-2">
            <p class="text-xs uppercase tracking-[0.3em] text-white/60">{{ metric.label }}</p>
            <p class="text-4xl font-semibold">{{ metric.value }}</p>
            <p class="text-sm text-white/70">{{ metric.hint }}</p>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Export Sağlığı</CardTitle>
          <CardDescription>Servis kuyrukları ve uyarılar</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <Alert v-for="note in healthAlerts" :key="note.id" :title="note.title" :variant="note.variant">
            {{ note.description }}
          </Alert>
        </CardContent>
      </Card>
    </section>

      <section class="mt-8 grid gap-6 lg:grid-cols-3">
      <Card class="lg:col-span-2">
        <CardHeader>
          <CardTitle>Son Exportlar</CardTitle>
          <CardDescription>Dosyaları indir veya bağlantıyı paylaş.</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
              <thead>
                <tr class="text-xs uppercase tracking-[0.3em] text-slate-400">
                  <th class="px-4 py-3">Dosya</th>
                  <th class="px-4 py-3">Format</th>
                  <th class="px-4 py-3">Segment</th>
                  <th class="px-4 py-3">Boyut</th>
                  <th class="px-4 py-3">Tarih</th>
                  <th class="px-4 py-3">Durum</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="file in files" :key="file.id" class="border-t border-slate-100">
                  <td class="px-4 py-4">
                    <p class="text-sm font-semibold text-slate-900">{{ file.name }}</p>
                    <p class="text-xs text-slate-400">{{ file.description }}</p>
                  </td>
                  <td class="px-4 py-4 text-sm text-slate-500">{{ file.format.toUpperCase() }}</td>
                  <td class="px-4 py-4 text-sm text-slate-500">{{ file.segment }}</td>
                  <td class="px-4 py-4 text-sm text-slate-500">{{ file.size }}</td>
                  <td class="px-4 py-4 text-sm text-slate-500">{{ file.date }}</td>
                  <td class="px-4 py-4">
                    <span :class="statusBadge(file.status)">{{ file.status }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Planlı Exportlar</CardTitle>
          <CardDescription>Önümüzdeki job takvimi</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <div v-for="job in scheduledJobs" :key="job.id" class="rounded-2xl border border-slate-100 bg-white p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-semibold text-slate-900">{{ job.title }}</p>
                <p class="text-xs text-slate-400">{{ job.segment }}</p>
              </div>
              <span class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ job.frequency }}</span>
            </div>
            <p class="mt-2 text-sm text-slate-500">{{ job.window }}</p>
          </div>
        </CardContent>
      </Card>
      </section>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import api from '@/services/api'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Alert } from '@/components/ui/alert'

type ExportStatus = 'Hazır' | 'Hazırlanıyor'

interface ExportFile {
  id: number | string
  name: string
  description: string
  format: string
  segment: string
  size: string
  date: string
  status: ExportStatus
}

interface ScheduledJob {
  id: number | string
  title: string
  segment: string
  frequency: string
  window: string
}

const files = ref<ExportFile[]>([])
const scheduledJobs = ref<ScheduledJob[]>([])
const isLoading = ref(true)
const loadError = ref<string | null>(null)

const summary = computed(() => ({
  lastDay: files.value.filter(file => file.status === 'Hazır').length,
  inProgress: files.value.filter(file => file.status === 'Hazırlanıyor').length
}))

const heroMetrics = computed(() => [
  { label: 'Toplam Export', value: files.value.length.toString().padStart(2, '0'), hint: 'Bu hafta oluşturulan dosya' },
  { label: 'Hazır Dosya', value: summary.value.lastDay.toString().padStart(2, '0'), hint: 'Linki paylaşılabilir' },
  { label: 'Kuyruktaki İş', value: summary.value.inProgress.toString().padStart(2, '0'), hint: 'Hazırlanması bekleniyor' }
])

const healthAlerts = computed(() => {
  const queueAlert = summary.value.inProgress
    ? {
        id: 'queue',
        title: `Kuyrukta ${summary.value.inProgress} iş var`,
        description: 'Worker kuyruğunu boşaltmak için ek kapasite açabilirsin.',
        variant: 'orange' as const
      }
    : {
        id: 'queue',
        title: 'Kuyruk boş',
        description: 'Tüm exportlar tamamlandı, yeni job planlayabilirsin.',
        variant: 'emerald' as const
      }

  return [
    {
      id: 'storage',
      title: `${files.value.length} dosya arşivde`,
      description: 'Export dosyaları 30 gün boyunca saklanır.',
      variant: 'default' as const
    },
    queueAlert
  ]
})

function statusBadge(status: ExportStatus) {
  return status === 'Hazır'
    ? 'rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700'
    : 'rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700'
}

function normalizeStatus(status?: string): ExportStatus {
  const value = (status || '').toLowerCase()
  if (['completed', 'ready', 'success', 'hazır'].includes(value)) {
    return 'Hazır'
  }
  return 'Hazırlanıyor'
}

function formatDateTime(date?: string) {
  if (!date) return '—'
  return new Date(date).toLocaleString('tr-TR', {
    day: '2-digit',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function formatBytes(size?: string | number | null) {
  if (size === null || size === undefined) return '—'
  const value = typeof size === 'string' ? Number(size) : size
  if (Number.isNaN(value)) return typeof size === 'string' ? size : '—'
  if (value === 0) return '0 B'
  const units = ['B', 'KB', 'MB', 'GB']
  const index = Math.min(units.length - 1, Math.floor(Math.log(value) / Math.log(1024)))
  const converted = value / Math.pow(1024, index)
  return `${converted.toFixed(converted >= 10 ? 0 : 1)} ${units[index]}`
}

function normalizeFile(log: any): ExportFile {
  const fileName = log.file_name || log.filename || log.name || 'export.csv'
  const format = fileName.split('.').pop()?.toUpperCase() || 'CSV'
  const description = log.export_type || log.description || log.action || 'Segment exportu'
  const sizeValue = log.file_size ?? log.size ?? log.payload_size

  return {
    id: log.id ?? fileName,
    name: fileName,
    description,
    format,
    segment: log.segment || log.vendor_name || log.vendor || 'Genel',
    size: typeof sizeValue === 'string' && sizeValue.includes('B') ? sizeValue : formatBytes(sizeValue),
    date: formatDateTime(log.exported_at || log.created_at || log.updated_at),
    status: normalizeStatus(log.status)
  }
}

function normalizeScheduled(entry: any): ScheduledJob {
  return {
    id: entry.id ?? entry.filename ?? entry.segment,
    title: entry.segment || entry.filename || 'Segment Exportu',
    segment: entry.vendor || entry.vendor_name || 'Genel kanal',
    frequency: entry.schedule || 'Tek seferlik',
    window: formatDateTime(entry.created_at)
  }
}

async function loadExports() {
  isLoading.value = true
  loadError.value = null

  try {
    const [logsRes, scheduledRes] = await Promise.all([
      api.get('/exports/logs'),
      api.get('/exports/scheduled')
    ])

    files.value = Array.isArray(logsRes.data) ? logsRes.data.map(normalizeFile) : []
    scheduledJobs.value = Array.isArray(scheduledRes.data) ? scheduledRes.data.map(normalizeScheduled) : []
  } catch (error) {
    console.error('Export verileri yüklenemedi:', error)
    loadError.value = 'Export logları alınamadı. Lütfen daha sonra tekrar deneyin.'
  } finally {
    isLoading.value = false
  }
}

onMounted(loadExports)
</script>