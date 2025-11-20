<template>
  <div class="bg-slate-50 px-4 py-10 md:px-8">
    <section class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
      <Card class="bg-gradient-to-r from-slate-900 via-indigo-900 to-fuchsia-900 text-white">
        <CardHeader>
          <CardTitle class="text-3xl">Segment Dışa Aktarım Stüdyosu</CardTitle>
          <CardDescription class="text-white/70">Tek tuşla CRM, BI veya SFTP aktarımı başlat.</CardDescription>
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
          <CardTitle>Kayıtlı Segmentler</CardTitle>
          <CardDescription>Hazır şablonları kullanarak filtreleri otomatik doldur.</CardDescription>
        </CardHeader>
        <CardContent>
          <Accordion type="single" collapsible defaultValue="segment-1">
            <AccordionItem v-for="segment in savedSegments" :key="segment.id" :value="segment.id">
              <div class="p-4">
                <AccordionTrigger>
                  {{ segment.title }} · <span class="text-slate-400">{{ segment.audience }}</span>
                </AccordionTrigger>
                <AccordionContent class="space-y-3 text-sm text-slate-500">
                  <p>{{ segment.description }}</p>
                  <button class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600" @click="prefillSegment(segment)">
                    Filtreleri Uygula
                  </button>
                </AccordionContent>
              </div>
            </AccordionItem>
          </Accordion>
        </CardContent>
      </Card>
    </section>

    <section class="mt-8 grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
      <Card class="space-y-6">
        <CardHeader>
          <CardTitle>Export Ayarları</CardTitle>
          <CardDescription>Zaman aralığı, segment ve format seç.</CardDescription>
        </CardHeader>
        <CardContent class="space-y-6">
          <div class="grid gap-4 md:grid-cols-2">
            <label class="space-y-2 text-sm font-semibold text-slate-700">
              Başlangıç Tarihi
              <input v-model="startDate" type="date" class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm focus:border-slate-900 focus:outline-none" />
            </label>
            <label class="space-y-2 text-sm font-semibold text-slate-700">
              Bitiş Tarihi
              <input v-model="endDate" type="date" class="w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm focus:border-slate-900 focus:outline-none" />
            </label>
          </div>

          <label class="space-y-2 text-sm font-semibold text-slate-700">
            Segment
            <select v-model="selectedSegment" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm focus:border-slate-900 focus:outline-none">
              <option value="" disabled>Segment seçin</option>
              <option v-for="segment in segmentOptions" :key="segment.id" :value="segment.id">
                {{ segment.label }}
              </option>
            </select>
          </label>

          <div>
            <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Çıktı Formatı</p>
            <Tabs v-model="exportFormat" defaultValue="excel" class="mt-3 space-y-4">
              <TabsList class="flex gap-3">
                <TabsTrigger value="excel" class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em]" active-class="border-transparent bg-slate-900 text-white shadow" inactive-class="border-slate-200 bg-white text-slate-500">
                  Excel
                </TabsTrigger>
                <TabsTrigger value="csv" class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em]" active-class="border-transparent bg-slate-900 text-white shadow" inactive-class="border-slate-200 bg-white text-slate-500">
                  CSV
                </TabsTrigger>
              </TabsList>
            </Tabs>
          </div>

          <div class="grid gap-3 md:grid-cols-2">
            <label v-for="field in exportFields" :key="field.id" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600">
              <input v-model="includedFields" :value="field.id" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900" />
              {{ field.label }}
            </label>
          </div>

          <div class="flex flex-wrap gap-3">
            <button class="rounded-2xl border border-slate-200 px-5 py-2 text-sm font-semibold text-slate-600" @click="resetForm">
              Temizle
            </button>
            <button :disabled="!canExport" class="rounded-2xl bg-slate-900 px-6 py-2 text-sm font-semibold text-white transition disabled:cursor-not-allowed disabled:opacity-50" @click="triggerExport">
              Exportu Başlat
            </button>
          </div>

          <Alert v-if="exportResult" :title="exportResult.title" :variant="exportResult.variant">
            {{ exportResult.message }}
          </Alert>
        </CardContent>
      </Card>

      <Card class="space-y-6">
        <CardHeader>
          <CardTitle>Son Exportlar</CardTitle>
          <CardDescription>Yakın zamanda indirilen dosyalar</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <div v-for="item in recentExports" :key="item.id" class="rounded-2xl border border-slate-100 bg-white p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-semibold text-slate-900">{{ item.title }}</p>
                <p class="text-xs text-slate-400">{{ item.format }} · {{ item.size }}</p>
              </div>
              <span class="text-xs text-slate-400">{{ item.time }}</span>
            </div>
            <p class="mt-2 text-sm text-slate-500">{{ item.segment }}</p>
          </div>
        </CardContent>
      </Card>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion'
