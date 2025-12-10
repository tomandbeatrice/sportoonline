<script setup lang="ts">
import { ref, onMounted } from 'vue'

const performanceScore = ref(78)
const scoreTrend = ref('up') // up, down, stable

const insights = ref([
  {
    id: 1,
    type: 'warning',
    title: 'Düşük Dönüşüm Oranı',
    message: '"Yaz İndirimi" kampanyasında tıklama yüksek ancak dönüşüm %1.2 seviyesinde (Ort: %2.5).',
    action: 'Landing Page\'i İncele'
  },
  {
    id: 2,
    type: 'success',
    title: 'Yüksek Performans',
    message: '"Spor Ayakkabı" kategorisi son 3 günde %45 artış gösterdi.',
    action: 'Bütçeyi Artır'
  },
  {
    id: 3,
    type: 'info',
    title: 'Fırsat',
    message: 'Hafta sonu yağmurlu görünüyor. "Outdoor" ürünlerinde talep artışı bekleniyor.',
    action: 'Kampanya Oluştur'
  }
])

const metrics = ref([
  { label: 'Tahmini Ciro', value: '₺450K', change: '+12%', trend: 'up' },
  { label: 'ROI', value: '4.2x', change: '+0.5', trend: 'up' },
  { label: 'Müşteri Maliyeti', value: '₺45', change: '-8%', trend: 'down' }, // down is good for cost
  { label: 'Churn Riski', value: '%15', change: '+2%', trend: 'up' } // up is bad for churn
])

const refreshAnalysis = () => {
  // Simulate API call
  performanceScore.value = Math.floor(Math.random() * (95 - 70) + 70)
}
</script>

<template>
  <div class="grid lg:grid-cols-3 gap-6">
    <!-- Score Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col items-center justify-center relative overflow-hidden">
      <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
      <h3 class="text-slate-500 font-medium mb-4">Genel Kampanya Sağlığı</h3>
      
      <div class="relative w-40 h-40 flex items-center justify-center">
        <!-- Circular Progress Background -->
        <svg class="w-full h-full transform -rotate-90">
          <circle
            cx="80"
            cy="80"
            r="70"
            stroke="currentColor"
            stroke-width="12"
            fill="transparent"
            class="text-slate-100"
          />
          <circle
            cx="80"
            cy="80"
            r="70"
            stroke="currentColor"
            stroke-width="12"
            fill="transparent"
            :stroke-dasharray="440"
            :stroke-dashoffset="440 - (440 * performanceScore) / 100"
            class="text-indigo-600 transition-all duration-1000 ease-out"
            stroke-linecap="round"
          />
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
          <span class="text-4xl font-bold text-slate-900">{{ performanceScore }}</span>
          <span class="text-sm text-slate-500">/ 100</span>
        </div>
      </div>

      <div class="mt-4 flex items-center gap-2 text-sm">
        <span class="flex items-center text-emerald-600 font-medium bg-emerald-50 px-2 py-1 rounded-full">
          <span class="mr-1">↑</span> %5
        </span>
        <span class="text-slate-400">Geçen haftaya göre</span>
      </div>
      
      <button @click="refreshAnalysis" class="mt-6 text-indigo-600 text-sm font-medium hover:text-indigo-700">
        Analizi Yenile
      </button>
    </div>

    <!-- Metrics Grid -->
    <div class="lg:col-span-2 grid sm:grid-cols-2 gap-4">
      <div v-for="(metric, index) in metrics" :key="index" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col justify-between">
        <div class="flex justify-between items-start mb-2">
          <span class="text-slate-500 text-sm font-medium">{{ metric.label }}</span>
          <span 
            class="text-xs font-bold px-2 py-1 rounded-full"
            :class="metric.trend === 'up' && metric.label !== 'Churn Riski' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
          >
            {{ metric.change }}
          </span>
        </div>
        <div class="text-2xl font-bold text-slate-900">{{ metric.value }}</div>
        
        <!-- Mini Chart Placeholder -->
        <div class="h-10 mt-4 flex items-end gap-1">
          <div 
            v-for="i in 10" 
            :key="i" 
            class="w-full bg-slate-100 rounded-t-sm hover:bg-indigo-100 transition-colors"
            :style="{ height: Math.random() * 100 + '%' }"
          ></div>
        </div>
      </div>
    </div>

    <!-- AI Insights List -->
    <div class="lg:col-span-3 bg-slate-900 rounded-2xl p-6 text-white shadow-lg">
      <div class="flex items-center gap-3 mb-6">
        <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
          ⚡
        </div>
        <h3 class="text-lg font-bold">AI Tespitleri & Öneriler</h3>
      </div>

      <div class="grid md:grid-cols-3 gap-4">
        <div 
          v-for="insight in insights" 
          :key="insight.id"
          class="bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/10 transition cursor-pointer group"
        >
          <div class="flex items-center justify-between mb-3">
            <span 
              class="text-xs font-bold px-2 py-1 rounded uppercase tracking-wider"
              :class="{
                'bg-yellow-500/20 text-yellow-400': insight.type === 'warning',
                'bg-emerald-500/20 text-emerald-400': insight.type === 'success',
                'bg-blue-500/20 text-blue-400': insight.type === 'info'
              }"
            >
              {{ insight.type === 'warning' ? 'Uyarı' : insight.type === 'success' ? 'Başarı' : 'Fırsat' }}
            </span>
            <span class="text-slate-400 text-xs">Az önce</span>
          </div>
          
          <h4 class="font-bold mb-2 group-hover:text-indigo-300 transition-colors">{{ insight.title }}</h4>
          <p class="text-sm text-slate-300 mb-4 leading-relaxed">{{ insight.message }}</p>
          
          <button class="w-full py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
            {{ insight.action }}
            <span>→</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
