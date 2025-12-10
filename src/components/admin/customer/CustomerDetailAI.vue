<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  customerId: number
  name: string
  totalSpent: number
  orderCount: number
}>()

const clvPrediction = ref({
  value: 15000,
  confidence: 85,
  trend: 'up'
})

const nextBestActions = ref([
  {
    id: 1,
    type: 'offer',
    title: 'Özel İndirim Tanımla',
    description: 'Sepet ortalamasını artırmak için %10 indirim kuponu öneriliyor.',
    impact: 'high'
  },
  {
    id: 2,
    type: 'contact',
    title: 'Geri Bildirim İste',
    description: 'Son siparişinden memnuniyet anketi gönder.',
    impact: 'medium'
  }
])

const churnRisk = ref(12) // 0-100

const riskLevel = computed(() => {
  if (churnRisk.value < 20) return { label: 'Düşük', color: 'text-emerald-600', bg: 'bg-emerald-50' }
  if (churnRisk.value < 50) return { label: 'Orta', color: 'text-orange-600', bg: 'bg-orange-50' }
  return { label: 'Yüksek', color: 'text-red-600', bg: 'bg-red-50' }
})

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)
}
</script>

<template>
  <div class="space-y-4">
    <!-- CLV Card -->
    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl p-4 text-white shadow-lg">
      <div class="flex justify-between items-start mb-2">
        <div>
          <p class="text-indigo-200 text-xs font-bold uppercase tracking-wider">Tahmini Yaşam Boyu Değer (CLV)</p>
          <h3 class="text-3xl font-bold mt-1">{{ formatCurrency(clvPrediction.value) }}</h3>
        </div>
        <div class="bg-white/20 px-2 py-1 rounded text-xs font-bold backdrop-blur-sm">
          %{{ clvPrediction.confidence }} Güven
        </div>
      </div>
      <p class="text-sm text-indigo-100">
        Bu müşteri önümüzdeki 12 ay içinde potansiyel olarak <span class="font-bold text-white">2.5x</span> daha fazla harcama yapabilir.
      </p>
    </div>

    <!-- Churn Risk -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
      <div>
        <p class="text-xs text-slate-500 font-bold uppercase">Kaybetme Riski (Churn)</p>
        <div class="flex items-center gap-2 mt-1">
          <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
            <div 
              class="h-full rounded-full transition-all duration-500"
              :class="churnRisk > 50 ? 'bg-red-500' : 'bg-emerald-500'"
              :style="{ width: churnRisk + '%' }"
            ></div>
          </div>
          <span class="font-bold text-slate-900">%{{ churnRisk }}</span>
        </div>
      </div>
      <span class="px-3 py-1 rounded-full text-xs font-bold" :class="riskLevel.bg + ' ' + riskLevel.color">
        {{ riskLevel.label }} Risk
      </span>
    </div>

    <!-- Next Best Actions -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="p-3 border-b border-slate-100 bg-slate-50">
        <h4 class="font-bold text-slate-800 text-sm">🤖 Sıradaki En İyi Aksiyon</h4>
      </div>
      <div class="divide-y divide-slate-100">
        <div v-for="action in nextBestActions" :key="action.id" class="p-3 hover:bg-slate-50 transition group cursor-pointer">
          <div class="flex justify-between items-start mb-1">
            <h5 class="font-bold text-slate-900 text-sm group-hover:text-indigo-600 transition">{{ action.title }}</h5>
            <span 
              class="w-2 h-2 rounded-full"
              :class="action.impact === 'high' ? 'bg-emerald-500' : 'bg-blue-500'"
              title="Etki Düzeyi"
            ></span>
          </div>
          <p class="text-xs text-slate-500 leading-relaxed">{{ action.description }}</p>
        </div>
      </div>
      <div class="p-3 bg-slate-50 border-t border-slate-100">
        <button class="w-full py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition">
          Aksiyon Al
        </button>
      </div>
    </div>
  </div>
</template>
