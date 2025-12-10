<script setup lang="ts">
import { ref, onMounted } from 'vue'

const securityScore = ref(85)
const performanceScore = ref(92)

const securityIssues = ref([
  { type: 'warning', text: '2FA zorunluluğu adminler için kapalı.' },
  { type: 'success', text: 'SSL sertifikası geçerli (280 gün kaldı).' },
  { type: 'error', text: 'Debug modu canlı ortamda açık!' }
])

const performanceMetrics = ref([
  { label: 'Önbellek Oranı', value: '88%', status: 'good' },
  { label: 'DB Sorgu Süresi', value: '12ms', status: 'good' },
  { label: 'Sunucu Yükü', value: '45%', status: 'normal' }
])

const aiSuggestions = ref([
  {
    id: 1,
    title: 'Veritabanı Optimizasyonu',
    description: 'Son 24 saatte yavaş çalışan 3 sorgu tespit edildi. İndeksleme öneriliyor.',
    action: 'İndeksleri Uygula'
  },
  {
    id: 2,
    title: 'Resim Sıkıştırma',
    description: 'Yüklenen ürün görselleri için otomatik WebP dönüşümü aktif değil.',
    action: 'Aktifleştir'
  }
])

const runAudit = () => {
  alert('Sistem denetimi başlatılıyor...')
}
</script>

<template>
  <div class="space-y-4">
    <!-- Scores -->
    <div class="grid grid-cols-2 gap-3">
      <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-sm text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
        <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">Güvenlik Skoru</p>
        <div class="text-3xl font-black text-emerald-600">{{ securityScore }}</div>
        <p class="text-[10px] text-slate-400">Risk Seviyesi: Düşük</p>
      </div>
      <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-sm text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
        <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">Performans</p>
        <div class="text-3xl font-black text-blue-600">{{ performanceScore }}</div>
        <p class="text-[10px] text-slate-400">Sistem Sağlığı: İyi</p>
      </div>
    </div>

    <!-- Security Alerts -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
      <h4 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
        <span>🛡️</span> Güvenlik Denetimi
      </h4>
      <div class="space-y-2">
        <div v-for="(issue, idx) in securityIssues" :key="idx" class="flex gap-2 items-start text-xs">
          <span v-if="issue.type === 'success'" class="text-emerald-500">✅</span>
          <span v-if="issue.type === 'warning'" class="text-orange-500">⚠️</span>
          <span v-if="issue.type === 'error'" class="text-red-500">❌</span>
          <span class="text-slate-600">{{ issue.text }}</span>
        </div>
      </div>
    </div>

    <!-- Performance Metrics -->
    <div class="bg-slate-800 rounded-xl p-4 text-white shadow-lg">
      <h4 class="font-bold text-sm mb-3 flex items-center gap-2">
        <span>⚡</span> Canlı Metrikler
      </h4>
      <div class="space-y-3">
        <div v-for="(metric, idx) in performanceMetrics" :key="idx" class="flex justify-between items-center border-b border-white/10 pb-2 last:border-0 last:pb-0">
          <span class="text-xs text-slate-300">{{ metric.label }}</span>
          <span class="text-sm font-bold font-mono text-emerald-300">{{ metric.value }}</span>
        </div>
      </div>
    </div>

    <!-- AI Suggestions -->
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
      <div class="flex justify-between items-center mb-3">
        <h4 class="text-indigo-900 font-bold text-sm flex items-center gap-2">
          <span>🤖</span> AI Önerileri
        </h4>
        <button @click="runAudit" class="text-[10px] bg-indigo-600 text-white px-2 py-1 rounded hover:bg-indigo-700 transition">
          Denetle
        </button>
      </div>
      
      <div class="space-y-3">
        <div v-for="sugg in aiSuggestions" :key="sugg.id" class="bg-white/60 p-3 rounded-lg border border-indigo-100">
          <div class="text-xs font-bold text-indigo-800 mb-1">{{ sugg.title }}</div>
          <p class="text-xs text-slate-600 mb-2">{{ sugg.description }}</p>
          <button class="w-full py-1 bg-indigo-100 text-indigo-700 rounded text-[10px] font-bold hover:bg-indigo-200 transition">
            {{ sugg.action }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
