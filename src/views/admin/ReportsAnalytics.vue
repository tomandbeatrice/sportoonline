<script setup lang="ts">
import { ref, computed } from 'vue'
import AnalyticsAIInsight from '@/components/admin/reports/AnalyticsAIInsight.vue'

// --- Types ---
interface ReportCategory {
  id: string
  label: string
  icon: string
  description: string
}

// --- State ---
const activeCategoryId = ref('sales')
const dateRange = ref('last_30_days')

const categories: ReportCategory[] = [
  { id: 'sales', label: 'Satış Raporları', icon: '💰', description: 'Gelir, kar marjı ve vergi raporları' },
  { id: 'orders', label: 'Sipariş Analizi', icon: '📦', description: 'Sipariş durumu, iptaller ve iadeler' },
  { id: 'customers', label: 'Müşteri İçgörüleri', icon: '👥', description: 'Demografi, sadakat ve LTV' },
  { id: 'products', label: 'Ürün Performansı', icon: '👕', description: 'En çok satanlar ve stok devir hızı' },
  { id: 'traffic', label: 'Trafik & Dönüşüm', icon: '🌐', description: 'Ziyaretçi kaynakları ve dönüşüm hunisi' }
]

const metrics = ref({
  totalRevenue: 1254000,
  revenueChange: 12.5,
  totalOrders: 3450,
  ordersChange: 5.2,
  avgOrderValue: 363.47,
  avgChange: -2.1,
  conversionRate: 2.8,
  conversionChange: 0.4
})

// --- Methods ---
const exportReport = (format: 'excel' | 'pdf') => {
  alert(`${format.toUpperCase()} formatında rapor hazırlanıyor...`)
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)
}
</script>