import { Alert } from '@/components/ui/alert'

type ExportFormat = 'excel' | 'csv'

interface SegmentOption {
  id: string
  label: string
}

interface FormState {
  startDate: string
  endDate: string
  selectedSegment: string
}

interface SavedSegment {
  id: string
  title: string
  audience: string
  description: string
  preset: Pick<FormState, 'selectedSegment' | 'startDate' | 'endDate'>
}

interface RecentExport {
  id: number
  title: string
  format: string
  size: string
  segment: string
  time: string
}

const formState = ref<FormState>({ startDate: '', endDate: '', selectedSegment: '' })
const exportFormat = ref<ExportFormat>('excel')
const includedFields = ref<string[]>(['contact'])
const exportResult = ref<{ title: string; message: string; variant: 'default' | 'emerald' } | null>(null)

const segmentOptions: SegmentOption[] = [
  { id: 'loyalty-max', label: 'Sadakat üyeleri (Max)' },
  { id: 'risk-ops', label: 'Risk ops · iade eğilimi' },
  { id: 'seller-pro', label: 'Satıcı pro · yüksek hacim' }
]

const savedSegments: SavedSegment[] = [
  {
    id: 'segment-1',
    title: 'Sadakat Elite',
    audience: '104K üye',
    description: 'RFM skoru 4+ ve son 60 günde alışveriş yapan kitle.',
    preset: { selectedSegment: 'loyalty-max', startDate: '2025-10-01', endDate: '2025-11-01' }
  },
  {
    id: 'segment-2',
    title: 'Risk Ops İleri',
    audience: '12K sipariş',
    description: 'İade riski %25 üzeri siparişler.',
    preset: { selectedSegment: 'risk-ops', startDate: '2025-11-01', endDate: '2025-11-17' }
  }
]

const exportFields = [
  { id: 'contact', label: 'İletişim bilgileri' },
  { id: 'order', label: 'Sipariş geçmişi' },
  { id: 'rfm', label: 'RFM skorları' },
  { id: 'preferences', label: 'Tercihler' }
]

const recentExports: RecentExport[] = [
  { id: 1, title: 'Sadakat Elite', format: 'Excel', size: '12 MB', segment: '104K üye', time: 'Bugün · 08:12' },
  { id: 2, title: 'Risk Ops', format: 'CSV', size: '16 MB', segment: '12K sipariş', time: 'Dün · 22:05' },
  { id: 3, title: 'Seller Pro', format: 'Excel', size: '8 MB', segment: 'Top 500', time: 'Dün · 18:30' }
]

const heroMetrics = computed(() => [
  { label: 'Son 24 Saat', value: '18', hint: 'Tamamlanan export' },
  { label: 'Ortalama SLA', value: '03:14', hint: 'Job başına dakika' },
  { label: 'Aktif Kuyruk', value: '04', hint: 'Hazırlanan çıktı' }
])

const canExport = computed(() => {
  const { startDate, endDate, selectedSegment } = formState.value
  return Boolean(startDate && endDate && selectedSegment)
})

const startDate = computed({
  get: () => formState.value.startDate,
  set: value => (formState.value.startDate = value)
})

const endDate = computed({
  get: () => formState.value.endDate,
  set: value => (formState.value.endDate = value)
})

const selectedSegment = computed({
  get: () => formState.value.selectedSegment,
  set: value => (formState.value.selectedSegment = value)
})

function triggerExport() {
  if (!canExport.value) return
  exportResult.value = {
    title: 'Export kuyruğa alındı',
    message: `${segmentLabel(selectedSegment.value)} segmenti ${exportFormat.value.toUpperCase()} olarak hazırlanıyor.`,
    variant: 'emerald'
  }
}

function segmentLabel(id: string) {
  return segmentOptions.find(option => option.id === id)?.label ?? 'Bilinmeyen segment'
}

function prefillSegment(segment: SavedSegment) {
  formState.value = { ...segment.preset }
}

function resetForm() {
  formState.value = { startDate: '', endDate: '', selectedSegment: '' }
  includedFields.value = ['contact']
  exportFormat.value = 'excel'
  exportResult.value = null
}

</script>