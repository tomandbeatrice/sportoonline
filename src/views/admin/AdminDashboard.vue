<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header with Logout -->
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white shadow-sm">
      <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-4">
          <router-link to="/dashboard" class="text-2xl font-bold text-slate-900">
            SportoOnline
          </router-link>
          <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
            ADMIN
          </span>
        </div>
        
        <nav class="flex items-center gap-2">
          <router-link to="/dashboard" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100">
            Ana Sayfa
          </router-link>
          <router-link to="/admin/dashboard" class="rounded-lg bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700">
            Dashboard
          </router-link>
          <router-link to="/admin/reports" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100">
            Raporlar
          </router-link>
          <router-link to="/admin/settings" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100">
            Ayarlar
          </router-link>
          <router-link to="/admin/notifications" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100">
            Bildirimler
          </router-link>
          <router-link to="/admin/theme" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100">
            Tema
          </router-link>
          
          <!-- Notification Bell -->
          <NotificationDropdown
            :notifications="notifications"
            :unreadCount="unreadCount"
            :markAsRead="markAsRead"
            :markAllAsRead="markAllAsRead"
            :clearAll="clearAll"
          />
          
          <div class="ml-4 flex items-center gap-3 border-l border-slate-200 pl-4">
            <div class="text-right">
              <p class="text-sm font-semibold text-slate-900">{{ userName }}</p>
              <p class="text-xs text-slate-500">Administrator</p>
            </div>
            <button 
              @click="handleLogout"
              class="rounded-lg bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-100"
            >
              Çıkış
            </button>
          </div>
        </nav>
      </div>
    </header>

    <!-- Main Content -->
    <div class="px-4 py-10 md:px-8">
      <div v-if="isLoading" class="flex h-[60vh] flex-col items-center justify-center gap-3 text-slate-500">
        <span class="h-10 w-10 animate-spin rounded-full border-2 border-slate-200 border-t-slate-500" />
        <p>Panon yükleniyor...</p>
      </div>
      <div v-else-if="loadError" class="rounded-3xl border border-orange-100 bg-white px-6 py-4 text-center text-sm text-orange-700">
        {{ loadError }}
      </div>

      <div v-else class="space-y-10">
      <!-- Test Chart -->
      <Card class="border-4 border-purple-500">
        <CardHeader>
          <CardTitle>🧪 Test Grafik (Her Zaman Görünür)</CardTitle>
          <CardDescription>Bu basit test grafiği her zaman çalışmalı</CardDescription>
        </CardHeader>
        <CardContent>
          <LineChart :data="testChartData" :height="200" />
        </CardContent>
      </Card>

      <!-- Real-time Metrics Banner -->
      <section v-if="realtimeMetrics" class="rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-cyan-50 p-6">
        <div class="mb-4 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="h-3 w-3 animate-pulse rounded-full bg-emerald-500"></div>
            <h3 class="text-lg font-semibold text-slate-900">Canlı Metrikler</h3>
            <span v-if="!realtimeLoading" class="text-xs text-slate-500">(Son 30 saniye)</span>
          </div>
          <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
            CANLI
          </span>
        </div>
        <div class="grid gap-4 md:grid-cols-4">
          <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-slate-500">Sipariş (Canlı)</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ realtimeMetrics.total_orders }}</p>
            <p class="mt-1 text-sm text-emerald-600">+{{ realtimeMetrics.total_orders - (adminStats?.last_24h_orders || 0) }} yeni</p>
          </div>
          <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-slate-500">Gelir (Canlı)</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ formatCurrency(realtimeMetrics.today_revenue) }}</p>
            <p class="mt-1 text-sm text-slate-600">Ortalama: {{ formatCurrency(realtimeMetrics.avg_order_value) }}</p>
          </div>
          <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-slate-500">Aktif Kampanya</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ realtimeMetrics.active_campaigns }}</p>
            <p class="mt-1 text-sm text-slate-600">Devam eden</p>
          </div>
          <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-slate-500">Dönüşüm</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ realtimeMetrics.conversion_rate }}%</p>
            <p class="mt-1 text-sm" :class="realtimeMetrics.conversion_rate > 2 ? 'text-emerald-600' : 'text-orange-600'">
              {{ realtimeMetrics.conversion_rate > 2 ? '↗ Hedefin üstünde' : '↘ Hedefin altında' }}
            </p>
          </div>
        </div>
      </section>

      <section class="grid gap-6 lg:grid-cols-[1.4fr_0.8fr]">
        <Card class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-900 text-white">
          <CardHeader class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
              <CardTitle class="text-3xl">Merkez Operasyon Kokpiti</CardTitle>
              <CardDescription class="text-white/70">
                Hoş geldin {{ userName }}. Sistem durumu: <span :class="heroStatusBadge">{{ systemStatus }}</span>
              </CardDescription>
            </div>
            <div class="flex flex-wrap gap-3">
              <button @click="shareReport" class="rounded-2xl border border-white/30 px-4 py-2 text-sm font-semibold text-white/80 transition hover:border-white hover:text-white">
                Raporu Paylaş
              </button>
              <button @click="createCriticalAlert" class="rounded-2xl bg-white px-5 py-2 text-sm font-semibold text-slate-900 shadow-lg shadow-slate-900/40 transition hover:-translate-y-[2px]">
                Kritik Alert Oluştur
              </button>
            </div>
          </CardHeader>
          <CardContent class="grid gap-6 md:grid-cols-2">
            <div class="space-y-3">
              <p class="text-xs uppercase tracking-[0.4em] text-white/60">Son 24 Saat</p>
              <p class="text-5xl font-semibold">{{ last24Hours }}</p>
              <p class="text-sm text-white/70">Yüksek hacimli kampanya döneminde sipariş hacmi %14 artış gösterdi.</p>
            </div>
            <div class="space-y-4 rounded-3xl bg-white/10 p-4 backdrop-blur">
              <div class="flex items-center justify-between text-sm text-white/70">
                <span>Uptime</span>
                <span class="text-white">{{ uptimeOverview }}</span>
              </div>
              <div class="h-2 rounded-full bg-white/20">
                <div class="h-full rounded-full bg-emerald-300" :style="{ width: uptimePercent }" />
              </div>
              <div class="grid grid-cols-2 gap-3 text-xs text-white/70">
                <div>
                  <p class="uppercase tracking-[0.3em]">API</p>
                  <p class="text-white">{{ apiLatency }}</p>
                </div>
                <div>
                  <p class="uppercase tracking-[0.3em]">İşlem Kuyruğu</p>
                  <p class="text-white">{{ jobQueueDepth }}</p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>İstikrar Notları</CardTitle>
            <CardDescription>Gözlemlenen olaylar ve aksiyon önerileri</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <Alert v-for="alert in governanceAlerts" :key="alert.id" :title="alert.title" :variant="alert.variant">
              {{ alert.description }}
              <template v-if="alert.action" #action>
                <button @click="handleAlertAction(alert)" class="rounded-2xl border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                  {{ alert.action }}
                </button>
              </template>
            </Alert>
          </CardContent>
        </Card>
      </section>

      <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <Card v-for="stat in statGrid" :key="stat.id" class="relative overflow-hidden">
          <CardHeader class="space-y-1">
            <CardDescription class="text-xs uppercase tracking-[0.4em] text-slate-400">{{ stat.label }}</CardDescription>
            <CardTitle class="text-3xl">{{ stat.value }}</CardTitle>
          </CardHeader>
          <CardContent class="flex items-center justify-between text-sm text-slate-500">
            <span>{{ stat.hint }}</span>
            <span :class="stat.trend === 'up' ? 'text-emerald-600' : 'text-orange-600'">{{ stat.delta }}</span>
          </CardContent>
          <div class="pointer-events-none absolute -right-4 top-1/2 h-16 w-16 -translate-y-1/2 rounded-full" :class="stat.trend === 'up' ? 'bg-emerald-100' : 'bg-orange-100'" />
        </Card>
      </section>

      <!-- Charts Section -->
      <section class="grid gap-6 lg:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>📊 Gelir Trendi</CardTitle>
            <CardDescription>Son 7 günlük gelir ve sipariş grafiği</CardDescription>
          </CardHeader>
          <CardContent>
            <div v-if="!revenueChartData" class="flex h-[300px] items-center justify-center text-sm">
              <div class="text-center">
                <p class="text-slate-400">Grafik verisi yükleniyor...</p>
                <p class="mt-2 text-xs text-slate-300">Financial Report: {{ financialReport ? 'Var' : 'Yok' }}</p>
                <p class="text-xs text-slate-300">Transactions: {{ financialReport?.recent_transactions?.length || 0 }}</p>
              </div>
            </div>
            <LineChart v-else :data="revenueChartData" :height="300" />
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>💰 Satıcı Gelir Dağılımı</CardTitle>
            <CardDescription>Top 5 satıcının gelir paylaşımı</CardDescription>
          </CardHeader>
          <CardContent>
            <div v-if="!sellerRevenueChart" class="flex h-[300px] items-center justify-center text-sm">
              <div class="text-center">
                <p class="text-slate-400">Grafik verisi yükleniyor...</p>
                <p class="mt-2 text-xs text-slate-300">Sellers: {{ financialReport?.sales_by_seller?.length || 0 }}</p>
              </div>
            </div>
            <BarChart v-else :data="sellerRevenueChart" :height="300" />
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle class="inline-flex items-center gap-2"><BadgeIcon name="box" cls="w-5 h-5 text-blue-600" /> Sipariş Durumu</CardTitle>
            <CardDescription>Sipariş durum dağılımı</CardDescription>
          </CardHeader>
          <CardContent>
            <div v-if="!orderStatusChart" class="flex h-[300px] items-center justify-center text-sm">
              <div class="text-center">
                <p class="text-slate-400">Grafik verisi yükleniyor...</p>
                <p class="mt-2 text-xs text-slate-300">Admin Stats: {{ adminStats ? 'Var' : 'Yok' }}</p>
              </div>
            </div>
            <DoughnutChart v-else :data="orderStatusChart" :height="300" />
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>💵 Komisyon Analizi</CardTitle>
            <CardDescription>Platform geliri vs satıcı payı</CardDescription>
          </CardHeader>
          <CardContent>
            <div v-if="!commissionChart" class="flex h-[300px] items-center justify-center text-sm">
              <div class="text-center">
                <p class="text-slate-400">Grafik verisi yükleniyor...</p>
                <p class="mt-2 text-xs text-slate-300">Summary: {{ financialReport?.summary ? 'Var' : 'Yok' }}</p>
              </div>
            </div>
            <DoughnutChart v-else :data="commissionChart" :height="300" />
          </CardContent>
        </Card>
      </section>

      <section>
        <Card class="space-y-6">
          <CardHeader>
            <CardTitle>Gözlemlenebilirlik Panosu</CardTitle>
            <CardDescription>İş yüklerini, satıcı davranışını ve riskleri tek bakışta oku.</CardDescription>
          </CardHeader>
          <CardContent>
            <Tabs :defaultValue="defaultObservabilityTab" class="space-y-6">
              <TabsList class="flex flex-wrap gap-3">
                <TabsTrigger
                  v-for="panel in observabilityPanels"
                  :key="panel.id"
                  :value="panel.id"
                  class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em]"
                  active-class="border-transparent bg-slate-900 text-white shadow-xl"
                  inactive-class="border-slate-200 bg-white text-slate-500"
                >
                  {{ panel.label }}
                </TabsTrigger>
              </TabsList>

              <TabsContent v-for="panel in observabilityPanels" :key="panel.id" :value="panel.id" class="space-y-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                  <div>
                    <p class="text-sm font-semibold text-slate-900">{{ panel.summary }}</p>
                    <p class="text-sm text-slate-500">{{ panel.description }}</p>
                  </div>
                  <span class="rounded-2xl bg-slate-100 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">
                    {{ panel.window }}
                  </span>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                  <div v-for="metric in panel.metrics" :key="metric.label" class="rounded-2xl border border-slate-100 bg-white p-4">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ metric.label }}</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ metric.value }}</p>
                    <p class="text-sm text-slate-500">{{ metric.detail }}</p>
                  </div>
                </div>

                <div class="space-y-2">
                  <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Kırılım</p>
                  <div v-for="item in panel.breakdown" :key="item.label" class="space-y-1">
                    <div class="flex items-center justify-between text-sm text-slate-600">
                      <span>{{ item.label }}</span>
                      <span class="font-semibold text-slate-900">{{ item.value }}%</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100">
                      <div class="h-full rounded-full bg-slate-900" :style="{ width: `${item.value}%` }" />
                    </div>
                  </div>
                </div>
              </TabsContent>
            </Tabs>
          </CardContent>
        </Card>
      </section>

      <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <Card>
          <CardHeader>
            <CardTitle>Operasyon Görev Listesi</CardTitle>
            <CardDescription>Takibe alınan aksiyon maddeleri</CardDescription>
          </CardHeader>
          <CardContent>
            <Accordion defaultValue="task-1" class="space-y-4">
              <AccordionItem v-for="task in opsTasks" :key="task.id" :value="task.id">
                <div class="p-4">
                  <AccordionTrigger class="text-sm font-semibold text-slate-900">
                    {{ task.title }} · <span class="text-slate-400">{{ task.department }}</span>
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
            <CardTitle>Gerçek Zamanlı Aktivite</CardTitle>
            <CardDescription>Alert, deploy ve satıcı bildirimleri</CardDescription>
          </CardHeader>
          <CardContent class="space-y-5">
            <div v-for="item in activityFeed" :key="item.id" class="flex items-start gap-4">
              <span :class="['mt-1 h-3 w-3 rounded-full', item.accent]" />
              <div>
                <p class="text-sm font-semibold text-slate-900">{{ item.title }}</p>
                <p class="text-xs text-slate-400">{{ item.time }}</p>
                <p class="text-sm text-slate-500">{{ item.detail }}</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

      <section class="grid gap-6 lg:grid-cols-3">
        <Card class="lg:col-span-2">
          <CardHeader>
            <CardTitle>Entegrasyon Sağlığı</CardTitle>
            <CardDescription>Üçüncü parti servislerin son durumları</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <div v-for="integration in integrations" :key="integration.id" class="flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-4">
              <div>
                <p class="text-sm font-semibold text-slate-900">{{ integration.name }}</p>
                <p class="text-xs text-slate-500">{{ integration.description }}</p>
              </div>
              <span :class="integration.badge">
                {{ integration.status }}
              </span>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle>Hızlı Aksiyon</CardTitle>
            <CardDescription>Kritik makrolar</CardDescription>
          </CardHeader>
          <CardContent class="space-y-3">
            <button
              v-for="action in quickActions"
              :key="action.id"
              class="flex w-full items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 text-left text-sm font-semibold text-slate-700 transition hover:-translate-y-[1px] hover:border-slate-400"
            >
              <span>{{ action.label }}</span>
              <span class="text-xs font-normal text-slate-400">{{ action.meta }}</span>
            </button>
          </CardContent>
        </Card>
      </section>
      </div>
      <!-- End v-else -->
    </div>
    <!-- End Main Content -->
  </div>
  <!-- End Container -->
