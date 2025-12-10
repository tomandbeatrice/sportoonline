<script setup lang="ts">
import { ref, computed, watch } from 'vue'

const props = defineProps<{
  colors: {
    primary: string
    secondary: string
    accent: string
    background: string
    text: string
  }
}>()

// Mock Accessibility Score Calculation
const accessibilityScore = computed(() => {
  // In a real app, we would calculate contrast ratio here
  // For now, we simulate a score based on inputs
  return 88
})

const moodAnalysis = ref({
  mood: 'Güvenilir & Kurumsal',
  description: 'Mavi tonlarının ağırlıklı olduğu bu palet, kullanıcıda güven ve profesyonellik hissi uyandırıyor. Finans ve teknoloji siteleri için uygundur.'
})

const suggestions = ref([
  {
    type: 'contrast',
    title: 'Kontrast Uyarısı',
    message: 'İkincil renk ile arka plan arasındaki kontrast oranı (3.2:1) biraz düşük. WCAG AA standardı için en az 4.5:1 olmalı.',
    fix: 'İkincil rengi biraz daha koyulaştırın (#1e40af).'
  },
  {
    type: 'harmony',
    title: 'Renk Uyumu',
    message: 'Vurgu renginiz (Accent) ana renklerle tamalayıcı (complementary) bir ilişki içinde. Bu, harekete geçirici butonlar için harika.',
    fix: null
  }
])

const generatePalette = () => {
  alert('AI yeni bir renk paleti oluşturuyor...')
}

const getScoreColor = (score: number) => {
  if (score >= 90) return 'text-emerald-600'
  if (score >= 70) return 'text-blue-600'
  return 'text-orange-600'
}
</script>

<template>
  <div class="space-y-4">
    <!-- Accessibility Score -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
      <div>
        <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">Erişilebilirlik Skoru (WCAG)</p>
        <div class="text-3xl font-black" :class="getScoreColor(accessibilityScore)">{{ accessibilityScore }}/100</div>
      </div>
      <div class="text-right">
        <div class="text-xs font-bold text-slate-700">AA Standardı</div>
        <div class="text-[10px] text-emerald-600 font-bold">✅ Geçerli</div>
      </div>
    </div>

    <!-- Mood Analysis -->
    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl p-4 text-white shadow-md">
      <h4 class="font-bold text-sm mb-2 flex items-center gap-2">
        <span>🧠</span> AI Duygu Analizi
      </h4>
      <div class="text-lg font-bold mb-1">{{ moodAnalysis.mood }}</div>
      <p class="text-xs opacity-90 leading-relaxed">{{ moodAnalysis.description }}</p>
    </div>

    <!-- Suggestions -->
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
      <div class="flex justify-between items-center mb-3">
        <h4 class="text-slate-800 font-bold text-sm flex items-center gap-2">
          <span>💡</span> İyileştirme Önerileri
        </h4>
        <button @click="generatePalette" class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200 transition font-bold">
          ✨ Otomatik Palet
        </button>
      </div>
      
      <div class="space-y-3">
        <div v-for="(sugg, idx) in suggestions" :key="idx" class="flex gap-3 items-start">
          <span v-if="sugg.type === 'contrast'" class="text-lg mt-0.5">👁️</span>
          <span v-else class="text-lg mt-0.5">🎨</span>
          <div>
            <h5 class="text-xs font-bold text-slate-800">{{ sugg.title }}</h5>
            <p class="text-xs text-slate-600 leading-relaxed mt-0.5">{{ sugg.message }}</p>
            <button v-if="sugg.fix" class="mt-1.5 text-[10px] text-indigo-600 font-bold hover:underline">
              Otomatik Düzelt: {{ sugg.fix }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Color Blindness Simulation (Mock) -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
      <h4 class="font-bold text-slate-800 text-sm mb-3">Renk Körlüğü Simülasyonu</h4>
      <div class="grid grid-cols-3 gap-2">
        <div class="space-y-1">
          <div class="h-8 bg-slate-200 rounded"></div>
          <div class="text-[10px] text-center text-slate-500">Protanopia</div>
        </div>
        <div class="space-y-1">
          <div class="h-8 bg-slate-200 rounded"></div>
          <div class="text-[10px] text-center text-slate-500">Deuteranopia</div>
        </div>
        <div class="space-y-1">
          <div class="h-8 bg-slate-200 rounded"></div>
          <div class="text-[10px] text-center text-slate-500">Tritanopia</div>
        </div>
      </div>
    </div>
  </div>
</template>
