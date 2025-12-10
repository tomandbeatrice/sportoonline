<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  destinationCity: string
  weight: number
}>()

const recommendedCarrier = ref('yurtici')

const carriers = ref([
  { 
    id: 'yurtici', 
    name: 'Yurtiçi Kargo', 
    logo: '🚚', 
    price: 45.90, 
    eta: 'Yarın', 
    score: 98,
    bestFor: 'Hız'
  },
  { 
    id: 'aras', 
    name: 'Aras Kargo', 
    logo: '🚛', 
    price: 38.50, 
    eta: '2 Gün', 
    score: 92,
    bestFor: 'Fiyat'
  },
  { 
    id: 'mng', 
    name: 'MNG Kargo', 
    logo: '📦', 
    price: 42.00, 
    eta: '2 Gün', 
    score: 85,
    bestFor: ''
  }
])

const selectCarrier = (id: string) => {
  recommendedCarrier.value = id
}
</script>

<template>
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-100">
      <h3 class="font-bold text-slate-800 flex items-center gap-2">
        <span class="text-blue-600">📦</span> Akıllı Kargo Seçimi
      </h3>
    </div>

    <div class="p-4">
      <div class="mb-4 bg-blue-50 p-3 rounded-lg border border-blue-100">
        <p class="text-sm text-blue-800">
          <span class="font-bold">AI Önerisi:</span> {{ destinationCity }} bölgesi için en yüksek teslimat başarısı <span class="font-bold">Yurtiçi Kargo</span> firmasında (%99.2).
        </p>
      </div>

      <div class="space-y-2">
        <div 
          v-for="carrier in carriers" 
          :key="carrier.id"
          @click="selectCarrier(carrier.id)"
          class="relative p-3 rounded-lg border cursor-pointer transition flex items-center justify-between group"
          :class="recommendedCarrier === carrier.id ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-500' : 'border-slate-200 hover:border-blue-300 hover:bg-slate-50'"
        >
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white rounded-lg border border-slate-200 flex items-center justify-center text-xl shadow-sm">
              {{ carrier.logo }}
            </div>
            <div>
              <div class="font-bold text-slate-900 flex items-center gap-2">
                {{ carrier.name }}
                <span v-if="carrier.bestFor" class="px-1.5 py-0.5 bg-slate-900 text-white text-[10px] rounded uppercase tracking-wider">
                  {{ carrier.bestFor }}
                </span>
              </div>
              <div class="text-xs text-slate-500">Tahmini: {{ carrier.eta }}</div>
            </div>
          </div>

          <div class="text-right">
            <div class="font-bold text-slate-900">{{ carrier.price.toFixed(2) }} ₺</div>
            <div class="text-xs font-medium" :class="carrier.score > 90 ? 'text-emerald-600' : 'text-orange-600'">
              {{ carrier.score }} Puan
            </div>
          </div>

          <div v-if="recommendedCarrier === carrier.id" class="absolute -right-1 -top-1 w-4 h-4 bg-blue-500 rounded-full border-2 border-white"></div>
        </div>
      </div>

      <button class="mt-4 w-full py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
        Kargo Etiketi Oluştur
      </button>
    </div>
  </div>
</template>