</template>

<script setup lang="ts">
import { ref, onMounted, computed, onUnmounted } from 'vue'
import { useToast } from 'vue-toastification'
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent
} from '@/components/ui/card'
import { useProductStore } from '../../stores/productStore'
import { useOrderStore } from '../../stores/orderStore'
import { storeToRefs } from 'pinia'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Alert } from '@/components/ui/alert'
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion'
import { useRealtimeDashboard } from '@/composables/useRealtimeDashboard'
import { performanceMonitor } from '@/services/performanceMonitor'
import LineChart from '@/components/charts/LineChart.vue'
import BarChart from '@/components/charts/BarChart.vue'
import DoughnutChart from '@/components/charts/DoughnutChart.vue'
import NotificationDropdown from '@/components/NotificationDropdown.vue'
import { useNotifications } from '@/composables/useNotifications'

const productStore = useProductStore()
const orderStore = useOrderStore()
const { products } = storeToRefs(productStore)
const { orders } = storeToRefs(orderStore)

const toast = useToast()

// Real-time notifications
const adminUserId = 1 // Mock admin user ID
const { notifications, unreadCount, markAsRead, markAllAsRead, clearAll } = useNotifications(adminUserId, 'admin')

const isLoading = ref(true)
const loadError = ref<string | null>(null)

