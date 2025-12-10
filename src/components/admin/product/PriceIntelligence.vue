<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  currentPrice: number
}>()

const competitors = ref([
  { name: 'Trendyol', price: 450, diff: 12, url: '#' },
  { name: 'Hepsiburada', price: 435, diff: 8, url: '#' },
  { name: 'Amazon', price: 420, diff: 5, url: '#' }
])

const suggestedPrice = ref(415)
const profitMargin = ref(25)

const marketTrend = ref('stable') // up, down, stable

</script>

<template>
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-bold text-slate-800 flex items-center gap-2">
        <span class="text-emerald-600">💰</span> Fiyat İstihbaratı
      </h3>
      <span class="text-xs text-slate-400">Son güncelleme: 10 dk önce</span>
    </div>

    <div class="p-4">
      <!-- Smart Suggestion -->
      <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-100 mb-6">
        <div class="flex justify-between items-start mb-2">
          <div>
            <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">AI Önerisi</p>
            <h4 class="text-2xl font-bold text-emerald-900">{{ suggestedPrice }} ₺</h4>
          </div>
          <div class="text-right">
            <p class="text-xs text-emerald-600">Tahmini Kar Marjı</p>
            <p class="font-bold text-emerald-800">%{{ profitMargin }}</p>
          </div>
        </div>
        <p class="text-sm text-emerald-700 leading-snug">
          Rakipler ortalama 435₺ seviyesinde. 415₺ fiyat ile "En İyi Fiyat" etiketini alabilir ve satışları %15 artırabilirsiniz.
        </p>
        <button class="mt-3 w-full py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 transition">
          Önerilen Fiyatı Uygula
        </button>
      </div>

      <!-- Competitor List -->
      <div>
        <h4 class="text-xs font-bold text-slate-500 uppercase mb-3">Rakip Analizi</h4>
        <div class="space-y-2">
          <div v-for="comp in competitors" :key="comp.name" class="flex items-center justify-between p-2 hover:bg-slate-50 rounded-lg transition group">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                {{ comp.name[0] }}
              </div>
              <span class="text-sm font-medium text-slate-700">{{ comp.name }}</span>
            </div>
            <div class="text-right">
              <div class="font-bold text-slate-900">{{ comp.price }} ₺</div>
              <div class="text-xs text-red-500 group-hover:opacity-100 opacity-0 transition">
                Bizden %{{ comp.diff }} pahalı
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
