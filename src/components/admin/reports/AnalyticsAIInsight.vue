<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  reportType: string
  dataSummary: any
}>()

const forecastData = ref([
  { day: 'Pzt', value: 12500, trend: 'up' },
  { day: 'Sal', value: 13200, trend: 'up' },
  { day: 'Çar', value: 12800, trend: 'down' },
  { day: 'Per', value: 14500, trend: 'up' },
  { day: 'Cum', value: 16000, trend: 'up' },
  { day: 'Cmt', value: 18500, trend: 'up' },
  { day: 'Paz', value: 19000, trend: 'up' }
])

const insights = ref([
  {
    type: 'positive',
    title: 'Yükseliş Trendi',
    message: 'Spor ayakkabı kategorisindeki satışlar son 3 günde %24 artış gösterdi.'
  },
  {
    type: 'negative',
    title: 'Stok Uyarısı',
    message: 'En çok satan 5 ürünün stokları kritik seviyenin altına inmek üzere.'
  },
  {
    type: 'neutral',
    title: 'Müşteri Davranışı',
    message: 'Mobil trafik masaüstünü geçti (%65 vs %35), ancak dönüşüm oranı mobilde daha düşük.'
  }
])

const aiQuery = ref('')
const aiResponse = ref<string | null>(null)
const isThinking = ref(false)

const askAI = () => {
  if (!aiQuery.value) return
  isThinking.value = true
  aiResponse.value = null
  
  setTimeout(() => {
    isThinking.value = false
    aiResponse.value = `"${aiQuery.value}" analizi yapıldı: Geçen aya göre %12'lik bir artış gözlemleniyor. En büyük etken "Yaz İndirimi" kampanyası.`
  }, 1500)
}
</script>

<template>
  <div class="space-y-4">
    <!-- Predictive Forecast -->
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-4 text-white shadow-lg">
      <h4 class="font-bold text-sm mb-3 flex items-center gap-2">
        <span>🔮</span> AI Tahmini (Gelecek 7 Gün)
      </h4>
      <div class="flex items-end justify-between h-24 gap-1">
        <div v-for="(day, idx) in forecastData" :key="idx" class="flex flex-col items-center gap-1 flex-1 group">
          <div class="text-[10px] opacity-0 group-hover:opacity-100 transition-opacity bg-white text-slate-900 px-1 rounded absolute -mt-6">
            {{ (day.value / 1000).toFixed(1) }}k
          </div>
          <div 
            class="w-full bg-indigo-500/50 rounded-t hover:bg-indigo-400 transition-colors relative"
            :style="{ height: (day.value / 20000 * 100) + '%' }"
          >
            <div v-if="day.trend === 'up'" class="absolute -top-1 left-1/2 -translate-x-1/2 w-1 h-1 bg-emerald-400 rounded-full"></div>
          </div>
          <span class="text-[10px] text-slate-400">{{ day.day }}</span>
        </div>
      </div>
      <div class="mt-3 text-xs text-slate-400 flex justify-between items-center">
        <span>Güven Aralığı: %85</span>
        <span class="text-emerald-400 font-bold">↗ Beklenen Büyüme</span>
      </div>
    </div>

    <!-- Smart Insights -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
      <h4 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
        <span>🧠</span> Akıllı İçgörüler
      </h4>
      <div class="space-y-3">
        <div v-for="(insight, idx) in insights" :key="idx" class="flex gap-3 items-start">
          <span v-if="insight.type === 'positive'" class="text-emerald-500 text-lg mt-0.5">📈</span>
          <span v-if="insight.type === 'negative'" class="text-red-500 text-lg mt-0.5">⚠️</span>
          <span v-if="insight.type === 'neutral'" class="text-blue-500 text-lg mt-0.5">ℹ️</span>
          <div>
            <h5 class="text-xs font-bold text-slate-800">{{ insight.title }}</h5>
            <p class="text-xs text-slate-600 leading-relaxed mt-0.5">{{ insight.message }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Ask AI -->
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
      <h4 class="text-indigo-900 font-bold text-sm mb-3 flex items-center gap-2">
        <span>💬</span> Veriye Soru Sor
      </h4>
      
      <div class="relative">
        <input 
          v-model="aiQuery"
          @keydown.enter="askAI"
          type="text" 
          placeholder="Örn: Geçen hafta en çok iade edilen ürün hangisi?" 
          class="w-full pl-3 pr-10 py-2 bg-white border border-indigo-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
        >
        <button 
          @click="askAI"
          class="absolute right-1 top-1 p-1 text-indigo-600 hover:bg-indigo-100 rounded"
        >
          ➤
        </button>
      </div>

      <div v-if="isThinking" class="mt-3 flex items-center gap-2 text-xs text-indigo-600">
        <span class="animate-spin">⚙️</span> Analiz ediliyor...
      </div>

      <div v-if="aiResponse" class="mt-3 bg-white p-3 rounded-lg border border-indigo-100 text-xs text-slate-700 leading-relaxed">
        <span class="font-bold text-indigo-600">AI:</span> {{ aiResponse }}
      </div>
    </div>
  </div>
</template>