const adminStats = ref<any>(null)
const financialReport = ref<any>(null)

// --- Actions ---
const handleAlertAction = (alert: any) => {
  toast.info(`Aksiyon alındı: ${alert.action}`)
}

const shareReport = () => {
  toast.success('Rapor panoya kopyalandı')
}

const createCriticalAlert = () => {
  toast.warning('Kritik alert oluşturma modalı açılıyor...')
}

const handleLogout = () => {
  // Clear auth data
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  localStorage.removeItem('role')
  
  // Show toast
  toast.success('Başarıyla çıkış yapıldı')
  
  // Redirect to login
  window.location.href = '/login'
}

// --- Mock Data & Logic ---
const userName = computed(() => {
  const user = localStorage.getItem('user')
  if (user) {
    try {
      const userData = JSON.parse(user)
      return userData.name || 'Admin'
    } catch (e) {
      return 'Admin'
    }
  }
  return 'Admin'
})

// Real-time dashboard integration
const { metrics: realtimeMetrics, isLoading: realtimeLoading, startPolling, stopPolling } = useRealtimeDashboard('admin')

const governanceAlerts = computed<GovernanceAlert[]>(() => {
  const summary = financialReport.value?.summary
  if (!summary) {
    return defaultGovernanceAlerts
  }

  const payoutGap = summary.total_revenue - summary.total_seller_payouts
  const feeRate = summary.total_platform_fees && summary.total_revenue
    ? Math.round((summary.total_platform_fees / summary.total_revenue) * 100)
    : 0

  return [
    {
      id: 'revenue-health',
      title: `Toplam gelir ${formatCurrency(summary.total_revenue)}`,
      description: `Platform payı ${formatCurrency(summary.total_platform_fees)} · ücret oranı %${feeRate}`,
      variant: feeRate > 18 ? 'orange' : 'emerald'
    },
    {
      id: 'payout-gap',
      title: 'Satıcı ödeme doğrulaması',
      description: `${formatCurrency(payoutGap)} tutarında bekleyen ödeme kontrolü.`,
      variant: payoutGap > 0 ? 'orange' : 'emerald',
      action: payoutGap > 0 ? 'Payout Kuyruğu' : undefined
    },
    {
      id: 'orders',
      title: 'Sipariş hacmi',
      description: `${formatNumber(summary.total_orders)} adet son 24 saatte işlendi.`,
      variant: 'default'
    }
  ]
})

