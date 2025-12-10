<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  transactionId: number
  amount: number
  user: string
  type: 'withdrawal' | 'deposit' | 'commission'
}>()

const riskScore = ref(15) // 0-100 (Low is good)

const riskLevel = computed(() => {
  if (riskScore.value < 20) return { label: 'Düşük Risk', color: 'text-emerald-600', bg: 'bg-emerald-50', bar: 'bg-emerald-500' }
  if (riskScore.value < 50) return { label: 'Orta Risk', color: 'text-orange-600', bg: 'bg-orange-50', bar: 'bg-orange-500' }
  return { label: 'Yüksek Risk', color: 'text-red-600', bg: 'bg-red-50', bar: 'bg-red-500' }
})

const aiAnalysis = ref([
  {
    id: 1,
    icon: '✅',
    text: 'Kullanıcının geçmiş 12 işlemi sorunsuz tamamlandı.',
    sentiment: 'positive'
  },
  {
    id: 2,
    icon: '📊',
    text: 'Talep edilen tutar, kullanıcının ortalama işlem hacminin %15 üzerinde (Normal aralıkta).',
    sentiment: 'neutral'
  },
  {
    id: 3,
    icon: '🌍',
    text: 'IP adresi ve cihaz bilgisi kayıtlı profil ile eşleşiyor.',
    sentiment: 'positive'
  }
])

const cashFlowForecast = ref({
  nextWeek: 125000,
  trend: 'up',
  percentage: 8
})

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)
}
</script>

<template>
  <div class="space-y-4">
    <!-- Risk Assessment Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
      <div class="flex justify-between items-center mb-2">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">İşlem Risk Skoru</h4>
        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase" :class="riskLevel.bg + ' ' + riskLevel.color">
          {{ riskLevel.label }}
        </span>
      </div>
      
      <div class="flex items-end gap-2 mb-2">
        <span class="text-3xl font-black text-slate-800">{{ riskScore }}</span>
        <span class="text-sm text-slate-400 mb-1">/ 100</span>
      </div>

      <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
        <div class="h-full transition-all duration-500" :class="riskLevel.bar" :style="{ width: riskScore + '%' }"></div>
      </div>
      
      <p class="text-xs text-slate-500 mt-2">
        AI bu işlemin güvenli olduğunu öngörüyor. Otomatik onay önerilir.
      </p>
    </div>

    <!-- AI Analysis List -->
    <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
      <h4 class="text-indigo-900 font-bold text-sm mb-3 flex items-center gap-2">
        <span>🤖</span> İşlem Analizi
      </h4>
      <div class="space-y-3">
        <div v-for="item in aiAnalysis" :key="item.id" class="flex gap-3 items-start">
          <span class="text-sm">{{ item.icon }}</span>
          <p class="text-xs text-indigo-800 leading-relaxed">{{ item.text }}</p>
        </div>
      </div>
    </div>

    <!-- Cash Flow Impact -->
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-4 text-white shadow-lg">
      <p class="text-slate-400 text-xs font-bold uppercase mb-1">Nakit Akışı Tahmini</p>
      <div class="flex justify-between items-end">
        <div>
          <h3 class="text-2xl font-bold">{{ formatCurrency(cashFlowForecast.nextWeek) }}</h3>
          <p class="text-xs text-slate-400">Gelecek Hafta Beklenen</p>
        </div>
        <div class="text-right">
          <span class="text-emerald-400 font-bold text-lg">↑ %{{ cashFlowForecast.percentage }}</span>
        </div>
      </div>
      <div class="mt-3 pt-3 border-t border-white/10">
        <p class="text-[10px] text-slate-300">
          Bu işlem nakit akışını kritik seviyede etkilemeyecek. Rezerv oranı: %85
        </p>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="grid grid-cols-2 gap-3">
      <button class="py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-sm">
        ✅ Güvenli Onay
      </button>
      <button class="py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-bold transition">
        🔍 Detaylı İncele
      </button>
    </div>
  </div>
</template>
