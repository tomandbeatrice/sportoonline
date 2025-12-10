<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  sellerId: number
  storeName: string
  rating: number
  totalSales: number
}>()

const performanceScore = ref(88) // 0-100

const scoreColor = computed(() => {
  if (performanceScore.value >= 90) return 'text-emerald-600'
  if (performanceScore.value >= 70) return 'text-blue-600'
  if (performanceScore.value >= 50) return 'text-orange-600'
  return 'text-red-600'
})

const metrics = ref([
  { label: 'Kargolama Hızı', value: '1.2 Gün', status: 'good', benchmark: 'Ort. 1.5 Gün' },
  { label: 'İptal Oranı', value: '%0.5', status: 'excellent', benchmark: 'Ort. %1.2' },
  { label: 'İade Oranı', value: '%3.2', status: 'warning', benchmark: 'Ort. %2.8' },
  { label: 'Müşteri Yanıtı', value: '4 Saat', status: 'average', benchmark: 'Ort. 3 Saat' }
])

const aiSuggestions = ref([
  {
    id: 1,
    type: 'growth',
    title: 'Kampanya Fırsatı',
    description: '"Yaz İndirimleri" kampanyasına katılarak satışlarını %25 artırabilir.',
    impact: 'high'
  },
  {
    id: 2,
    type: 'operation',
    title: 'İade Analizi',
    description: 'Son 10 iadenin 6\'sı "Beden Uyumsuzluğu". Beden tablosunu güncellemesi önerilmeli.',
    impact: 'medium'
  }
])

const forecast = ref({
  nextMonth: 45000,
  trend: '+12%'
})

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)
}
</script>

<template>
  <div class="space-y-4">
    <!-- Performance Score Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm relative overflow-hidden">
      <div class="absolute top-0 right-0 p-2 opacity-10">
        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
      </div>
      
      <div class="flex justify-between items-end relative z-10">
        <div>
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Satıcı Performans Skoru</p>
          <div class="flex items-baseline gap-1 mt-1">
            <span class="text-4xl font-black" :class="scoreColor">{{ performanceScore }}</span>
            <span class="text-slate-400 text-sm font-medium">/ 100</span>
          </div>
        </div>
        <div class="text-right">
          <div class="text-xs font-bold text-slate-500 mb-1">Gelecek Ay Tahmini</div>
          <div class="font-bold text-slate-800">{{ formatCurrency(forecast.nextMonth) }}</div>
          <div class="text-xs text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full inline-block">
            {{ forecast.trend }} Büyüme
          </div>
        </div>
      </div>
    </div>

    <!-- Operational Metrics -->
    <div class="grid grid-cols-2 gap-3">
      <div v-for="metric in metrics" :key="metric.label" class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-xs text-slate-500 mb-1">{{ metric.label }}</p>
        <div class="flex justify-between items-end">
          <span class="font-bold text-slate-800">{{ metric.value }}</span>
          <span 
            class="w-2 h-2 rounded-full mb-1"
            :class="{
              'bg-emerald-500': metric.status === 'excellent' || metric.status === 'good',
              'bg-orange-500': metric.status === 'average' || metric.status === 'warning',
              'bg-red-500': metric.status === 'bad'
            }"
          ></span>
        </div>
        <p class="text-[10px] text-slate-400 mt-1">{{ metric.benchmark }}</p>
      </div>
    </div>

    <!-- AI Suggestions -->
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-4 text-white shadow-lg">
      <div class="flex items-center gap-2 mb-3 border-b border-slate-700 pb-2">
        <span class="text-lg">💡</span>
        <h4 class="font-bold text-sm">AI Tavsiyeleri</h4>
      </div>
      
      <div class="space-y-3">
        <div v-for="suggestion in aiSuggestions" :key="suggestion.id" class="bg-white/5 rounded-lg p-3 hover:bg-white/10 transition cursor-pointer border border-white/5">
          <div class="flex justify-between items-start mb-1">
            <span class="text-xs font-bold text-indigo-300 uppercase">{{ suggestion.type }}</span>
            <span class="text-[10px] px-1.5 py-0.5 rounded bg-white/20 font-bold">
              {{ suggestion.impact === 'high' ? 'Yüksek Etki' : 'Orta Etki' }}
            </span>
          </div>
          <h5 class="font-bold text-sm mb-1">{{ suggestion.title }}</h5>
          <p class="text-xs text-slate-300 leading-relaxed">{{ suggestion.description }}</p>
        </div>
      </div>
      
      <button class="w-full mt-3 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-xs font-bold transition">
        Tüm Tavsiyeleri Gör
      </button>
    </div>
  </div>
</template>
