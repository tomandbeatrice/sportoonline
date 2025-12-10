<template>
  <section v-if="metrics" class="rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-cyan-50 p-6">
    <div class="mb-4 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="h-3 w-3 animate-pulse rounded-full bg-emerald-500"></div>
        <h3 class="text-lg font-semibold text-slate-900">Canlı Metrikler</h3>
        <span v-if="!isLoading" class="text-xs text-slate-500">(Son 30 saniye)</span>
      </div>
      <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
        CANLI
      </span>
    </div>
    <div class="grid gap-4 md:grid-cols-4">
      <div class="rounded-xl bg-white p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500">Sipariş (Canlı)</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ metrics.total_orders }}</p>
        <p class="mt-1 text-sm text-emerald-600">+{{ newOrders }} yeni</p>
      </div>
      <div class="rounded-xl bg-white p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500">Gelir (Canlı)</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ formatCurrency(metrics.today_revenue) }}</p>
        <p class="mt-1 text-sm text-slate-600">Ortalama: {{ formatCurrency(metrics.avg_order_value) }}</p>
      </div>
      <div class="rounded-xl bg-white p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500">Aktif Kampanya</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ metrics.active_campaigns }}</p>
        <p class="mt-1 text-sm text-slate-600">Devam eden</p>
      </div>
      <div class="rounded-xl bg-white p-4 shadow-sm">
        <p class="text-xs uppercase tracking-wider text-slate-500">Dönüşüm</p>
        <p class="mt-2 text-3xl font-bold text-slate-900">{{ metrics.conversion_rate }}%</p>
        <p class="mt-1 text-sm flex items-center gap-1" :class="metrics.conversion_rate > 2 ? 'text-emerald-600' : 'text-orange-600'">
          <TrendingUp v-if="metrics.conversion_rate > 2" class="w-4 h-4" />
          <TrendingDown v-else class="w-4 h-4" />
          {{ metrics.conversion_rate > 2 ? 'Hedefin üstünde' : 'Hedefin altında' }}
        </p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { TrendingUp, TrendingDown } from 'lucide-vue-next'

export interface LiveMetrics {
  total_orders: number
  today_revenue: number
  avg_order_value: number
  active_campaigns: number
  conversion_rate: number
}

const props = defineProps<{
  metrics: LiveMetrics | null
  isLoading?: boolean
  previousOrders?: number
}>()

const newOrders = computed(() => {
  if (!props.metrics || !props.previousOrders) return 0
  return Math.max(0, props.metrics.total_orders - props.previousOrders)
})

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { 
    style: 'currency', 
    currency: 'TRY', 
    maximumFractionDigits: 0 
  }).format(value ?? 0)
}
</script>