const defaultGovernanceAlerts: GovernanceAlert[] = [
  {
    id: 'sla',
    title: '2 API endpointi 400ms üstünde',
    description: 'Ürün detay servisinde gecikme gözlendi. Cache 15 dk boyunca bypass edildi.',
    variant: 'orange',
    action: 'Logları Aç'
  },
  {
    id: 'fraud',
    title: 'Fraud duvarı %96 doğrulukta',
    description: 'Yeni makine öğrenmesi kuralı kart provizyon hatalarını %18 azalttı.',
    variant: 'emerald'
  },
  {
    id: 'ops',
    title: 'Satıcı onboarding uçtan uca 27 dk',
    description: 'İmza sürecini hızlandırmak için sözleşme şablonları güncellendi.',
    variant: 'default'
  }
]

const defaultObservabilityPanels: ObservabilityPanel[] = [
  {
    id: 'traffic',
    label: 'Trafik',
    summary: 'Pik saatlerinde isteklerin %82 si cache tarafından yanıtlanıyor.',
    description: 'Altyapı kapasitesi 3 kat ölçeklenebilir durumda.',
    window: 'Son 6 saat',
    metrics: [
      { label: 'Dakikalık istek', value: '148K', detail: '+12% vs dün' },
      { label: 'Cache oranı', value: '%82', detail: '+4pp WoW' },
      { label: 'Ortalama süre', value: '214ms', detail: '-36ms hedefe göre' },
      { label: 'Hata oranı', value: '%0.21', detail: '-0.03pp' }
    ],
    breakdown: [
      { label: 'Kuzey veri merkezi', value: 54 },
      { label: 'Doğu veri merkezi', value: 32 },
      { label: 'Yedek bölge', value: 14 }
    ]
  },
  {
    id: 'sellers',
    label: 'Satıcılar',
    summary: 'Aktif satıcıların %67 si önerilen fiyat motorunu kullanıyor.',
    description: 'Kritik KPI larda 9 satıcı için manuel destek açıldı.',
    window: 'Son 7 gün',
    metrics: [
      { label: 'Aktif satıcı', value: '4.820', detail: '+210 WoW' },
      { label: 'Onboarding süresi', value: '27 dk', detail: '-6 dk WoW' },
      { label: 'SLA uyumu', value: '%94', detail: '+2pp hedefe göre' },
      { label: 'Destek bileti', value: '184', detail: '-36 vs dün' }
    ],
    breakdown: [
      { label: 'Enterprise', value: 41 },
      { label: 'SMB', value: 45 },
      { label: 'Yeni aday', value: 14 }
    ]
  },
  {
    id: 'risk',
    label: 'Risk',
    summary: 'Fraud skor kartı 4 akışta yeniden eğitildi.',
    description: 'Kart hataları ve iade talepleri dinamik olarak eşleniyor.',
    window: 'Canlı',
    metrics: [
      { label: 'Anomali alarmı', value: '12', detail: '3 kritik, 9 bilgi amaçlı' },
      { label: 'İade eşiği', value: '%2.9', detail: '-0.4pp vs hedef' },
      { label: 'Ödeme kesinti sayısı', value: '0', detail: 'Son 48 saatte hata yok' },
      { label: 'Fraud yakalama', value: '%96', detail: '+3pp model güncellemesi' }
    ],
    breakdown: [
      { label: 'Ödeme akışı', value: 38 },
      { label: 'Sipariş akışı', value: 31 },
      { label: 'Satıcı akışı', value: 31 }
    ]
  }
]

