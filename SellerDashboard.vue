<template>
  <div class="space-y-10 bg-slate-50 px-4 py-10 md:px-8">
    <div v-if="isLoading" class="flex h-[60vh] flex-col items-center justify-center gap-3 text-slate-500">
      <span class="size-10 animate-spin rounded-full border-2 border-slate-200 border-t-slate-500" />
      <p>Panel yükleniyor...</p>
    </div>
    <div v-else-if="loadError" class="rounded-3xl border border-orange-100 bg-white px-6 py-4 text-center text-sm text-orange-700">
      {{ loadError }}
    </div>
    <template v-else>
      <section class="grid gap-6 lg:grid-cols-[1.4fr_0.8fr]">
      <Card class="bg-gradient-to-r from-violet-900 via-indigo-800 to-indigo-600 text-white">
        <CardHeader class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <CardTitle class="text-3xl font-semibold">Satıcı Kokpiti</CardTitle>
            <CardDescription class="text-white/80">Son 24 saatte vitrinde en çok etkileşim alan ürünlerin için aksiyon al.</CardDescription>
          </div>
          <div class="flex flex-wrap gap-3">
            <button
              class="rounded-2xl border border-white/30 px-4 py-2 text-sm font-medium text-white/90 transition hover:border-white hover:text-white"
            >
              Akış Otomasyonunu Kur
            </button>
            <button
              class="rounded-2xl bg-white px-5 py-2 text-sm font-semibold text-indigo-800 shadow-lg shadow-indigo-900/30 transition hover:-translate-y-[2px]"
              @click="createCampaign"
            >
              Yeni Kampanya Oluştur
            </button>
          </div>
        </CardHeader>
        <CardContent class="grid gap-6 md:grid-cols-2">
          <div class="space-y-3">
            <p class="text-sm uppercase tracking-[0.3em] text-white/70">Canlı Kampanyalar</p>
            <p class="text-4xl font-semibold">{{ liveCampaignCount }}</p>
            <p class="text-sm text-white/70">Performans limiti yaklaşan 2 kampanya için bütçe güncellemesi önerildi.</p>
          </div>
          <div class="rounded-2xl bg-white/10 p-4 backdrop-blur">
            <div class="flex items-center justify-between text-sm text-white/70">
              <span>Satış Kanalları</span>
              <span class="text-xs">Son 7 gün</span>
            </div>
            <div class="mt-4 space-y-4 text-sm">
              <div v-for="channel in channelSplit" :key="channel.label" class="space-y-1">
                <div class="flex items-center justify-between">
                  <span>{{ channel.label }}</span>
                  <span class="font-semibold text-white">{{ channel.value }}%</span>
                </div>
                <div class="h-2 rounded-full bg-white/10">
                  <div class="h-full rounded-full bg-emerald-300" :style="{ width: `${channel.value}%` }" />
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Teslimat Özeti</CardTitle>
          <CardDescription>Operasyon SLA'larının anlık görüntüsü</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <Alert
            v-for="alert in fulfillmentAlerts"
            :key="alert.id"
            :title="alert.title"
            :variant="alert.variant"
          >
            {{ alert.description }}
            <template v-if="alert.actionLabel" #action>
              <button class="rounded-2xl border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-600">
                {{ alert.actionLabel }}
              </button>
            </template>
          </Alert>
        </CardContent>
      </Card>
    </section>

      <section>
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <Card v-for="kpi in kpiCards" :key="kpi.id" class="relative overflow-hidden">
          <CardHeader class="space-y-1">
            <CardDescription class="uppercase tracking-[0.4em] text-xs text-slate-400">
              {{ kpi.label }}
            </CardDescription>
            <CardTitle class="text-3xl">{{ kpi.value }}</CardTitle>
          </CardHeader>
          <CardContent class="flex items-center justify-between text-sm text-slate-500">
            <span>{{ kpi.hint }}</span>
            <span :class="kpi.trend === 'up' ? 'text-emerald-600' : 'text-orange-600'">{{ kpi.delta }}</span>
          </CardContent>
          <div
            class="pointer-events-none absolute -right-4 top-1/2 size-16 -translate-y-1/2 rounded-full"
            :class="kpi.trend === 'up' ? 'bg-emerald-100' : 'bg-orange-100'"
          />
        </Card>
      </div>
    </section>

      <section class="grid gap-6 lg:grid-cols-[1.4fr_0.8fr]">
      <Card class="space-y-6">
        <CardHeader class="space-y-2">
          <CardTitle>Performans Panosu</CardTitle>
          <CardDescription>Satış, iade ve stok hikayesini tek yerde takip et.</CardDescription>
        </CardHeader>
        <CardContent>
          <Tabs defaultValue="sales" class="space-y-6">
            <TabsList class="flex flex-wrap gap-3">
              <TabsTrigger
                v-for="panel in performancePanels"
                :key="panel.id"
                :value="panel.id"
                class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em]"
                active-class="border-transparent bg-slate-900 text-white shadow-xl"
                inactive-class="border-slate-200 bg-white text-slate-500"
              >
                {{ panel.label }}
              </TabsTrigger>
            </TabsList>

            <TabsContent v-for="panel in performancePanels" :key="panel.id" :value="panel.id" class="space-y-6">
              <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                  <p class="text-sm font-semibold text-slate-900">{{ panel.title }}</p>
                  <p class="text-sm text-slate-500">{{ panel.description }}</p>
                </div>
                <span class="rounded-2xl bg-slate-100 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">
                  {{ panel.window }}
                </span>
              </div>

              <div class="grid gap-4 md:grid-cols-2">
                <div
                  v-for="metric in panel.metrics"
                  :key="metric.label"
                  class="rounded-2xl border border-slate-100 bg-white p-4"
                >
                  <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ metric.label }}</p>
                  <p class="mt-2 text-2xl font-semibold text-slate-900">{{ metric.value }}</p>
                  <p class="text-sm text-slate-500">{{ metric.detail }}</p>
                </div>
              </div>

              <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Kırılım</p>
                <div v-for="breakdown in panel.breakdown" :key="breakdown.label" class="space-y-1">
                  <div class="flex items-center justify-between text-sm text-slate-600">
                    <span>{{ breakdown.label }}</span>
                    <span class="font-semibold text-slate-900">{{ breakdown.value }}%</span>
                  </div>
                  <div class="h-2 rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-slate-900" :style="{ width: `${breakdown.value}%` }" />
                  </div>
                </div>
              </div>
            </TabsContent>
          </Tabs>
        </CardContent>
      </Card>

      <Card class="space-y-4">
        <CardHeader>
          <CardTitle>Aktif Kampanyalar</CardTitle>
          <CardDescription>Bütçe ve performans sinyalleri</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <div
            v-for="campaign in campaigns"
            :key="campaign.id"
            class="rounded-2xl border border-slate-100 p-4"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-semibold text-slate-900">{{ campaign.name }}</p>
                <p class="text-xs text-slate-500">{{ campaign.status }} · Bütçe {{ campaign.budget }}</p>
              </div>
              <span class="rounded-2xl bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                {{ campaign.uplift }} uplift
              </span>
            </div>
            <div class="mt-4 space-y-2">
              <div class="flex items-center justify-between text-xs text-slate-500">
                <span>İlerleme</span>
                <span>{{ campaign.progress }}%</span>
              </div>
              <div class="h-2 rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-emerald-500" :style="{ width: `${campaign.progress}%` }" />
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </section>

      <section class="grid gap-6 lg:grid-cols-2">
      <Card>
        <CardHeader>
          <CardTitle>Operasyon Görevleri</CardTitle>
          <CardDescription>Playbook maddelerini sırayla tamamla.</CardDescription>
        </CardHeader>
        <CardContent>
          <Accordion defaultValue="task-1" class="space-y-4">
            <AccordionItem v-for="task in sellerTasks" :key="task.id" :value="task.id">
              <div class="p-4">
                <AccordionTrigger class="text-sm font-semibold text-slate-900">
                  {{ task.title }} · <span class="text-slate-400">{{ task.owner }}</span>
                </AccordionTrigger>
                <AccordionContent class="text-sm text-slate-500">
                  <p>{{ task.description }}</p>
                  <p class="mt-3 text-xs uppercase tracking-[0.3em] text-slate-400">Teslim: {{ task.due }}</p>
                </AccordionContent>
              </div>
            </AccordionItem>
          </Accordion>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Canlı Aktivite</CardTitle>
          <CardDescription>Kritik olay akışı</CardDescription>
        </CardHeader>
        <CardContent class="space-y-5">
          <div v-for="activity in activityFeed" :key="activity.id" class="flex items-start gap-4">
            <span :class="['mt-1 size-3 rounded-full', activity.accent]" />
            <div>
              <p class="text-sm font-semibold text-slate-900">{{ activity.title }}</p>
              <p class="text-xs text-slate-400">{{ activity.time }}</p>
              <p class="text-sm text-slate-500">{{ activity.detail }}</p>
            </div>
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
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion'
import { Alert } from '@/components/ui/alert'

