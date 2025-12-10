<template>
  <div class="space-y-6">
    <!-- AI Header -->
    <div class="rounded-xl bg-gradient-to-r from-orange-600 to-red-600 p-6 text-white shadow-lg">
      <div class="flex items-start justify-between">
        <div>
          <div class="flex items-center gap-2">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
              <i class="fas fa-robot text-sm"></i>
            </div>
            <h2 class="text-lg font-bold">Filo Zekası</h2>
          </div>
          <p class="mt-1 text-sm text-orange-100">
            {{ car ? car.model : 'Genel' }} için kullanım ve bakım analizi
          </p>
        </div>
        <div class="rounded-lg bg-white/10 px-3 py-1 text-xs font-medium backdrop-blur-sm">
          Canlı Analiz
        </div>
      </div>

      <!-- Key Metrics -->
      <div class="mt-6 grid grid-cols-2 gap-4">
        <div class="rounded-lg bg-white/10 p-3 backdrop-blur-sm">
          <div class="text-xs text-orange-100">Kullanım Tahmini</div>
          <div class="mt-1 flex items-end gap-2">
            <span class="text-2xl font-bold">{{ aiMetrics.utilizationForecast }}%</span>
            <span class="mb-1 text-xs text-emerald-300">
              <i class="fas fa-arrow-up"></i> %10
            </span>
          </div>
        </div>
        <div class="rounded-lg bg-white/10 p-3 backdrop-blur-sm">
          <div class="text-xs text-orange-100">Bakım Riski</div>
          <div class="mt-1 flex items-end gap-2">
            <span class="text-2xl font-bold">{{ aiMetrics.maintenanceRisk }}%</span>
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
                class="mt-2 text-xs font-medium text-orange-600 hover:text-orange-700"
              >
                {{ insight.action }} →
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Vehicle Health (Mock) -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
      <h3 class="mb-3 text-sm font-semibold text-slate-900">Araç Sağlığı</h3>
      <div class="grid grid-cols-2 gap-3">
        <div v-for="(status, index) in vehicleHealth" :key="index" class="rounded-lg bg-slate-50 p-3">
          <div class="mb-1 text-xs text-slate-500">{{ status.label }}</div>
          <div class="flex items-center justify-between">
            <span class="font-semibold text-slate-900">{{ status.value }}</span>
            <i class="fas fa-check-circle text-emerald-500" v-if="status.healthy"></i>
            <i class="fas fa-exclamation-circle text-amber-500" v-else></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Demand Forecast Chart (Mock) -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-900">Talep Tahmini</h3>
        <select class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs">
          <option>Bu Hafta</option>
          <option>Gelecek Hafta</option>
        </select>
      </div>
      <div class="flex h-32 items-end justify-between gap-1">
        <div 
          v-for="(day, i) in demandData" 
          :key="i"
          class="w-full rounded-t bg-orange-100 transition-all hover:bg-orange-200"
          :style="{ height: day.value + '%' }"
        ></div>
      </div>
      <div class="mt-2 flex justify-between text-[10px] text-slate-400">
        <span>Pzt</span>
        <span>Çar</span>
        <span>Cum</span>
        <span>Paz</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  car: {
    type: Object,
    default: null
  }
});

const aiMetrics = ref({
  utilizationForecast: 85,
  maintenanceRisk: 12
});

const insights = ref([
  {
    title: 'Yüksek Talep',
    description: 'Bu araç sınıfı için hafta sonu talebinde %25 artış bekleniyor.',
    icon: 'fa-chart-line',
    bgClass: 'bg-emerald-100',
    textClass: 'text-emerald-600',
    action: 'Fiyatı Optimize Et',
    actionType: 'optimize_price'
  },
  {
    title: 'Servis Zamanı',
    description: 'Tahmini 500 km sonra periyodik bakım gerekiyor.',
    icon: 'fa-wrench',
    bgClass: 'bg-amber-100',
    textClass: 'text-amber-600',
    action: 'Servis Randevusu',
    actionType: 'schedule_service'
  }
]);

const vehicleHealth = [
  { label: 'Motor Durumu', value: '%98', healthy: true },
  { label: 'Lastik Basıncı', value: 'Normal', healthy: true },
  { label: 'Yağ Seviyesi', value: '%85', healthy: true },
  { label: 'Fren Balatası', value: 'İyi', healthy: true },
];

const demandData = Array.from({ length: 7 }, () => ({
  value: Math.floor(Math.random() * 60) + 30
}));

// Watch for car changes to update mock data
watch(() => props.car, (newCar) => {
  if (newCar) {
    // Simulate data update based on car
    aiMetrics.value.utilizationForecast = Math.floor(Math.random() * 30) + 60;
    aiMetrics.value.maintenanceRisk = Math.floor(Math.random() * 20);
  }
});
</script>
