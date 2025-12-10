<template>
  <div class="space-y-6">
    <!-- AI Analysis Header -->
    <div class="rounded-xl border border-green-100 bg-green-50/50 p-4">
      <div class="flex items-start gap-3">
        <div class="rounded-lg bg-green-100 p-2 text-green-600">
          <i class="fas fa-robot text-lg"></i>
        </div>
        <div>
          <h3 class="font-semibold text-green-900">Transfer Analizi</h3>
          <p class="text-sm text-green-700/80">
            Güzergah verimliliği ve sürücü performansı yapay zeka ile değerlendirildi.
          </p>
        </div>
      </div>
    </div>

    <!-- Efficiency Score -->
    <div class="grid gap-4 sm:grid-cols-2">
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Verimlilik Skoru</span>
          <i class="fas fa-tachometer-alt text-emerald-500" v-if="efficiencyScore > 80"></i>
          <i class="fas fa-tachometer-alt text-amber-500" v-else-if="efficiencyScore > 50"></i>
          <i class="fas fa-tachometer-alt text-red-500" v-else></i>
        </div>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-slate-900">{{ efficiencyScore }}</span>
          <span class="mb-1 text-sm font-medium text-slate-400">/ 100</span>
        </div>
        <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
          <div
            class="h-full rounded-full transition-all duration-1000"
            :class="scoreColorClass"
            :style="{ width: `${efficiencyScore}%` }"
          ></div>
        </div>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tahmini Varış</span>
          <i class="fas fa-clock text-blue-500"></i>
        </div>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-slate-900">{{ estimatedDuration }}</span>
          <span class="mb-1 text-sm font-medium text-slate-400">dk</span>
        </div>
        <p class="mt-2 text-xs text-slate-500">Trafik yoğunluğu: {{ trafficStatus }}</p>
      </div>
    </div>

    <!-- AI Insights -->
    <div class="space-y-3">
      <h4 class="text-sm font-semibold text-slate-900">Tespitler & Öneriler</h4>
      
      <div v-for="(insight, index) in insights" :key="index" 
        class="flex items-start gap-3 rounded-lg border p-3 text-sm transition-colors"
        :class="insight.variant === 'positive' ? 'border-emerald-100 bg-emerald-50/50 text-emerald-900' : 
                insight.variant === 'warning' ? 'border-amber-100 bg-amber-50/50 text-amber-900' : 
                'border-red-100 bg-red-50/50 text-red-900'"
      >
        <i class="fas mt-0.5" :class="insight.icon"></i>
        <span>{{ insight.text }}</span>
      </div>
    </div>

    <!-- Smart Actions -->
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
      <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Akıllı İşlemler</h4>
      <div class="flex flex-col gap-2">
        <button 
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-white border border-slate-300 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
          @click="$emit('action', 'optimize_route')"
        >
          <i class="fas fa-route text-blue-500"></i>
          Alternatif Rota Bul
        </button>
        
        <button 
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-white border border-slate-300 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
          @click="$emit('action', 'contact_driver')"
        >
          <i class="fas fa-phone text-green-500"></i>
          Sürücüyle İletişime Geç
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  transfer: {
    type: Object,
    required: true
  }
});

defineEmits(['action']);

// Mock AI Logic
const efficiencyScore = computed(() => {
  if (!props.transfer) return 0;
  // Mock score based on status
  return props.transfer.status === 'completed' ? 95 : 
         props.transfer.status === 'active' ? 75 : 60;
});

const estimatedDuration = computed(() => {
  return Math.floor(Math.random() * 45) + 15; // 15-60 min
});

const trafficStatus = computed(() => {
  const statuses = ['Düşük', 'Orta', 'Yüksek'];
  return statuses[Math.floor(Math.random() * statuses.length)];
});

const scoreColorClass = computed(() => {
  if (efficiencyScore.value >= 80) return 'bg-emerald-500';
  if (efficiencyScore.value >= 60) return 'bg-amber-500';
  return 'bg-red-500';
});

const insights = computed(() => {
  const list = [];
  
  if (props.transfer.status === 'active') {
    list.push({
      variant: 'positive',
      icon: 'fa-check-circle',
      text: 'Sürücü rotaya sadık kalıyor, tahmini varış süresi güncel.'
    });
    
    if (trafficStatus.value === 'Yüksek') {
      list.push({
        variant: 'warning',
        icon: 'fa-traffic-light',
        text: 'Güzergah üzerinde yoğun trafik tespit edildi (+15dk gecikme riski).'
      });
    }
  } else if (props.transfer.status === 'pending') {
    list.push({
      variant: 'warning',
      icon: 'fa-clock',
      text: 'Talep yoğunluğu nedeniyle araç ataması gecikebilir.'
    });
  } else {
    list.push({
      variant: 'positive',
      icon: 'fa-star',
      text: 'Transfer başarıyla tamamlandı. Müşteri puanı: 5/5'
    });
  }
  
  return list;
});
</script>