type Trend = 'up' | 'down'

interface KpiCard {
  id: string
  label: string
  value: string
  delta: string
  trend: Trend
  hint: string
}

interface PerformanceMetric {
  label: string
  value: string
  detail: string
}

interface PerformanceBreakdown {
  label: string
  value: number
}

interface PerformancePanel {
  id: string
  label: string
  title: string
  description: string
  window: string
  metrics: PerformanceMetric[]
  breakdown: PerformanceBreakdown[]
}

interface FulfillmentAlert {
  id: string
  title: string
  description: string
  variant: 'default' | 'emerald' | 'orange'
  actionLabel?: string
}

interface Campaign {
  id: number | string
  name: string
  status: string
  budget: string
  uplift: string
  progress: number
}

interface SellerTask {
  id: string
  title: string
  owner: string
  due: string
  description: string
}

interface ActivityItem {
  id: string
  title: string
  time: string
  detail: string
  accent: string
}

interface SellerStatsResponse {
  total_products: number
  total_orders: number
  total_revenue: number
  avg_rating: number | null
}

interface SellerOrderItem {
  id: number
  quantity: number
  price: number
  product?: { id: number; name: string }
}

interface SellerOrder {
  id: number
  status?: string
  created_at?: string
  user?: { id: number; name: string }
  items: SellerOrderItem[]
}