const observabilityPanels = computed<ObservabilityPanel[]>(() => {
  const summary = financialReport.value?.summary
  const sellers = financialReport.value?.sales_by_seller ?? []
  const transactions = financialReport.value?.recent_transactions ?? []

  if (!summary) {
    return defaultObservabilityPanels
  }

  const totalRevenue = summary.total_revenue || 1
  const sellerBreakdown = sellers.slice(0, 3).map(breakdown => ({
    label: breakdown.seller_name || 'Satıcı',
    value: Math.min(100, Math.round((breakdown.total_revenue / totalRevenue) * 100))
  }))

  const statusCounts = transactions.reduce<Record<string, number>>((acc, tx) => {
    acc[tx.status] = (acc[tx.status] || 0) + 1
    return acc
  }, {})

  const pendingShare = statusCounts['pending'] || 0
  const shippedShare = statusCounts['shipped'] || 0
  const deliveredShare = statusCounts['delivered'] || 0
  const totalTx = transactions.length || 1

  return [
    {
      id: 'revenue',
      label: 'Gelir',
      summary: `Platform geliri ${formatCurrency(summary.total_revenue)}`,
      description: 'Net gelir, komisyon ve payout dengesi.',
      window: 'Son 24 saat',
      metrics: [
        { label: 'Platform payı', value: formatCurrency(summary.total_platform_fees), detail: 'Komisyon toplamı' },
        { label: 'Satıcı payout', value: formatCurrency(summary.total_seller_payouts), detail: 'Tamamlanan ödemeler' },
        { label: 'Sipariş', value: formatNumber(summary.total_orders), detail: 'İşlenen sipariş adedi' },
        { label: 'Ortalama sepet', value: formatCurrency(summary.total_orders ? summary.total_revenue / summary.total_orders : 0), detail: 'Gelir / sipariş' }
      ],
      breakdown: sellerBreakdown.length ? sellerBreakdown : defaultObservabilityPanels[0].breakdown
    },
    {
      id: 'sellers',
      label: 'Satıcılar',
      summary: `${sellers.length} satıcı gelir oluşturdu`,
      description: 'İlk üç satıcı toplam gelirin büyük kısmını oluşturuyor.',
      window: 'Son 24 saat',
      metrics: [
        { label: 'Toplam satıcı', value: formatNumber(sellers.length), detail: 'Gelir paylaşımına dahil' },
        { label: 'Ortalama payout', value: formatCurrency(sellers.length ? summary.total_seller_payouts / sellers.length : 0), detail: 'Satıcı başına' },
        { label: 'Sipariş ortalaması', value: formatNumber(sellers.length ? summary.total_orders / sellers.length : 0), detail: 'Satıcı başına sipariş' },
        { label: 'Komisyon oranı', value: `${Math.round((summary.total_platform_fees / totalRevenue) * 100)}%`, detail: 'Gelire göre' }
      ],
      breakdown: sellerBreakdown.length ? sellerBreakdown : defaultObservabilityPanels[1].breakdown
    },
    {
      id: 'risk',
      label: 'Risk',
      summary: `${pendingShare + shippedShare} sipariş takipte`,
      description: 'Sipariş akış statülerine göre anlık görünüm.',
      window: 'Canlı',
      metrics: [
        { label: 'Bekleyen', value: `${pendingShare}`, detail: 'Çıkış bekleyen sipariş' },
        { label: 'Kargoda', value: `${shippedShare}`, detail: 'Transitte olan sipariş' },
        { label: 'Teslim edildi', value: `${deliveredShare}`, detail: 'Tamamlanan sipariş' },
        { label: 'Toplam olay', value: `${totalTx}`, detail: 'İzlenen işlem' }
      ],
      breakdown: [
        { label: 'Bekleyen', value: Math.round((pendingShare / totalTx) * 100) },
        { label: 'Kargoda', value: Math.round((shippedShare / totalTx) * 100) },
        { label: 'Teslim', value: Math.max(0, 100 - Math.round((pendingShare / totalTx) * 100) - Math.round((shippedShare / totalTx) * 100)) }
      ]
    }
  ]
})

