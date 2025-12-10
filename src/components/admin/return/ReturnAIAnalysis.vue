<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  returnId: number
  reason: string
  customerHistory: {
    totalReturns: number
    returnRate: number // percentage
  }
  images: string[]
}>()

const fraudScore = ref(12) // 0-100 (Low is good)

const riskLevel = computed(() => {
  if (fraudScore.value < 20) return { label: 'Düşük Risk', color: 'text-emerald-600', bg: 'bg-emerald-50', bar: 'bg-emerald-500' }
  if (fraudScore.value < 50) return { label: 'Orta Risk', color: 'text-orange-600', bg: 'bg-orange-50', bar: 'bg-orange-500' }
  return { label: 'Yüksek Risk', color: 'text-red-600', bg: 'bg-red-50', bar: 'bg-red-500' }
})

const imageAnalysis = ref([
  {
    id: 1,
    status: 'detected',
    label: 'Ürün Etiketi',
    confidence: 98,
    message: 'Ürün etiketi üzerinde ve hasarsız görünüyor.'
  },
  {
    id: 2,
    status: 'detected',
    label: 'Kullanım İzi',
    confidence: 15,
    message: 'Belirgin bir kullanım izi veya leke tespit edilmedi.'
  }
])

const policyCheck = ref([
  { id: 1, rule: 'İade Süresi (14 Gün)', passed: true, detail: 'Siparişten 3 gün sonra talep edildi.' },
  { id: 2, rule: 'Kategori Uygunluğu', passed: true, detail: 'İç giyim/Hijyen kategorisinde değil.' },
  { id: 3, rule: 'Müşteri İade Limiti', passed: true, detail: 'Aylık limit aşılmadı (1/3).' }
])

const recommendation = ref({
  action: 'approve', // approve, reject, inspect
  text: 'Otomatik Onay',
  reason: 'Düşük risk skoru ve politika uygunluğu nedeniyle anında onay öneriliyor.'
})

</script>

<template>
  <div class="space-y-4">
    <!-- Fraud Risk Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
      <div class="flex justify-between items-center mb-2">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">İade Risk Analizi</h4>
        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase" :class="riskLevel.bg + ' ' + riskLevel.color">
          {{ riskLevel.label }}
        </span>
      </div>
      
      <div class="flex items-end gap-2 mb-2">
        <span class="text-3xl font-black text-slate-800">{{ fraudScore }}</span>
        <span class="text-sm text-slate-400 mb-1">/ 100</span>
      </div>

      <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
        <div class="h-full transition-all duration-500" :class="riskLevel.bar" :style="{ width: fraudScore + '%' }"></div>
      </div>
      
      <div class="mt-3 flex gap-4 text-xs text-slate-500">
        <div>
          <span class="block font-bold text-slate-700">%{{ customerHistory.returnRate }}</span>
          <span>İade Oranı</span>
        </div>
        <div>
          <span class="block font-bold text-slate-700">{{ customerHistory.totalReturns }}</span>
          <span>Toplam İade</span>
        </div>
      </div>
    </div>

    <!-- Policy Checks -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
      <div class="p-3 bg-slate-50 border-b border-slate-200">
        <h4 class="font-bold text-slate-800 text-sm">📋 Politika Kontrolü</h4>
      </div>
      <div class="divide-y divide-slate-100">
        <div v-for="check in policyCheck" :key="check.id" class="p-3 flex items-start gap-3">
          <div class="mt-0.5">
            <span v-if="check.passed" class="text-emerald-500 text-lg">✓</span>
            <span v-else class="text-red-500 text-lg">✕</span>
          </div>
          <div>
            <p class="text-sm font-medium text-slate-900">{{ check.rule }}</p>
            <p class="text-xs text-slate-500">{{ check.detail }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Image Analysis (Mock) -->
    <div class="bg-slate-800 rounded-xl p-4 text-white shadow-lg">
      <h4 class="font-bold text-sm mb-3 flex items-center gap-2">
        <span>📷</span> Görsel Analizi (AI)
      </h4>
      <div class="space-y-3">
        <div v-for="analysis in imageAnalysis" :key="analysis.id" class="bg-white/10 rounded-lg p-3 border border-white/5">
          <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-bold text-indigo-300">{{ analysis.label }}</span>
            <span class="text-[10px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.5 rounded">
              %{{ analysis.confidence }} Güven
            </span>
          </div>
          <p class="text-xs text-slate-300">{{ analysis.message }}</p>
        </div>
      </div>
    </div>

    <!-- Recommendation -->
    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4">
      <div class="flex items-center gap-2 mb-2">
        <span class="text-xl">🤖</span>
        <h4 class="text-emerald-800 font-bold text-sm">AI Önerisi: {{ recommendation.text }}</h4>
      </div>
      <p class="text-emerald-700 text-xs leading-relaxed">
        {{ recommendation.reason }}
      </p>
      <button class="mt-3 w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">
        Öneriyi Uygula
      </button>
    </div>
  </div>
</template>