interface FinancialSummary {
  total_revenue: number
  total_platform_fees: number
  total_seller_payout: number
  pending_payout: number
  confirmed_payout: number
}

interface FinancialTransaction {
  id: number
  order_id: number
  product_name: string
  quantity: number
  total: number
  order_status: string
  date: string
}

interface FinancialReport {
  summary: FinancialSummary
  transactions: FinancialTransaction[]
}

const performancePanels: PerformancePanel[] = [
  {
    id: 'sales',
    label: 'Satış',
    title: 'Satış Hunisi',
    description: 'Reklam tıklamasından teslimata kadar dönüşümler',
    window: 'Son 7 gün',
    metrics: [
      { label: 'Ziyaret', value: '78.4K', detail: '+12% WoW' },
      { label: 'Sepete Ekleme', value: '6.2K', detail: '+9% WoW' },
      { label: 'Ödeme Başlama', value: '1.9K', detail: '+5% WoW' },
      { label: 'Tamamlanan Sipariş', value: '1.4K', detail: '+18% WoW' }
    ],
    breakdown: [
      { label: 'Mobil', value: 64 },
      { label: 'Desktop', value: 28 },
      { label: 'Pazar yeri', value: 8 }
    ]
  },
  {
    id: 'returns',
    label: 'İade',
    title: 'İade Riski',
    description: 'Ürün ve lojistik kaynaklı iade sinyalleri',
    window: 'Son 30 gün',
    metrics: [
      { label: 'İade Oranı', value: '2.8%', detail: '-0.6pp vs hedef' },
      { label: 'Hasarlı Ürün', value: '0.4%', detail: '-0.1pp WoW' },
      { label: 'Geç Teslimat', value: '36 sipariş', detail: '-14 sipariş WoW' },
      { label: 'SLA Uyarısı', value: '3 ürün', detail: 'Kritik stokta' }
    ],
    breakdown: [
      { label: 'Tekstil', value: 35 },
      { label: 'Ayakkabı', value: 27 },
      { label: 'Aksesuar', value: 16 },
      { label: 'Diğer', value: 22 }
    ]
  },
  {
    id: 'inventory',
    label: 'Stok',
    title: 'Stok Sağlığı',
    description: 'Talep ve stok dengesi',
    window: 'Canlı',
    metrics: [
      { label: 'Günlük Devir', value: '4.2', detail: '+0.6 WoW' },
      { label: 'Stokta Gün', value: '21 gün', detail: '-3 gün hedefe göre' },
      { label: 'Riskli SKU', value: '12 ürün', detail: '7 kritik stok' },
      { label: 'Yeni SKU', value: '34 ürün', detail: 'Bu hafta yüklendi' }
    ],
    breakdown: [
      { label: 'Hızlı satan', value: 42 },
      { label: 'Stabil', value: 33 },
      { label: 'Yavaşlayan', value: 25 }
    ]
  }
]

