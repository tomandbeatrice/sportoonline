<template>
  <div class="space-y-6">
    <!-- AI Header -->
    <div class="rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 p-6 text-white shadow-lg">
      <div class="flex items-start justify-between">
        <div>
          <div class="flex items-center gap-2">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
              <i class="fas fa-robot text-sm"></i>
            </div>
            <h2 class="text-lg font-bold">Aktivite Zekası</h2>
          </div>
          <p class="mt-1 text-sm text-rose-100">
            {{ activity ? activity.name : 'Genel' }} için katılım ve risk analizi
          </p>
        </div>
        <div class="rounded-lg bg-white/10 px-3 py-1 text-xs font-medium backdrop-blur-sm">
          Canlı Analiz
        </div>
      </div>

      <!-- Key Metrics -->
      <div class="mt-6 grid grid-cols-2 gap-4">
        <div class="rounded-lg bg-white/10 p-3 backdrop-blur-sm">
          <div class="text-xs text-rose-100">Katılım Tahmini</div>
          <div class="mt-1 flex items-end gap-2">
            <span class="text-2xl font-bold">{{ aiMetrics.participationForecast }}%</span>
            <span class="mb-1 text-xs text-emerald-300">
              <i class="fas fa-arrow-up"></i> %15
            </span>
          </div>
        </div>
        <div class="rounded-lg bg-white/10 p-3 backdrop-blur-sm">
          <div class="text-xs text-rose-100">Risk Skoru</div>
          <div class="mt-1 flex items-end gap-2">
            <span class="text-2xl font-bold">{{ aiMetrics.riskScore }}/10</span>
            <span class="mb-1 text-xs text-emerald-300">
              <i class="fas fa-arrow-down"></i> Düşük
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
                class="mt-2 text-xs font-medium text-rose-600 hover:text-rose-700"
              >
                {{ insight.action }} →
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Equipment Status (Mock) -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
      <h3 class="mb-3 text-sm font-semibold text-slate-900">Ekipman Durumu</h3>
      <div class="space-y-3">
        <div v-for="(item, index) in equipmentStatus" :key="index">
          <div class="mb-1 flex justify-between text-xs">
            <span class="font-medium text-slate-700">{{ item.label }}</span>
            <span class="text-slate-500">%{{ item.health }}</span>
          </div>
          <div class="h-1.5 w-full rounded-full bg-slate-100">
            <div 
              class="h-1.5 rounded-full transition-all"
              :class="item.health > 70 ? 'bg-emerald-500' : (item.health > 40 ? 'bg-amber-500' : 'bg-red-500')"
              :style="{ width: item.health + '%' }"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Hourly Demand Chart (Mock) -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-900">Saatlik Yoğunluk</h3>
        <div class="flex items-center gap-2">
          <span class="h-2 w-2 rounded-full bg-rose-500"></span>
          <span class="text-xs text-slate-500">Canlı</span>
        </div>
      </div>
      <div class="flex h-32 items-end justify-between gap-1">
        <div 
          v-for="(hour, i) in hourlyDemand" 
          :key="i"
          class="w-full rounded-t bg-rose-100 transition-all hover:bg-rose-200"
          :style="{ height: hour.value + '%' }"
        ></div>
      </div>
      <div class="mt-2 flex justify-between text-[10px] text-slate-400">
        <span>09:00</span>
        <span>12:00</span>
        <span>15:00</span>
        <span>18:00</span>
        <span>21:00</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  activity: {
    type: Object,
    default: null
  }
});

const aiMetrics = ref({
  participationForecast: 92,
  riskScore: 1.2
});

const insights = ref([
  {
    title: 'Yüksek İlgi',
    description: 'Bu aktivite için son 24 saatte görüntülenme sayısı %40 arttı.',
    icon: 'fa-fire',
    bgClass: 'bg-rose-100',
    textClass: 'text-rose-600',
    action: 'Öne Çıkar',
    actionType: 'promote_activity'
  },
  {
    title: 'Ekipman Bakımı',
    description: 'Kullanım sıklığına göre 3 gün içinde bakım öneriliyor.',
    icon: 'fa-tools',
    bgClass: 'bg-amber-100',
    textClass: 'text-amber-600',
    action: 'Bakım Planla',
    actionType: 'schedule_maintenance'
  }
]);

const equipmentStatus = [
  { label: 'Güvenlik Ekipmanları', health: 95 },
  { label: 'Araçlar/Gereçler', health: 82 },
  { label: 'Yedek Parçalar', health: 45 },
];

const hourlyDemand = Array.from({ length: 12 }, () => ({
  value: Math.floor(Math.random() * 70) + 20
}));

// Watch for activity changes to update mock data
watch(() => props.activity, (newActivity) => {
  if (newActivity) {
    // Simulate data update based on activity
    aiMetrics.value.participationForecast = Math.floor(Math.random() * 25) + 70;
    aiMetrics.value.riskScore = (Math.random() * 3).toFixed(1);
  }
});
</script>
