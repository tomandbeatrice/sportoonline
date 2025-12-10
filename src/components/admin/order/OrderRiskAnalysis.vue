<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  orderId: string
  customerScore: number // 0-100
  totalAmount: number
  ipAddress: string
}>()

const isAnalyzing = ref(false)
const riskScore = ref(15) // 0-100 (Low is good)

const riskFactors = ref([
  { id: 1, label: 'IP Adresi Konumu', status: 'safe', message: 'Sipariş adresi ile IP lokasyonu eşleşiyor.' },
  { id: 2, label: 'Ödeme Davranışı', status: 'warning', message: 'Aynı kartla son 1 saatte 3. deneme.' },
  { id: 3, label: 'Sepet Tutarı', status: 'safe', message: 'Ortalama sepet tutarı ile uyumlu.' },
  { id: 4, label: 'Adres Doğrulama', status: 'safe', message: 'Adres veritabanında kayıtlı ve geçerli.' }
])

const riskLevel = computed(() => {
  if (riskScore.value < 30) return { label: 'Düşük Risk', color: 'text-emerald-600', bg: 'bg-emerald-50', border: 'border-emerald-200' }
  if (riskScore.value < 70) return { label: 'Orta Risk', color: 'text-orange-600', bg: 'bg-orange-50', border: 'border-orange-200' }
  return { label: 'Yüksek Risk', color: 'text-red-600', bg: 'bg-red-50', border: 'border-red-200' }
})

const reAnalyze = () => {
  isAnalyzing.value = true
  setTimeout(() => {
    isAnalyzing.value = false
    riskScore.value = Math.floor(Math.random() * 100)
  }, 1500)
}
</script>

<template>
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-bold text-slate-800 flex items-center gap-2">
        <span class="text-indigo-600">🛡️</span> Güvenlik Analizi
      </h3>
      <button 
        @click="reAnalyze"
        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
      >
        {{ isAnalyzing ? 'Analiz ediliyor...' : 'Tekrar Tara' }}
      </button>
    </div>

    <div class="p-4">
      <!-- Score Card -->
      <div 
        class="rounded-xl p-4 border mb-4 flex items-center justify-between"
        :class="[riskLevel.bg, riskLevel.border]"
      >
        <div>
          <p class="text-xs font-bold uppercase tracking-wider opacity-70" :class="riskLevel.color">Risk Skoru</p>
          <h4 class="text-3xl font-bold" :class="riskLevel.color">{{ riskScore }}/100</h4>
        </div>
        <div class="text-right">
          <div class="text-lg font-bold" :class="riskLevel.color">{{ riskLevel.label }}</div>
          <p class="text-xs opacity-70" :class="riskLevel.color">AI Güven Motoru</p>
        </div>
      </div>

      <!-- Factors -->
      <div class="space-y-3">
        <div v-for="factor in riskFactors" :key="factor.id" class="flex items-start gap-3 text-sm">
          <div 
            class="mt-0.5 w-5 h-5 rounded-full flex items-center justify-center shrink-0"
            :class="{
              'bg-emerald-100 text-emerald-600': factor.status === 'safe',
              'bg-orange-100 text-orange-600': factor.status === 'warning',
              'bg-red-100 text-red-600': factor.status === 'danger'
            }"
          >
            {{ factor.status === 'safe' ? '✓' : '!' }}
          </div>
          <div>
            <p class="font-medium text-slate-900">{{ factor.label }}</p>
            <p class="text-slate-500 text-xs leading-relaxed">{{ factor.message }}</p>
          </div>
        </div>
      </div>

      <!-- Action -->
      <div v-if="riskScore > 50" class="mt-4 pt-4 border-t border-slate-100">
        <button class="w-full py-2 bg-white border border-red-200 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition">
          ⚠️ Manuel İncelemeye Al
        </button>
      </div>
    </div>
  </div>
</template>
