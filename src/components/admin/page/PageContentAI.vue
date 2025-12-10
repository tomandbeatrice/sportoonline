<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  pageId: number
  title: string
  contentPreview: string // Short snippet of content
  slug: string
}>()

const seoScore = ref(78)
const readabilityScore = ref(92)

const seoIssues = ref([
  { type: 'warning', text: 'Meta açıklama çok kısa (120 karakter altı).' },
  { type: 'success', text: 'Anahtar kelime yoğunluğu uygun (%2.1).' },
  { type: 'error', text: 'H1 etiketi birden fazla kullanılmış.' }
])

const aiSuggestions = ref([
  {
    title: 'Daha Çekici Başlık',
    original: props.title,
    suggestion: 'En İyi ' + props.title + ' - 2024 Rehberi',
    reason: 'Tıklama oranını artırmak için yıl ve "Rehber" kelimesi eklendi.'
  },
  {
    title: 'Paragraf Düzenlemesi',
    suggestion: 'Giriş paragrafı çok uzun. Okuyucuyu sıkmamak için 3 cümleye bölünmesi önerilir.',
    reason: 'Mobil okunabilirlik için kısa paragraflar önemlidir.'
  }
])

const generateContent = () => {
  // Mock action
  alert('AI içerik oluşturma modülü başlatılıyor...')
}
</script>

<template>
  <div class="space-y-4">
    <!-- Scores -->
    <div class="grid grid-cols-2 gap-3">
      <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-sm text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
        <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">SEO Skoru</p>
        <div class="text-3xl font-black text-emerald-600">{{ seoScore }}</div>
        <p class="text-[10px] text-slate-400">Arama Motoru Dostu</p>
      </div>
      <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-sm text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
        <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">Okunabilirlik</p>
        <div class="text-3xl font-black text-blue-600">{{ readabilityScore }}</div>
        <p class="text-[10px] text-slate-400">Kullanıcı Deneyimi</p>
      </div>
    </div>

    <!-- SEO Analysis -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
      <h4 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
        <span>🔍</span> SEO Analizi
      </h4>
      <div class="space-y-2">
        <div v-for="(issue, idx) in seoIssues" :key="idx" class="flex gap-2 items-start text-xs">
          <span v-if="issue.type === 'success'" class="text-emerald-500">✅</span>
          <span v-if="issue.type === 'warning'" class="text-orange-500">⚠️</span>
          <span v-if="issue.type === 'error'" class="text-red-500">❌</span>
          <span class="text-slate-600">{{ issue.text }}</span>
        </div>
      </div>
    </div>

    <!-- AI Suggestions -->
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
      <div class="flex justify-between items-center mb-3">
        <h4 class="text-indigo-900 font-bold text-sm flex items-center gap-2">
          <span>✨</span> İçerik Önerileri
        </h4>
        <button @click="generateContent" class="text-[10px] bg-indigo-600 text-white px-2 py-1 rounded hover:bg-indigo-700 transition">
          Otomatik Düzelt
        </button>
      </div>
      
      <div class="space-y-3">
        <div v-for="(sugg, idx) in aiSuggestions" :key="idx" class="bg-white/60 p-2 rounded-lg border border-indigo-100">
          <div class="text-xs font-bold text-indigo-800 mb-1">{{ sugg.title }}</div>
          <div class="text-xs text-slate-600 italic mb-1">"{{ sugg.suggestion }}"</div>
          <div class="text-[10px] text-indigo-500 font-medium">💡 {{ sugg.reason }}</div>
        </div>
      </div>
    </div>

    <!-- Keyword Cloud (Mock) -->
    <div class="bg-slate-800 rounded-xl p-4 text-white shadow-lg">
      <h4 class="font-bold text-sm mb-3">Anahtar Kelimeler</h4>
      <div class="flex flex-wrap gap-2">
        <span class="bg-white/10 px-2 py-1 rounded text-xs">spor ayakkabı (4.2%)</span>
        <span class="bg-white/10 px-2 py-1 rounded text-xs">koşu (2.1%)</span>
        <span class="bg-white/10 px-2 py-1 rounded text-xs">indirim (1.8%)</span>
        <span class="bg-white/5 px-2 py-1 rounded text-xs text-slate-400">rahatlık (0.5%)</span>
      </div>
    </div>
  </div>
</template>