const defaultOpsTasks: OpsTask[] = [
  {
    id: 'task-1',
    title: 'Pazar yeri SLA protokolü revizyonu',
    department: 'Operasyon',
    due: 'Bugün 18:00',
    description: 'Yeni sezon için SLA takvimlerini güncelle ve satıcı paneline yayınla.'
  },
  {
    id: 'task-2',
    title: 'Fraud dashboard veri doğrulaması',
    department: 'Risk',
    due: 'Yarın 11:00',
    description: 'Yeni özellik mühimmat tablosunu Looker ile kıyasla.'
  },
  {
    id: 'task-3',
    title: 'Kategori bazlı karlılık raporu',
    department: 'Finans',
    due: 'Cuma 14:00',
    description: 'Günlük csv aktarımını BigQuery data martına bağla.'
  }
]

const opsTasks = computed<OpsTask[]>(() => {
  const sellers = financialReport.value?.sales_by_seller ?? []
  if (!sellers.length) {
    return defaultOpsTasks
  }

  return sellers.slice(0, 3).map((seller, index) => ({
    id: `task-${seller.seller_id ?? index}`,
    title: `${seller.seller_name || 'Satıcı'} payout denetimi`,
    department: 'Finans',
    due: `Sipariş: ${seller.order_count}`,
    description: `${formatCurrency(seller.total_revenue)} gelirin %${Math.round((seller.platform_fees / (seller.total_revenue || 1)) * 100)} komisyonu doğrulanacak.`
  }))
})

const defaultActivityFeed: ActivityItem[] = [
  {
    id: 'activity-1',
    title: 'Deploy v2025.11.17 tamamlandı',
    detail: 'Beta satıcı paneli için yeni KPI kartları aktif.',
    time: '8 dk önce',
    accent: 'bg-emerald-400'
  },
  {
    id: 'activity-2',
    title: 'Alert: Kuzey veri merkezi soğutma uyarısı',
    detail: 'Yedek kapasite %60 seviyesinde devrede.',
    time: '32 dk önce',
    accent: 'bg-orange-400'
  },
  {
    id: 'activity-3',
    title: 'Satıcı komisyon güncellemesi duyuruldu',
    detail: 'Tüm satıcılara bildirim gönderildi, takipte 1.240 okuma var.',
    time: '1 saat önce',
    accent: 'bg-indigo-400'
  }
]

const activityFeed = computed<ActivityItem[]>(() => {
  const transactions = financialReport.value?.recent_transactions ?? []
  if (!transactions.length) {
    return defaultActivityFeed
  }

  return transactions.slice(0, 5).map(transaction => ({
    id: `activity-${transaction.id}`,
    title: `${transaction.product_name} siparişi ${statusLabel(transaction.status)}`,
    detail: `${transaction.seller_name} · ${formatCurrency(transaction.total)} · Komisyon ${formatCurrency(transaction.platform_fee)}`,
    time: formatRelative(transaction.date),
    accent: transaction.status === 'paid' || transaction.status === 'delivered' ? 'bg-emerald-400' : transaction.status === 'cancelled' ? 'bg-orange-400' : 'bg-indigo-400'
  }))
})

const integrations = ref<IntegrationItem[]>([
  {
    id: 'integration-1',
    name: 'Ödeme Ağ Geçidi',
    description: '3DS doğrulama servisleri stabil.',
    status: 'Aktif',
    badge: 'rounded-2xl bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700'
  },
  {
    id: 'integration-2',
    name: 'Kargo Partnerleri',
    description: 'Ataşehir hub gecikmesi 24 dk.',
    status: 'Gecikmeli',
    badge: 'rounded-2xl bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700'
  },
  {
    id: 'integration-3',
    name: 'CRM Bildirimleri',
    description: 'Push gönderimlerinde %99 teslimat.',
    status: 'Aktif',
    badge: 'rounded-2xl bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700'
  }
])

const quickActions = ref<QuickAction[]>([
  { id: 'action-1', label: 'Satıcıyı manuel olarak onayla', meta: '2 bekleyen talep' },
  { id: 'action-2', label: 'Kampanya bütçe koridoru tanımla', meta: 'Yeni yönerge' },
  { id: 'action-3', label: 'Riskli siparişleri durdur', meta: 'Son 15 dk' }
])

