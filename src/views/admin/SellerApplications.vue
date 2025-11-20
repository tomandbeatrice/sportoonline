<template>
  <div class="bg-slate-50 px-4 py-10 md:px-8">
    <section class="mb-8 grid gap-6 lg:grid-cols-[1.4fr_0.8fr]">
      <Card class="bg-gradient-to-r from-indigo-900 via-violet-900 to-slate-900 text-white">
        <CardHeader class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <CardTitle class="text-3xl">Satıcı Başvuru Kokpiti</CardTitle>
            <CardDescription class="text-white/70">
              Başvuruların onay SLA'sı {{ approvalSla }}. Şu an {{ statusSummary.pending }} bekleyen talep var.
            </CardDescription>
          </div>
          <div class="flex flex-wrap gap-3">
            <button class="rounded-2xl border border-white/30 px-4 py-2 text-sm font-semibold text-white/80 transition hover:border-white hover:text-white">
              Kılavuzu Paylaş
            </button>
            <button class="rounded-2xl bg-white px-5 py-2 text-sm font-semibold text-slate-900 shadow-lg shadow-slate-900/40 transition hover:-translate-y-[2px]">
              Otomatik Onay Akışı
            </button>
          </div>
        </CardHeader>
        <CardContent class="grid gap-6 md:grid-cols-3">
          <div v-for="metric in summaryMetrics" :key="metric.id" class="space-y-2">
            <p class="text-xs uppercase tracking-[0.4em] text-white/60">{{ metric.label }}</p>
            <p class="text-4xl font-semibold">{{ metric.value }}</p>
            <p class="text-sm text-white/70">{{ metric.hint }}</p>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>İstikrar Notları</CardTitle>
          <CardDescription>Başvuru akışıyla ilgili öneriler</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <Alert v-for="note in reviewAlerts" :key="note.id" :title="note.title" :variant="note.variant">
            {{ note.description }}
          </Alert>
        </CardContent>
      </Card>
    </section>

    <section>
      <Card class="space-y-6">
        <CardHeader class="gap-4">
          <div>
            <CardTitle>Başvuru Listesi</CardTitle>
            <CardDescription>Filtreleyerek bekleyen satıcıları hızla değerlendir.</CardDescription>
          </div>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="filter in statusFilters"
              :key="filter.id"
              class="rounded-2xl border px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] transition"
              :class="selectedStatus === filter.id ? 'border-transparent bg-slate-900 text-white shadow' : 'border-slate-200 bg-white text-slate-500'"
              @click="selectedStatus = filter.id"
            >
              {{ filter.label }}
              <span class="ml-2 rounded-full bg-black/10 px-2 py-0.5 text-[10px]" v-if="filter.count !== undefined">
                {{ filter.count }}
              </span>
            </button>
          </div>
        </CardHeader>

        <CardContent>
          <div v-if="loading" class="flex min-h-[240px] items-center justify-center text-slate-500">
            Başvurular yükleniyor...
          </div>

          <div v-else>
            <div class="overflow-x-auto">
              <table class="min-w-full text-left text-sm">
                <thead>
                  <tr class="text-xs uppercase tracking-[0.3em] text-slate-400">
                    <th class="px-4 py-3">Satıcı</th>
                    <th class="px-4 py-3">E-posta</th>
                    <th class="px-4 py-3">Mağaza</th>
                    <th class="px-4 py-3">Başvuru</th>
                    <th class="px-4 py-3">Durum</th>
                    <th class="px-4 py-3">İşlem</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="application in filteredApplications" :key="application.id" class="border-t border-slate-100">
                    <td class="px-4 py-4 text-sm font-semibold text-slate-900">
                      {{ application.user.name }}
                      <p class="text-xs text-slate-400">{{ application.user.company_role || '—' }}</p>
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-500">{{ application.user.email }}</td>
                    <td class="px-4 py-4 text-sm text-slate-900">{{ application.name || application.store_name || '—' }}</td>
                    <td class="px-4 py-4 text-sm text-slate-500">{{ formatDate(application.created_at) }}</td>
                    <td class="px-4 py-4">
                      <span :class="statusBadge(application.status)">
                        {{ statusText(application.status) }}
                      </span>
                    </td>
                    <td class="px-4 py-4 text-sm font-semibold">
                      <div v-if="application.status === 'pending'" class="flex flex-wrap gap-2">
                        <button class="text-slate-500 underline-offset-2 hover:text-slate-900 hover:underline" @click="viewDetails(application)">
                          Detay
                        </button>
                        <button class="text-emerald-600 hover:text-emerald-800" @click="approve(application.id)">
                          Onayla
                        </button>
                        <button class="text-orange-600 hover:text-orange-800" @click="reject(application.id)">
                          Reddet
                        </button>
                      </div>
                      <span v-else class="text-xs uppercase tracking-[0.3em] text-slate-400">
                        {{ statusText(application.status) }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="filteredApplications.length === 0" class="py-16 text-center text-sm text-slate-500">
              Filtreye uygun başvuru bulunamadı.
            </div>
          </div>
        </CardContent>
      </Card>
    </section>

    <transition name="fade">
      <div
        v-if="selectedApplication"
        class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/60 px-4 py-10"
        @click="closeDetails"
      >
        <Card class="max-h-[90vh] w-full max-w-3xl overflow-y-auto bg-white" @click.stop>
          <CardHeader class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <CardTitle>Başvuru Detayları</CardTitle>
              <CardDescription>{{ selectedApplication?.name || selectedApplication?.store_name }}</CardDescription>
            </div>
            <span v-if="selectedApplication" :class="statusBadge(selectedApplication.status)">
              {{ statusText(selectedApplication.status) }}
            </span>
          </CardHeader>
          <CardContent class="space-y-6">
            <div class="grid gap-6 md:grid-cols-2">
              <div v-for="field in detailFields" :key="field.id">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ field.label }}</p>
                <p class="text-sm font-semibold text-slate-900">{{ detailValue(field.id) }}</p>
              </div>
            </div>
            <div class="flex flex-wrap justify-end gap-3">
              <button class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600" @click="closeDetails">
                Kapat
              </button>
              <button
                v-if="selectedApplication?.status === 'pending'"
                class="rounded-2xl bg-orange-100 px-4 py-2 text-sm font-semibold text-orange-700"
                @click="handleDecision('reject')"
              >
                Reddet
              </button>
              <button
                v-if="selectedApplication?.status === 'pending'"
                class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white"
                @click="handleDecision('approve')"
              >
                Onayla
              </button>
            </div>
          </CardContent>
        </Card>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import api from '@/services/api'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Alert } from '@/components/ui/alert'