const isLoading = ref(true)
const loadError = ref<string | null>(null)
const sellerStats = ref<SellerStatsResponse | null>(null)
const sellerOrders = ref<SellerOrder[]>([])
const financialReport = ref<FinancialReport | null>(null)

const kpiCards = computed<KpiCard[]>(() => {
  const stats = sellerStats.value
  const summary = financialReport.value?.summary
  const orders = stats?.total_orders ?? 0
  const totalRevenue = summary?.total_revenue ?? 0
  const avgOrderValue = orders > 0 ? totalRevenue / orders : 0

  return [
    {
      id: 'revenue',
      label: 'Toplam Gelir',
      value: formatCurrency(totalRevenue),
      delta: '',
      trend: 'up',
      hint: `${formatNumber(orders)} sipariş`
    },
    {
      id: 'orders',
      label: 'Toplam Sipariş',
      value: formatNumber(orders),
      delta: '',
      trend: 'up',
      hint: `${formatNumber(stats?.total_products ?? 0)} ürün`
    },
    {
      id: 'aov',
      label: 'Sepet Ortalaması',
      value: formatCurrency(avgOrderValue),
      delta: '',
      trend: 'up',
      hint: 'Gelir / sipariş'
    },
    {
      id: 'rating',
      label: 'Ortalama Puan',
      value: stats?.avg_rating ? stats.avg_rating.toFixed(2) : '—',
      delta: '',
      trend: 'up',
      hint: 'Müşteri değerlendirmesi'
    }
  ]
})

const channelSplit = computed(() => {
  const total = sellerOrders.value.length
  if (!total) {
    return [
      { label: 'Tamamlanan', value: 60 },
      { label: 'Hazırlanıyor', value: 25 },
      { label: 'Riskli', value: 15 }
    ]
  }

  const delivered = sellerOrders.value.filter(order => order.status === 'delivered').length
  const inProgress = sellerOrders.value.filter(order => ['processing', 'pending', 'shipped'].includes(order.status ?? '')).length
  const risk = Math.max(0, total - delivered - inProgress)

  return [
    { label: 'Tamamlanan', value: Math.round((delivered / total) * 100) },
    { label: 'Hazırlanıyor', value: Math.round((inProgress / total) * 100) },
    { label: 'Güncelleniyor', value: Math.max(0, 100 - Math.round((delivered / total) * 100) - Math.round((inProgress / total) * 100)) }
  ]
})

const fulfillmentAlerts = computed<FulfillmentAlert[]>(() => {
  const pending = sellerOrders.value.filter(order => order.status !== 'delivered').length
  const delayed = sellerOrders.value.filter(order => {
    if (!order.created_at) return false
    const created = new Date(order.created_at)
    const hours = (Date.now() - created.getTime()) / (1000 * 60 * 60)
    return order.status !== 'delivered' && hours > 24
  }).length
  const confirmed = sellerOrders.value.length - pending

  return [
    {
      id: 'shipping',
      title: `${pending} sipariş gönderim bekliyor`,
      description: delayed
        ? `${delayed} sipariş 24 saatten uzun süredir açık.`
        : 'Tüm siparişler SLA içinde ilerliyor.',
      variant: delayed ? 'orange' : 'default',
      actionLabel: delayed ? 'Riskleri Gör' : undefined
    },
    {
      id: 'same-day',
      title: `${confirmed} sipariş tamamlandı`,
      description: 'Teslim edilen siparişler otomatik olarak finansal rapora yansıdı.',
      variant: 'emerald'
    }
  ]
})

