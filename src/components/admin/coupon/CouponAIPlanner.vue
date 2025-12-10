<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  code: string
  discount: number
  type: 'percentage' | 'fixed'
  targetSegment: string
}>()

const impactScore = ref(85) // 0-100

const impactLevel = computed(() => {
  if (impactScore.value >= 80) return { label: 'Yüksek Etki', color: 'text-emerald-600', bg: 'bg-emerald-50' }
  if (impactScore.value >= 50) return { label: 'Orta Etki', color: 'text-blue-600', bg: 'bg-blue-50' }
  return { label: 'Düşük Etki', color: 'text-slate-600', bg: 'bg-slate-50' }
})

const predictions = ref([
  { label: 'Tahmini Kullanım', value: '1,250', change: '+15%', trend: 'up' },
  { label: 'Ekstra Ciro', value: '₺450.000', change: '+22%', trend: 'up' },
  { label: 'Maliyet', value: '₺45.000', change: '-5%', trend: 'down' } // Lower cost is good
])

const smartSegments = ref([
  { id: 1, name: 'Sepeti Terk Edenler', match: 95, reason: 'Bu segment fiyat duyarlı.' },
  { id: 2, name: 'Sadık Müşteriler', match: 80, reason: 'Ödüllendirme için uygun.' },
  { id: 3, name: 'Yeni Üyeler', match: 60, reason: 'İlk sipariş teşviği olabilir.' }
])

const optimizationTips = ref([
  'Kupon süresini 48 saat ile sınırlamak aciliyet hissi yaratabilir.',
  'Minimum sepet tutarını ₺500 yerine ₺750 yapmak kârlılığı artırır.'
])

</script>

<template>
  <div class="space-y-4">
    <!-- Impact Score -->
    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl p-4 text-white shadow-lg relative overflow-hidden">
      <div class="absolute top-0 right-0 p-4 opacity-10">
        <span class="text-6xl">🚀</span>
      </div>
      <div class="relative z-10">
        <div class="flex justify-between items-start mb-2">
          <p class="text-indigo-200 text-xs font-bold uppercase tracking-wider">AI Etki Tahmini</p>
          <span class="bg-white/20 px-2 py-1 rounded text-xs font-bold backdrop-blur-sm">
            %{{ impactScore }} Başarı
          </span>
        </div>
        <h3 class="text-3xl font-bold mt-1">{{ impactLevel.label }}</h3>
        <p class="text-sm text-indigo-100 mt-2">
          Bu kuponun satışları önemli ölçüde artırması bekleniyor.
        </p>
      </div>
    </div>

    <!-- Predictions Grid -->
    <div class="grid grid-cols-3 gap-2">
      <div v-for="pred in predictions" :key="pred.label" class="bg-white p-2 rounded-xl border border-slate-200 shadow-sm text-center">
        <p class="text-[10px] text-slate-500 uppercase font-bold mb-1">{{ pred.label }}</p>
        <p class="font-bold text-slate-800 text-sm">{{ pred.value }}</p>
        <p class="text-[10px] font-bold" :class="pred.trend === 'up' ? 'text-emerald-600' : 'text-red-600'">
          {{ pred.change }}
        </p>
      </div>
    </div>

    <!-- Smart Segmentation -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
      <div class="p-3 bg-slate-50 border-b border-slate-200">
        <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
          <span>🎯</span> Hedef Kitle Önerisi
        </h4>
      </div>
      <div class="divide-y divide-slate-100">
        <div v-for="segment in smartSegments" :key="segment.id" class="p-3 hover:bg-slate-50 transition">
          <div class="flex justify-between items-center mb-1">
            <span class="font-bold text-sm text-slate-700">{{ segment.name }}</span>
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
              %{{ segment.match }} Uyum
            </span>
          </div>
          <p class="text-xs text-slate-500">{{ segment.reason }}</p>
        </div>
      </div>
    </div>

    <!-- Optimization Tips -->
    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4">
      <h4 class="text-amber-800 font-bold text-sm mb-2 flex items-center gap-2">
        <span>💡</span> Optimizasyon İpuçları
      </h4>
      <ul class="space-y-2">
        <li v-for="(tip, index) in optimizationTips" :key="index" class="flex gap-2 text-xs text-amber-700">
          <span class="font-bold">•</span>
          <span>{{ tip }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>
