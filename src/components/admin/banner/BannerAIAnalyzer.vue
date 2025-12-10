<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  bannerId: number
  imageUrl: string
  title: string
  ctr: number // Current Click Through Rate
}>()

const attentionScore = ref(82) // 0-100
const conversionScore = ref(65) // 0-100

const getScoreColor = (score: number) => {
  if (score >= 80) return 'text-emerald-600'
  if (score >= 50) return 'text-blue-600'
  return 'text-orange-600'
}

const heatmapAnalysis = ref([
  { area: 'Başlık', attention: 'High', note: 'Kullanıcıların %70\'i ilk buraya bakıyor.' },
  { area: 'CTA Butonu', attention: 'Medium', note: 'Buton rengi arka planla az kontrast oluşturuyor.' },
  { area: 'Ürün Görseli', attention: 'High', note: 'Görsel net ve ilgi çekici.' }
])

const aiSuggestions = ref([
  {
    id: 1,
    type: 'color',
    title: 'Kontrast İyileştirmesi',
    description: 'CTA butonunu "Turuncu" (#F97316) yaparak tıklama oranını %15 artırabilirsiniz.'
  },
  {
    id: 2,
    type: 'text',
    title: 'Metin Optimizasyonu',
    description: '"İndirimi Kaçırma" yerine "Şimdi %50 İndirimli Al" yazısı daha harekete geçirici olabilir.'
  }
])

const abTestProposal = ref({
  variant: 'B',
  change: 'Buton Rengi & Başlık',
  predictedImpact: '+22% Tıklama'
})

</script>

<template>
  <div class="space-y-4">
    <!-- Scores -->
    <div class="grid grid-cols-2 gap-3">
      <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-sm text-center">
        <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">Dikkat Skoru</p>
        <div class="text-3xl font-black" :class="getScoreColor(attentionScore)">{{ attentionScore }}</div>
        <p class="text-[10px] text-slate-400">Görsel Çekicilik</p>
      </div>
      <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-sm text-center">
        <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">Dönüşüm Skoru</p>
        <div class="text-3xl font-black" :class="getScoreColor(conversionScore)">{{ conversionScore }}</div>
        <p class="text-[10px] text-slate-400">Satış Potansiyeli</p>
      </div>
    </div>

    <!-- Heatmap Analysis (Text based for now) -->
    <div class="bg-slate-800 rounded-xl p-4 text-white shadow-lg">
      <h4 class="font-bold text-sm mb-3 flex items-center gap-2">
        <span>🔥</span> Isı Haritası Analizi
      </h4>
      <div class="space-y-3">
        <div v-for="(item, index) in heatmapAnalysis" :key="index" class="flex justify-between items-start border-b border-white/10 pb-2 last:border-0 last:pb-0">
          <div>
            <span class="text-xs font-bold text-indigo-300">{{ item.area }}</span>
            <p class="text-[10px] text-slate-300 mt-0.5">{{ item.note }}</p>
          </div>
          <span 
            class="text-[10px] px-1.5 py-0.5 rounded font-bold"
            :class="item.attention === 'High' ? 'bg-red-500/20 text-red-300' : 'bg-yellow-500/20 text-yellow-300'"
          >
            {{ item.attention }}
          </span>
        </div>
      </div>
    </div>

    <!-- AI Suggestions -->
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
      <h4 class="text-indigo-900 font-bold text-sm mb-3 flex items-center gap-2">
        <span>💡</span> İyileştirme Önerileri
      </h4>
      <div class="space-y-3">
        <div v-for="suggestion in aiSuggestions" :key="suggestion.id" class="flex gap-3 items-start">
          <span class="text-lg mt-0.5" v-if="suggestion.type === 'color'">🎨</span>
          <span class="text-lg mt-0.5" v-else>✍️</span>
          <div>
            <h5 class="text-xs font-bold text-indigo-800">{{ suggestion.title }}</h5>
            <p class="text-xs text-indigo-700 leading-relaxed mt-0.5">{{ suggestion.description }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- A/B Test Proposal -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-4 text-white shadow-md">
      <div class="flex justify-between items-center mb-2">
        <span class="text-xs font-bold uppercase opacity-80">Otomatik A/B Testi</span>
        <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-bold">Öneri</span>
      </div>
      <p class="text-sm font-bold mb-1">{{ abTestProposal.change }}</p>
      <div class="flex items-center gap-2 text-xs opacity-90">
        <span>Beklenen Etki:</span>
        <span class="font-bold text-emerald-300">{{ abTestProposal.predictedImpact }}</span>
      </div>
      <button class="w-full mt-3 py-1.5 bg-white text-indigo-600 rounded-lg text-xs font-bold hover:bg-indigo-50 transition">
        Testi Başlat
      </button>
    </div>
  </div>
</template>
