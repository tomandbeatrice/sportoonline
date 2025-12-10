<template>
  <div class="space-y-6">
    <!-- AI Header -->
    <div class="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 p-6 text-white shadow-lg">
      <div class="flex items-start justify-between">
        <div>
          <div class="flex items-center gap-2">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
              <i class="fas fa-robot text-sm"></i>
            </div>
            <h2 class="text-lg font-bold">Otel Zekası</h2>
          </div>
          <p class="mt-1 text-sm text-indigo-100">
            {{ hotel ? hotel.name : 'Genel' }} için doluluk ve fiyat analizi
          </p>
        </div>
        <div class="rounded-lg bg-white/10 px-3 py-1 text-xs font-medium backdrop-blur-sm">
          Canlı Analiz
        </div>
      </div>

      <!-- Key Metrics -->
      <div class="mt-6 grid grid-cols-2 gap-4">
        <div class="rounded-lg bg-white/10 p-3 backdrop-blur-sm">
          <div class="text-xs text-indigo-100">Doluluk Tahmini</div>
          <div class="mt-1 flex items-end gap-2">
            <span class="text-2xl font-bold">{{ aiMetrics.occupancyForecast }}%</span>
            <span class="mb-1 text-xs text-emerald-300">
              <i class="fas fa-arrow-up"></i> %5
            </span>
          </div>
        </div>
        <div class="rounded-lg bg-white/10 p-3 backdrop-blur-sm">
          <div class="text-xs text-indigo-100">Önerilen Fiyat</div>
          <div class="mt-1 flex items-end gap-2">
            <span class="text-2xl font-bold">{{ aiMetrics.suggestedPrice }}₺</span>
            <span class="mb-1 text-xs text-emerald-300">
              <i class="fas fa-arrow-up"></i> %12
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Smart Insights -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-100 px-4 py-3">
        <h3 class="text-sm font-semibold text-slate-900">Akıllı Öngörüler</h3>
      </div>
      <div class="divide-y divide-slate-100">
        <div v-for="(insight, index) in insights" :key="index" class="p-4">
          <div class="flex gap-3">
            <div 
              class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
              :class="insight.bgClass"
            >
              <i class="fas" :class="[insight.icon, insight.textClass]"></i>
            </div>
            <div>
              <h4 class="text-sm font-medium text-slate-900">{{ insight.title }}</h4>
              <p class="mt-1 text-xs text-slate-500">{{ insight.description }}</p>
              <button 
                v-if="insight.action"
                @click="$emit('action', insight.actionType)"
                class="mt-2 text-xs font-medium text-indigo-600 hover:text-indigo-700"
              >
                {{ insight.action }} →
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Demand Forecast Chart (Mock) -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-900">Talep Tahmini (7 Gün)</h3>
        <select class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs">
          <option>Bu Hafta</option>
          <option>Gelecek Hafta</option>
        </select>
      </div>
      <div class="flex h-32 items-end justify-between gap-2">
        <div 
          v-for="(day, i) in demandData" 
          :key="i"
          class="group relative w-full rounded-t-lg bg-indigo-100 transition-all hover:bg-indigo-200"
          :style="{ height: day.value + '%' }"
        >
          <div class="absolute -top-8 left-1/2 hidden -translate-x-1/2 rounded bg-slate-800 px-2 py-1 text-xs text-white group-hover:block">
            %{{ day.value }}
          </div>
          <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[10px] text-slate-500">
            {{ day.label }}
          </div>
        </div>
      </div>
    </div>

    <!-- Guest Sentiment -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
      <h3 class="mb-3 text-sm font-semibold text-slate-900">Misafir Duygusu</h3>
      <div class="space-y-3">
        <div v-for="(sentiment, index) in sentiments" :key="index">
          <div class="mb-1 flex justify-between text-xs">
            <span class="font-medium text-slate-700">{{ sentiment.label }}</span>
            <span class="text-slate-500">%{{ sentiment.score }}</span>
          </div>
          <div class="h-1.5 w-full rounded-full bg-slate-100">
            <div 
              class="h-1.5 rounded-full transition-all"
              :class="sentiment.colorClass"
              :style="{ width: sentiment.score + '%' }"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  hotel: {
    type: Object,
    default: null
  }
});

const aiMetrics = ref({
  occupancyForecast: 78,
  suggestedPrice: 1450
});

const insights = ref([
  {
    title: 'Yüksek Talep Beklentisi',
    description: 'Önümüzdeki hafta sonu bölgedeki festival nedeniyle doluluk %90\'ı aşabilir.',
    icon: 'fa-chart-line',
    bgClass: 'bg-emerald-100',
    textClass: 'text-emerald-600',
    action: 'Fiyatları Optimize Et',
    actionType: 'optimize_prices'
  },
  {
    title: 'Hizmet Kalitesi Uyarısı',
    description: 'Son 3 yorumda "kahvaltı çeşitliliği" hakkında olumsuz geri bildirim tespit edildi.',
    icon: 'fa-exclamation-triangle',
    bgClass: 'bg-amber-100',
    textClass: 'text-amber-600',
    action: 'Raporu İncele',
    actionType: 'view_report'
  }
]);

const demandData = [
  { label: 'Pzt', value: 45 },
  { label: 'Sal', value: 52 },
  { label: 'Çar', value: 58 },
  { label: 'Per', value: 65 },
  { label: 'Cum', value: 85 },
  { label: 'Cmt', value: 92 },
  { label: 'Paz', value: 70 },
];

const sentiments = [
  { label: 'Temizlik', score: 92, colorClass: 'bg-emerald-500' },
  { label: 'Konum', score: 88, colorClass: 'bg-blue-500' },
  { label: 'Hizmet', score: 76, colorClass: 'bg-amber-500' },
  { label: 'Fiyat/Perf.', score: 84, colorClass: 'bg-indigo-500' },
];

// Watch for hotel changes to update mock data
watch(() => props.hotel, (newHotel) => {
  if (newHotel) {
    // Simulate data update based on hotel
    aiMetrics.value.occupancyForecast = Math.floor(Math.random() * 30) + 60;
    aiMetrics.value.suggestedPrice = newHotel.priceRange === '₺₺₺' ? 3500 : 1450;
  }
});
</script>
