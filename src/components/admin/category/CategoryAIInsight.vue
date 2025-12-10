<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  categoryName: string
  productCount: number
  searchVolume: number // Monthly search volume
}>()

const seoScore = ref(72) // 0-100

const seoLevel = computed(() => {
  if (seoScore.value >= 80) return { label: 'Mükemmel', color: 'text-emerald-600', bg: 'bg-emerald-50', bar: 'bg-emerald-500' }
  if (seoScore.value >= 50) return { label: 'İyi', color: 'text-blue-600', bg: 'bg-blue-50', bar: 'bg-blue-500' }
  return { label: 'Geliştirilmeli', color: 'text-orange-600', bg: 'bg-orange-50', bar: 'bg-orange-500' }
})

const keywords = ref([
  { text: 'koşu ayakkabısı', volume: '12K', competition: 'High' },
  { text: 'spor ayakkabı modelleri', volume: '8.5K', competition: 'Medium' },
  { text: 'rahat yürüyüş ayakkabısı', volume: '5K', competition: 'Low' }
])

const trendForecast = ref({
  direction: 'up',
  percentage: 18,
  season: 'Yaz Sezonu'
})

const aiSuggestions = ref([
  {
    id: 1,
    type: 'structure',
    title: 'Alt Kategori Önerisi',
    description: '"Profesyonel Koşu" ve "Günlük Koşu" olarak iki alt kategoriye ayırmak dönüşümü artırabilir.'
  },
  {
    id: 2,
    type: 'content',
    title: 'Açıklama Güncellemesi',
    description: 'Kategori açıklamasında "ortopedik taban" anahtar kelimesini daha sık kullanın.'
  }
])

</script>

<template>
  <div class="space-y-4">
    <!-- SEO Score Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
      <div class="flex justify-between items-center mb-2">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori SEO Skoru</h4>
        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase" :class="seoLevel.bg + ' ' + seoLevel.color">
          {{ seoLevel.label }}
        </span>
      </div>
      
      <div class="flex items-end gap-2 mb-2">
        <span class="text-3xl font-black text-slate-800">{{ seoScore }}</span>
        <span class="text-sm text-slate-400 mb-1">/ 100</span>
      </div>

      <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
        <div class="h-full transition-all duration-500" :class="seoLevel.bar" :style="{ width: seoScore + '%' }"></div>
      </div>
      
      <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
        <span class="font-bold text-slate-700">{{ searchVolume }}</span>
        <span>Aylık Arama Hacmi</span>
      </div>
    </div>

    <!-- Trend Forecast -->
    <div class="bg-gradient-to-br from-indigo-600 to-blue-600 rounded-xl p-4 text-white shadow-lg">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-indigo-200 text-xs font-bold uppercase">Trend Analizi</p>
          <h3 class="text-xl font-bold mt-1">{{ trendForecast.season }}</h3>
        </div>
        <div class="bg-white/20 px-2 py-1 rounded text-xs font-bold backdrop-blur-sm flex items-center gap-1">
          <span v-if="trendForecast.direction === 'up'">📈</span>
          <span v-else>📉</span>
          %{{ trendForecast.percentage }}
        </div>
      </div>
      <p class="text-xs text-indigo-100 mt-2 leading-relaxed">
        Bu kategori önümüzdeki 3 ay boyunca yükseliş trendinde olacak. Stokları artırmanız önerilir.
      </p>
    </div>

    <!-- Keywords -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
      <div class="p-3 bg-slate-50 border-b border-slate-200">
        <h4 class="font-bold text-slate-800 text-sm">🔑 Popüler Anahtar Kelimeler</h4>
      </div>
      <div class="divide-y divide-slate-100">
        <div v-for="(kw, index) in keywords" :key="index" class="p-3 flex justify-between items-center">
          <span class="text-sm text-slate-700 font-medium">{{ kw.text }}</span>
          <div class="text-right">
            <div class="text-xs font-bold text-slate-900">{{ kw.volume }}</div>
            <div class="text-[10px] text-slate-400">{{ kw.competition }} Rekabet</div>
          </div>
        </div>
      </div>
    </div>

    <!-- AI Suggestions -->
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
      <h4 class="text-indigo-900 font-bold text-sm mb-3 flex items-center gap-2">
        <span>💡</span> Yapısal Öneriler
      </h4>
      <div class="space-y-3">
        <div v-for="suggestion in aiSuggestions" :key="suggestion.id" class="flex gap-3 items-start">
          <span class="text-lg mt-0.5">✨</span>
          <div>
            <h5 class="text-xs font-bold text-indigo-800">{{ suggestion.title }}</h5>
            <p class="text-xs text-indigo-700 leading-relaxed mt-0.5">{{ suggestion.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