type ApplicationStatus = 'pending' | 'approved' | 'rejected'

interface SellerUser {
  name: string
  email: string
  company_role?: string
}

interface SellerApplication {
  id: number
  user: SellerUser
  name?: string
  store_name?: string
  business_description?: string
  phone?: string
  tax_number?: string
  address?: string
  bank_account?: string
  status: ApplicationStatus
  status_tr?: string
  created_at: string
}

const applications = ref<SellerApplication[]>([])
const loading = ref(true)
const selectedApplication = ref<SellerApplication | null>(null)
const selectedStatus = ref<'all' | ApplicationStatus>('all')

const statusSummary = computed(() => {
  return applications.value.reduce(
    (acc, application) => {
      acc[application.status] += 1
      return acc
    },
    { pending: 0, approved: 0, rejected: 0 }
  )
})

const statusFilters = computed(() => [
  { id: 'all' as const, label: 'Tümü', count: applications.value.length },
  { id: 'pending' as const, label: 'Bekleyen', count: statusSummary.value.pending },
  { id: 'approved' as const, label: 'Onaylanan', count: statusSummary.value.approved },
  { id: 'rejected' as const, label: 'Reddedilen', count: statusSummary.value.rejected }
])

const summaryMetrics = computed(() => [
  {
    id: 'pending',
    label: 'Bekleyen Başvuru',
    value: formatNumber(statusSummary.value.pending),
    hint: 'Manuel inceleme bekleyen talep'
  },
  {
    id: 'approved',
    label: 'Son 7 Gün Çıkan',
    value: formatNumber(statusSummary.value.approved),
    hint: 'Otomatik onaylanan satıcı'
  },
  {
    id: 'sla',
    label: 'Ortalama SLA',
    value: approvalSla,
    hint: 'Başvurudan karara kadarki süre'
  }
])

const filteredApplications = computed(() => {
  if (selectedStatus.value === 'all') {
    return applications.value
  }
  return applications.value.filter(application => application.status === selectedStatus.value)
})