const campaigns = computed<Campaign[]>(() => {
  const transactions = financialReport.value?.transactions ?? []
  const totalRevenue = financialReport.value?.summary.total_revenue ?? 1

  return transactions.slice(0, 3).map(tx => ({
    id: tx.id,
    name: tx.product_name || `Sipariş #${tx.order_id}`,
    status: statusLabel(tx.order_status),
    budget: formatCurrency(tx.total),
    uplift: `${tx.quantity} adet`,
    progress: Math.min(100, Math.round((tx.total / totalRevenue) * 100) || 0)
  }))
})

const sellerTasks = computed<SellerTask[]>(() => {
  return sellerOrders.value
    .filter(order => order.status !== 'delivered')
    .slice(0, 3)
    .map(order => ({
      id: `task-${order.id}`,
      title: `Sipariş #${order.id} hazırlanıyor`,
      owner: order.user?.name ?? 'Operasyon',
      due: order.created_at ? new Intl.DateTimeFormat('tr-TR', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }).format(new Date(order.created_at)) : '—',
      description: `${order.items.length} ürün · Durum: ${statusLabel(order.status)}`
    }))
})

const activityFeed = computed<ActivityItem[]>(() => {
  return sellerOrders.value.slice(0, 3).map(order => ({
    id: `activity-${order.id}`,
    title: `Sipariş #${order.id} ${statusLabel(order.status)}`,
    time: formatRelative(order.created_at),
    detail: `${order.user?.name ?? 'Müşteri'} · ${order.items.length} ürün`,
    accent: statusAccent(order.status)
  }))
})

const liveCampaignCount = computed(() => campaigns.value.length || sellerOrders.value.length)
const pendingOrdersCount = computed(() => sellerOrders.value.filter(order => order.status !== 'delivered').length)

function createCampaign() {
  // Kampanya oluşturma işlemi
}

async function loadSellerDashboard() {
  isLoading.value = true
  loadError.value = null

  try {
    const [statsRes, ordersRes, financialRes] = await Promise.all([
      api.get<SellerStatsResponse>('/seller/stats'),
      api.get<SellerOrder[]>('/seller/orders'),
      api.get<FinancialReport>('/seller/financial-report')
    ])

    sellerStats.value = statsRes.data
    sellerOrders.value = ordersRes.data
    financialReport.value = financialRes.data
  } catch (error) {
    console.error('Satıcı dashboard verileri alınamadı:', error)
    loadError.value = 'Satıcı verileri yüklenemedi. Lütfen oturumunuzu doğrulayıp tekrar deneyin.'
  } finally {
    isLoading.value = false
  }
}

function formatCurrency(value: number) {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 0 }).format(value ?? 0)
}

function formatNumber(value: number | null | undefined) {
  return new Intl.NumberFormat('tr-TR').format(value ?? 0)
}

function formatRelative(dateStr?: string) {
  if (!dateStr) return '—'
  const date = new Date(dateStr)
  const diff = Date.now() - date.getTime()
  const minutes = Math.floor(diff / (1000 * 60))
  if (minutes < 60) return `${minutes} dk önce`
  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `${hours} sa önce`
  const days = Math.floor(hours / 24)
  return `${days} g önce`
}

function statusLabel(status?: string) {
  switch (status) {
    case 'delivered':
      return 'Teslim edildi'
    case 'processing':
      return 'Hazırlanıyor'
    case 'pending':
      return 'Bekliyor'
    case 'shipped':
      return 'Kargoda'
    case 'cancelled':
      return 'İptal edildi'
    default:
      return status ?? 'Bilinmiyor'
  }
}

function statusAccent(status?: string) {
  switch (status) {
    case 'delivered':
      return 'bg-emerald-400'
    case 'cancelled':
      return 'bg-orange-400'
    default:
      return 'bg-indigo-400'
  }
}

onMounted(loadSellerDashboard)
</script>