const statGrid = computed<StatHighlight[]>(() => {
  const stats = adminStats.value
  const summary = financialReport.value?.summary
  if (!stats) {
    return []
  }

  return [
    {
      id: 'users',
      label: 'Toplam Kullanıcı',
      value: formatNumber(stats.total_users),
      hint: `${formatNumber(stats.total_sellers)} satıcı + ${formatNumber(stats.total_buyers)} alıcı`,
      delta: '+2%',
      trend: 'up'
    },
    {
      id: 'orders',
      label: 'Toplam Sipariş',
      value: formatNumber(stats.total_orders),
      hint: `${formatNumber(stats.last_24h_orders)} son 24 saatte`,
      delta: summary ? `+${Math.min(25, Math.round((stats.last_24h_orders / Math.max(stats.total_orders, 1)) * 100))}%` : '+0%',
      trend: 'up'
    },
    {
      id: 'products',
      label: 'Aktif Ürün',
      value: formatNumber(stats.total_products),
      hint: `${formatNumber(stats.category_count)} kategori`,
      delta: '+1%',
      trend: 'up'
    },
    {
      id: 'sellers',
      label: 'Aktif Satıcı',
      value: formatNumber(stats.seller_count),
      hint: `${formatNumber(financialReport.value?.sales_by_seller.length ?? 0)} satıcı bugün işlem yaptı`,
      delta: financialReport.value?.sales_by_seller.length ? '+4%' : '+0%',
      trend: financialReport.value?.sales_by_seller.length ? 'up' : 'down'
    }
  ]
})

const systemStatus = computed(() => (loadError.value ? 'Hata' : adminStats.value?.status || 'Stabil'))

const heroStatusBadge = computed(() => {
  if (systemStatus.value === 'Stabil') return 'rounded-full bg-emerald-400/30 px-3 py-1 text-xs font-semibold text-emerald-50'
  if (systemStatus.value === 'Hata') return 'rounded-full bg-orange-400/30 px-3 py-1 text-xs font-semibold text-orange-50'
  return 'rounded-full bg-slate-400/30 px-3 py-1 text-xs font-semibold text-slate-50'
})

const last24Hours = computed(() => formatNumber(adminStats.value?.last_24h_orders || 0))
const uptimeOverview = computed(() => adminStats.value?.uptime || '0%')
const uptimePercent = computed(() => adminStats.value?.uptime || '0%')
const apiLatency = computed(() => {
  const totalOrders = financialReport.value?.summary.total_orders ?? 0
  if (!totalOrders) return '—'
  const latency = Math.max(140, Math.min(480, Math.round(120000 / (totalOrders + 50))))
  return `${latency}ms`
})
const jobQueueDepth = computed(() => {
  const sellers = financialReport.value?.sales_by_seller.length ?? 0
  const orders = financialReport.value?.summary.total_orders ?? 0
  if (!sellers || !orders) {
    return '—'
  }
  const estimated = Math.max(4, Math.round(orders / sellers))
  return `${estimated} işlem`
})

const defaultObservabilityTab = computed(() => observabilityPanels.value[0]?.id ?? 'revenue')

// Test Chart Data (Always visible)
const testChartData = {
  labels: ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'],
  datasets: [
    {
      label: 'Test Veri',
      data: [12, 19, 3, 5, 2, 3, 15],
      borderColor: 'rgb(168, 85, 247)',
      backgroundColor: 'rgba(168, 85, 247, 0.1)',
      tension: 0.4,
      fill: true
    }
  ]
}

// Chart Data
const revenueChartData = computed(() => {
  if (!financialReport.value?.recent_transactions.length) return null
  
  // Last 7 days revenue
  const last7Days = Array.from({ length: 7 }, (_, i) => {
    const date = new Date()
    date.setDate(date.getDate() - (6 - i))
    return date.toISOString().split('T')[0]
  })
  
  const revenueByDay = last7Days.map(day => {
    const dayTransactions = financialReport.value?.recent_transactions.filter(
      t => t.date?.startsWith(day)
    ) || []
    return dayTransactions.reduce((sum, t) => sum + (t.total || 0), 0)
  })
  
  const ordersByDay = last7Days.map(day => {
    return financialReport.value?.recent_transactions.filter(
      t => t.date?.startsWith(day)
    ).length || 0
  })
  
  return {
    labels: last7Days.map(d => new Date(d).toLocaleDateString('tr-TR', { weekday: 'short', day: 'numeric' })),
    datasets: [
      {
        label: 'Gelir (₺)',
        data: revenueByDay,
        borderColor: 'rgb(16, 185, 129)',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.4,
        fill: true
      },
      {
        label: 'Sipariş Sayısı',
        data: ordersByDay,
        borderColor: 'rgb(99, 102, 241)',
        backgroundColor: 'rgba(99, 102, 241, 0.1)',
        tension: 0.4,
        fill: true,
        yAxisID: 'y1'
      }
    ]
  }
})

const sellerRevenueChart = computed(() => {
  const sellers = financialReport.value?.sales_by_seller || []
  if (!sellers.length) return null
  
  const top5 = sellers.slice(0, 5)
  
  return {
    labels: top5.map(s => s.seller_name || 'Bilinmeyen'),
    datasets: [
      {
        label: 'Toplam Gelir (₺)',
        data: top5.map(s => s.total_revenue || 0),
        backgroundColor: [
          'rgba(16, 185, 129, 0.8)',
          'rgba(99, 102, 241, 0.8)',
          'rgba(251, 146, 60, 0.8)',
          'rgba(244, 63, 94, 0.8)',
          'rgba(168, 85, 247, 0.8)'
        ],
        borderColor: [
          'rgb(16, 185, 129)',
          'rgb(99, 102, 241)',
          'rgb(251, 146, 60)',
          'rgb(244, 63, 94)',
          'rgb(168, 85, 247)'
        ],
        borderWidth: 2
      }
    ]
  }
})