const reviewAlerts = computed(() => {
  const pending = statusSummary.value.pending
  const approved = statusSummary.value.approved
  return [
    {
      id: 'pending',
      title: `${pending} bekleyen başvuru`,
      description: pending
        ? 'Operasyon kuyruğunu boşaltmak için manuel inceleme bekliyor.'
        : 'Bekleyen başvuru yok. Otomasyon çalışıyor.',
      variant: pending > 0 ? 'orange' : 'emerald'
    },
    {
      id: 'automation',
      title: 'Otomatik onay motoru',
      description: approved
        ? `${approved} başvuru otomatik olarak onaylandı.`
        : 'Son 24 saatte otomatik onaylanan başvuru bulunmuyor.',
      variant: approved > 0 ? 'emerald' : 'default'
    }
  ]
})

const detailFields = [
  { id: 'user.name', label: 'Satıcı Adı' },
  { id: 'user.email', label: 'E-posta' },
  { id: 'name', label: 'Mağaza' },
  { id: 'business_description', label: 'İş Açıklaması' },
  { id: 'phone', label: 'Telefon' },
  { id: 'tax_number', label: 'Vergi No' },
  { id: 'address', label: 'Adres' },
  { id: 'bank_account', label: 'Banka Hesabı' }
]

const approvalSla = computed(() => {
  if (!applications.value.length) return '—'
  const now = Date.now()
  const totalMinutes = applications.value.reduce((acc, application) => {
    const created = new Date(application.created_at).getTime()
    return acc + Math.max(0, (now - created) / (1000 * 60))
  }, 0)
  const avg = totalMinutes / applications.value.length
  return `${Math.max(1, Math.round(avg))} dk`
})

function statusBadge(status: ApplicationStatus) {
  const map: Record<ApplicationStatus, string> = {
    pending: 'rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800',
    approved: 'rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700',
    rejected: 'rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700'
  }
  return map[status]
}

function statusText(status: ApplicationStatus) {
  const map: Record<ApplicationStatus, string> = {
    pending: 'Beklemede',
    approved: 'Onaylandı',
    rejected: 'Reddedildi'
  }
  return map[status]
}

function detailValue(fieldId: string) {
  if (!selectedApplication.value) return '—'
  const segments = fieldId.split('.')
  let value = segments.reduce<unknown>((acc, segment) => (acc as Record<string, unknown>)?.[segment], selectedApplication.value)
  if ((!value || value === '') && fieldId === 'name') {
    value = selectedApplication.value.store_name
  }
  return (value as string) ?? '—'
}

function formatNumber(value: number | undefined) {
  return new Intl.NumberFormat('tr-TR').format(value ?? 0)
}

function formatDate(date: string) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('tr-TR', { year: 'numeric', month: 'long', day: 'numeric' })
}

async function fetchApplications() {
  loading.value = true
  try {
    const { data } = await api.get<SellerApplication[]>('/admin/seller-applications')
    applications.value = data
    if (selectedApplication.value) {
      selectedApplication.value = data.find(app => app.id === selectedApplication.value?.id) || null
    }
  } catch (error) {
    console.error('Başvurular yüklenemedi:', error)
  } finally {
    loading.value = false
  }
}

function viewDetails(application: SellerApplication) {
  selectedApplication.value = application
}

function closeDetails() {
  selectedApplication.value = null
}

async function approve(id: number) {
  if (!confirm('Bu başvuruyu onaylamak istediğinizden emin misiniz?')) return
  try {
    await api.patch(`/admin/vendors/${id}/status`, { status: 'approved' })
    await fetchApplications()
  } catch (error) {
    console.error('Onaylama işlemi başarısız:', error)
    alert('Onay işlemi sırasında bir hata oluştu.')
  }
}

async function reject(id: number) {
  if (!confirm('Bu başvuruyu reddetmek istediğinizden emin misiniz?')) return
  try {
    await api.patch(`/admin/vendors/${id}/status`, { status: 'rejected' })
    await fetchApplications()
  } catch (error) {
    console.error('Reddetme işlemi başarısız:', error)
    alert('Red işlemi sırasında bir hata oluştu.')
  }
}

async function handleDecision(type: 'approve' | 'reject') {
  if (!selectedApplication.value) return
  if (type === 'approve') {
    await approve(selectedApplication.value.id)
  } else {
    await reject(selectedApplication.value.id)
  }
  closeDetails()
}

onMounted(fetchApplications)
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
