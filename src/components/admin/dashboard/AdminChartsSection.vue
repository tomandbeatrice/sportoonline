<template>
  <section class="grid gap-6 lg:grid-cols-2">
    <!-- Gelir Trendi -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-900">📊 Gelir Trendi</h3>
        <p class="text-sm text-slate-500">Son 7 günlük gelir ve sipariş grafiği</p>
      </div>
      <div class="p-6">
        <div v-if="!revenueData" class="flex h-[280px] flex-col items-center justify-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
          <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center mb-3">
            <span class="animate-pulse text-xl">📊</span>
          </div>
          <p class="font-medium text-slate-900">Veriler Hazırlanıyor</p>
          <p class="text-slate-500 text-xs mt-1">Finansal raporlar analiz ediliyor...</p>
        </div>
        <LineChart v-else :data="revenueData" :height="280" />
      </div>
    </div>

    <!-- Satıcı Gelir Dağılımı -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-900">💰 Satıcı Gelir Dağılımı</h3>
        <p class="text-sm text-slate-500">Top 5 satıcının gelir paylaşımı</p>
      </div>
      <div class="p-6">
        <div v-if="!sellerData" class="flex h-[280px] flex-col items-center justify-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
          <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center mb-3">
            <span class="animate-pulse text-xl">💰</span>
          </div>
          <p class="font-medium text-slate-900">Satıcı Verileri Yükleniyor</p>
          <p class="text-slate-500 text-xs mt-1">Satış performansları hesaplanıyor...</p>
        </div>
        <BarChart v-else :data="sellerData" :height="280" />
      </div>
    </div>

    <!-- Sipariş Durumu -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-900">📦 Sipariş Durumu</h3>
        <p class="text-sm text-slate-500">Sipariş durum dağılımı</p>
      </div>
      <div class="p-6">
        <div v-if="!orderStatusData" class="flex h-[280px] flex-col items-center justify-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
          <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center mb-3">
            <span class="animate-pulse text-xl">📦</span>
          </div>
          <p class="font-medium text-slate-900">Sipariş Durumu</p>
          <p class="text-slate-500 text-xs mt-1">Anlık sipariş akışı bekleniyor...</p>
        </div>
        <DoughnutChart v-else :data="orderStatusData" :height="280" />
      </div>
    </div>

    <!-- Komisyon Analizi -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-900">💵 Komisyon Analizi</h3>
        <p class="text-sm text-slate-500">Platform geliri vs satıcı payı</p>
      </div>
      <div class="p-6">
        <div v-if="!commissionData" class="flex h-[280px] flex-col items-center justify-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
          <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center mb-3">
            <span class="animate-pulse text-xl">💵</span>
          </div>
          <p class="font-medium text-slate-900">Komisyon Analizi</p>
          <p class="text-slate-500 text-xs mt-1">Platform gelirleri hesaplanıyor...</p>
        </div>
        <DoughnutChart v-else :data="commissionData" :height="280" />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import LineChart from '@/components/charts/LineChart.vue'
import BarChart from '@/components/charts/BarChart.vue'
import DoughnutChart from '@/components/charts/DoughnutChart.vue'

defineProps<{
  revenueData: any
  sellerData: any
  orderStatusData: any
  commissionData: any
}>()
</script>