<template>
  <div class="h-[calc(100vh-4rem)] flex flex-col bg-slate-50 overflow-hidden">
    <!-- Top Bar -->
    <div class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shrink-0">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
          📊 Raporlama & Analitik
          <span class="text-xs font-normal bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full border border-indigo-200">Canlı Veri</span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">İşletmenizin performansını derinlemesine analiz edin</p>
      </div>
      
      <div class="flex gap-3">
        <select v-model="dateRange" class="bg-white border border-slate-200 text-slate-700 text-sm rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
          <option value="today">Bugün</option>
          <option value="yesterday">Dün</option>
          <option value="last_7_days">Son 7 Gün</option>
          <option value="last_30_days">Son 30 Gün</option>
          <option value="this_month">Bu Ay</option>
          <option value="last_month">Geçen Ay</option>
          <option value="this_year">Bu Yıl</option>
        </select>
        
        <div class="h-full w-px bg-slate-200 mx-1"></div>

        <button @click="exportReport('excel')" class="bg-emerald-50 text-emerald-700 hover:bg-emerald-100 px-4 py-2 rounded-lg text-sm font-bold transition border border-emerald-200 flex items-center gap-2">
          <span>📥</span> Excel
        </button>
        <button @click="exportReport('pdf')" class="bg-red-50 text-red-700 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-bold transition border border-red-200 flex items-center gap-2">
          <span>📄</span> PDF
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-1 overflow-hidden">
      
      <!-- Left Panel: Categories -->
      <div class="w-64 bg-white border-r border-slate-200 flex flex-col overflow-y-auto">
        <div class="p-4 space-y-1">
          <button 
            v-for="cat in categories" 
            :key="cat.id"
            @click="activeCategoryId = cat.id"
            class="w-full text-left px-4 py-3 rounded-xl flex items-center gap-3 transition-all duration-200"
            :class="activeCategoryId === cat.id ? 'bg-indigo-50 text-indigo-700 font-bold ring-1 ring-indigo-200' : 'text-slate-600 hover:bg-slate-50'"
          >
            <span class="text-xl">{{ cat.icon }}</span>
            <div>
              <div class="text-sm">{{ cat.label }}</div>
              <div class="text-[10px] opacity-70 font-normal truncate max-w-[120px]">{{ cat.description }}</div>
            </div>
          </button>
        </div>
      </div>

      <!-- Middle Panel: Charts & Data -->
      <div class="flex-1 overflow-y-auto p-8 bg-slate-50">
        <div class="max-w-4xl mx-auto space-y-6">
          
          <!-- Key Metrics Cards -->
          <div class="grid grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
              <div class="text-xs text-slate-500 font-bold uppercase mb-1">Toplam Gelir</div>
              <div class="text-xl font-black text-slate-800">{{ formatCurrency(metrics.totalRevenue) }}</div>
              <div class="text-xs font-bold mt-1" :class="metrics.revenueChange >= 0 ? 'text-emerald-600' : 'text-red-600'">
                {{ metrics.revenueChange >= 0 ? '↗' : '↘' }} %{{ Math.abs(metrics.revenueChange) }}
              </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
              <div class="text-xs text-slate-500 font-bold uppercase mb-1">Siparişler</div>
              <div class="text-xl font-black text-slate-800">{{ metrics.totalOrders.toLocaleString() }}</div>
              <div class="text-xs font-bold mt-1" :class="metrics.ordersChange >= 0 ? 'text-emerald-600' : 'text-red-600'">
                {{ metrics.ordersChange >= 0 ? '↗' : '↘' }} %{{ Math.abs(metrics.ordersChange) }}
              </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
              <div class="text-xs text-slate-500 font-bold uppercase mb-1">Ort. Sepet</div>
              <div class="text-xl font-black text-slate-800">{{ formatCurrency(metrics.avgOrderValue) }}</div>
              <div class="text-xs font-bold mt-1" :class="metrics.avgChange >= 0 ? 'text-emerald-600' : 'text-red-600'">
                {{ metrics.avgChange >= 0 ? '↗' : '↘' }} %{{ Math.abs(metrics.avgChange) }}
              </div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
              <div class="text-xs text-slate-500 font-bold uppercase mb-1">Dönüşüm</div>
              <div class="text-xl font-black text-slate-800">%{{ metrics.conversionRate }}</div>
              <div class="text-xs font-bold mt-1" :class="metrics.conversionChange >= 0 ? 'text-emerald-600' : 'text-red-600'">
                {{ metrics.conversionChange >= 0 ? '↗' : '↘' }} %{{ Math.abs(metrics.conversionChange) }}
              </div>
            </div>
          </div>

          <!-- Main Chart Area -->
          <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-lg font-bold text-slate-800">
                {{ activeCategoryId === 'sales' ? 'Gelir Grafiği' : 'Trend Analizi' }}
              </h2>
              <div class="flex gap-2">
                <button class="px-3 py-1 text-xs font-bold bg-slate-100 text-slate-600 rounded hover:bg-slate-200">Günlük</button>
                <button class="px-3 py-1 text-xs font-bold bg-white border border-slate-200 text-slate-600 rounded hover:bg-slate-50">Haftalık</button>
              </div>
            </div>
            
            <!-- Mock Chart Placeholder -->
            <div class="h-64 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center relative overflow-hidden group">
              <div class="absolute inset-0 flex items-end justify-between px-4 pb-0 pt-10 gap-2 opacity-50">
                <div class="w-full bg-indigo-200 rounded-t h-[40%]"></div>
                <div class="w-full bg-indigo-300 rounded-t h-[60%]"></div>
                <div class="w-full bg-indigo-400 rounded-t h-[50%]"></div>
                <div class="w-full bg-indigo-500 rounded-t h-[80%]"></div>
                <div class="w-full bg-indigo-600 rounded-t h-[70%]"></div>
                <div class="w-full bg-indigo-500 rounded-t h-[90%]"></div>
                <div class="w-full bg-indigo-400 rounded-t h-[65%]"></div>
              </div>
              <div class="z-10 bg-white/80 backdrop-blur px-4 py-2 rounded-lg shadow-sm text-sm font-bold text-slate-600">
                Grafik Alanı (Chart.js / ApexCharts)
              </div>
            </div>
          </div>

          <!-- Detailed Table -->
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
              <h3 class="font-bold text-slate-800">Detaylı Döküm</h3>
              <button class="text-indigo-600 text-sm font-bold hover:underline">Tümünü Gör</button>
            </div>
            <table class="w-full text-sm text-left">
              <thead class="bg-slate-50 text-slate-500 font-medium">
                <tr>
                  <th class="px-6 py-3">Kategori / Ürün</th>
                  <th class="px-6 py-3 text-right">Satış Adedi</th>
                  <th class="px-6 py-3 text-right">Gelir</th>
                  <th class="px-6 py-3 text-right">Trend</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr class="hover:bg-slate-50 transition">
                  <td class="px-6 py-3 font-medium text-slate-700">Spor Ayakkabı</td>
                  <td class="px-6 py-3 text-right">1,240</td>
                  <td class="px-6 py-3 text-right font-mono">₺845,000</td>
                  <td class="px-6 py-3 text-right text-emerald-600 font-bold">↗ %12</td>
                </tr>
                <tr class="hover:bg-slate-50 transition">
                  <td class="px-6 py-3 font-medium text-slate-700">Koşu Kıyafetleri</td>
                  <td class="px-6 py-3 text-right">850</td>
                  <td class="px-6 py-3 text-right font-mono">₺210,500</td>
                  <td class="px-6 py-3 text-right text-emerald-600 font-bold">↗ %5</td>
                </tr>
                <tr class="hover:bg-slate-50 transition">
                  <td class="px-6 py-3 font-medium text-slate-700">Aksesuarlar</td>
                  <td class="px-6 py-3 text-right">420</td>
                  <td class="px-6 py-3 text-right font-mono">₺45,200</td>
                  <td class="px-6 py-3 text-right text-red-600 font-bold">↘ %2</td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>

      <!-- Right Panel: AI Advisor -->
      <div class="w-80 bg-white border-l border-slate-200 p-6 overflow-y-auto">
        <h3 class="font-bold text-slate-800 mb-4">Analitik Asistanı</h3>
        <AnalyticsAIInsight 
          :report-type="activeCategoryId"
          :data-summary="metrics"
        />
      </div>

    </div>
  </div>
</template>