const orderStatusChart = computed(() => {
  const stats = adminStats.value
  if (!stats) return null
  
  return {
    labels: ['Beklemede', 'Kargoda', 'Teslim Edildi'],
    datasets: [
      {
        data: [
          stats.pending_orders || 0,
          stats.shipped_orders || 0,
          stats.delivered_orders || 0
        ],
        backgroundColor: [
          'rgba(251, 146, 60, 0.8)',
          'rgba(99, 102, 241, 0.8)',
          'rgba(16, 185, 129, 0.8)'
        ],
        borderColor: [
          'rgb(251, 146, 60)',
          'rgb(99, 102, 241)',
          'rgb(16, 185, 129)'
        ],
        borderWidth: 2
      }
    ]
  }
})

const commissionChart = computed(() => {
  const summary = financialReport.value?.summary
  if (!summary) return null
  
  return {
    labels: ['Platform Komisyonu', 'Satıcı Payı'],
    datasets: [
      {
        data: [
          summary.total_platform_fees || 0,
          summary.total_seller_payouts || 0
        ],
        backgroundColor: [
          'rgba(99, 102, 241, 0.8)',
          'rgba(16, 185, 129, 0.8)'
        ],
        borderColor: [
          'rgb(99, 102, 241)',
          'rgb(16, 185, 129)'
        ],
        borderWidth: 2
      }
    ]
  }
})

function formatNumber(value: number | null | undefined) {
  const safeValue = typeof value === 'number' ? value : 0
  return new Intl.NumberFormat('tr-TR').format(safeValue)
}

function formatCurrency(value: number | null | undefined) {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 0 }).format(value ?? 0)
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

function statusLabel(status: string) {
  switch (status) {
    case 'pending':
      return 'Beklemede'
    case 'processing':
      return 'İşleniyor'
    case 'shipped':
      return 'Kargoda'
    case 'delivered':
      return 'Teslim edildi'
    case 'paid':
      return 'Ödeme tamamlandı'
    case 'cancelled':
      return 'İptal edildi'
    default:
      return status
  }
}

async function loadAdminDashboard() {
  isLoading.value = true
  loadError.value = null

  try {
    console.log('🚀 Starting loadAdminDashboard from REAL API...')
    
    // Import API service dynamically
    const { adminApi } = await import('@/services/api/adminApi')
    
    // Fetch real data from Laravel backend
    const [stats, financial] = await Promise.all([
      adminApi.getStats(),
      adminApi.getFinancialReport()
    ])
    
    console.log('✅ Admin Stats:', stats)
    console.log('✅ Financial Report:', financial)
    console.log('📊 Transactions count:', financial?.recent_transactions?.length)
    console.log('💰 Sellers count:', financial?.sales_by_seller?.length)
    
    adminStats.value = stats
    financialReport.value = financial
    
    // Log chart data after assignment
    setTimeout(() => {
      console.log('📈 Revenue Chart Data:', revenueChartData.value)
      console.log('📊 Seller Revenue Chart:', sellerRevenueChart.value)
      console.log('📦 Order Status Chart:', orderStatusChart.value)
      console.log('💵 Commission Chart:', commissionChart.value)
    }, 100)

  } catch (error: any) {
    console.error('❌ Admin dashboard verileri yüklenemedi:', error)
    
    // Fallback to mock data if API fails
    console.warn('⚠️ Falling back to mock data...')
    const totalRevenue = orders.value.reduce((sum, order) => sum + order.total, 0)
    const totalOrders = orders.value.length
    const totalProducts = products.value.length
    
    adminStats.value = {
      total_users: 150,
      total_sellers: 12,
      total_buyers: 138,
      total_products: totalProducts,
      total_orders: totalOrders,
      total_revenue: totalRevenue,
      pending_orders: 5,
      shipped_orders: 8,
      delivered_orders: 12,
      last_24h_orders: 3,
      last_24h_revenue: 5400,
      active_campaigns: 0,
      conversion_rate: 2.4,
      avg_order_value: totalOrders > 0 ? totalRevenue / totalOrders : 0
    }

    financialReport.value = {
      summary: {
        total_revenue: totalRevenue,
        total_platform_fees: totalRevenue * 0.15,
        total_seller_payouts: totalRevenue * 0.85,
        total_orders: totalOrders
      },
      sales_by_seller: [],
      recent_transactions: []
    }
    
    loadError.value = error.message || 'Veriler yüklenemedi. Mock data gösteriliyor.'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  console.log('📊 AdminDashboard mounted')
  console.log('🔑 Token from localStorage:', localStorage.getItem('token'))
  console.log('👤 Role from localStorage:', localStorage.getItem('role'))
  loadAdminDashboard()
  startPolling() // Start real-time metrics polling
  
  // Test notification after 3 seconds
  setTimeout(() => {
    toast.info('Bildirim sistemi aktif! Test bildirimi gönderildi.', { icon: '🔔' })
  }, 3000)
})

onUnmounted(() => {
  console.log('👋 AdminDashboard unmounted')
  stopPolling() // Stop polling when component unmounts
})
</script>