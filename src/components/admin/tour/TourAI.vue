<template>
  <div class="space-y-6">
    <!-- AI Header -->
    <div class="rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 p-6 text-white shadow-lg">
      <div class="flex items-start justify-between">
        <div>
          <div class="flex items-center gap-2">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
              <i class="fas fa-robot text-sm"></i>
            </div>
            <h2 class="text-lg font-bold">Tur Zekası</h2>
          </div>
          <p class="mt-1 text-sm text-sky-100">
            {{ tour ? tour.name : 'Genel' }} için talep ve kapasite analizi
          </p>
        </div>
        <div class="rounded-lg bg-white/10 px-3 py-1 text-xs font-medium backdrop-blur-sm">
          Canlı Analiz
        </div>
      </div>

      <!-- Key Metrics -->
      <div class="mt-6 grid grid-cols-2 gap-4">
        <div class="rounded-lg bg-white/10 p-3 backdrop-blur-sm">
          <div class="text-xs text-sky-100">Doluluk Tahmini</div>
          <div class="mt-1 flex items-end gap-2">
            <span class="text-2xl font-bold">{{ aiMetrics.occupancyForecast }}%</span>
            <span class="mb-1 text-xs text-emerald-300">
              <i class="fas fa-arrow-up"></i> %8
            </span>
          </div>
        </div>
        <div class="rounded-lg bg-white/10 p-3 backdrop-blur-sm">
          <div class="text-xs text-sky-100">Popülarite Skoru</div>
          <div class="mt-1 flex items-end gap-2">
            <span class="text-2xl font-bold">{{ aiMetrics.popularityScore }}/10</span>
            <span class="mb-1 text-xs text-emerald-300">
              <i class="fas fa-arrow-up"></i> 0.4
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
                class="mt-2 text-xs font-medium text-sky-600 hover:text-sky-700"
              >
                {{ insight.action }} →
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Weather Impact (Mock) -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
      <h3 class="mb-3 text-sm font-semibold text-slate-900">Hava Durumu Etkisi</h3>
      <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3">
        <div class="flex items-center gap-3">
          <div class="text-2xl text-amber-500"><i class="fas fa-sun"></i></div>
          <div>
            <p class="text-xs font-medium text-slate-900">Güneşli</p>
            <p class="text-[10px] text-slate-500">Tur için ideal koşullar</p>
          </div>
        </div>
        <span class="rounded bg-emerald-100 px-2 py-1 text-xs font-bold text-emerald-700">Pozitif</span>
      </div>
    </div>

    <!-- Demand Trend Chart (Mock) -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-900">Talep Trendi</h3>
        <select class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs">
          <option>Son 30 Gün</option>
          <option>Gelecek 30 Gün</option>
        </select>
      </div>
      <div class="flex h-32 items-end justify-between gap-1">
        <div 
          v-for="(day, i) in demandData" 
          :key="i"
          class="w-full rounded-t bg-sky-100 transition-all hover:bg-sky-200"
          :style="{ height: day.value + '%' }"
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  tour: {
    type: Object,
    default: null
  }
});

const aiMetrics = ref({
  occupancyForecast: 85,
  popularityScore: 9.2
});

const insights = ref([
  {
    title: 'Kapasite Uyarısı',
    description: 'Bu tur için önümüzdeki hafta sonu kontenjan dolmak üzere.',
    icon: 'fa-users',
    bgClass: 'bg-amber-100',
    textClass: 'text-amber-600',
    action: 'Ek Kontenjan Aç',
    actionType: 'increase_capacity'
  },
  {
    title: 'Fiyat Fırsatı',
    description: 'Benzer turlara göre fiyatınız %10 düşük, artış yapılabilir.',
    icon: 'fa-tag',
    bgClass: 'bg-emerald-100',
    textClass: 'text-emerald-600',
    action: 'Fiyatı Güncelle',
    actionType: 'update_price'
  }
]);

const demandData = Array.from({ length: 15 }, () => ({
  value: Math.floor(Math.random() * 60) + 30
}));

// Watch for tour changes to update mock data
watch(() => props.tour, (newTour) => {
  if (newTour) {
    // Simulate data update based on tour
    aiMetrics.value.occupancyForecast = Math.floor(Math.random() * 20) + 70;
    aiMetrics.value.popularityScore = (Math.random() * 2 + 8).toFixed(1);
  }
});
</script>
