<template>
  <div class="space-y-6">
    <!-- AI Analysis Header -->
    <div class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-4">
      <div class="flex items-start gap-3">
        <div class="rounded-lg bg-indigo-100 p-2 text-indigo-600">
          <i class="fas fa-robot text-lg"></i>
        </div>
        <div>
          <h3 class="font-semibold text-indigo-900">Export Analizi</h3>
          <p class="text-sm text-indigo-700/80">
            Dosya boyutu, işlem süresi ve veri bütünlüğü yapay zeka tarafından kontrol edildi.
          </p>
        </div>
      </div>
    </div>

    <!-- Health & Performance -->
    <div class="grid gap-4 sm:grid-cols-2">
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Veri Bütünlüğü</span>
          <i class="fas fa-check-circle text-emerald-500" v-if="integrityScore > 90"></i>
          <i class="fas fa-exclamation-triangle text-amber-500" v-else></i>
        </div>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-slate-900">{{ integrityScore }}%</span>
        </div>
        <p class="mt-2 text-xs text-slate-500">Satır sayısı ve veri tipleri tutarlı.</p>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tahmini Süre</span>
          <i class="fas fa-clock text-blue-500"></i>
        </div>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-slate-900">{{ estimatedTime }}</span>
          <span class="mb-1 text-sm font-medium text-slate-400">sn</span>
        </div>
        <p class="mt-2 text-xs text-slate-500">Benzer işlemlere göre hesaplandı.</p>
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
          @click="$emit('action', 'optimize')"
        >
          <i class="fas fa-magic text-indigo-500"></i>
          Sorguyu Optimize Et
        </button>
        
        <button 
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-white border border-slate-300 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
          @click="$emit('action', 'schedule')"
        >
          <i class="fas fa-calendar-alt text-blue-500"></i>
          Gece Çalışması Planla
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  exportFile: {
    type: Object,
    required: true
  }
});

defineEmits(['action']);

// Mock AI Logic
const integrityScore = computed(() => {
  if (!props.exportFile) return 0;
  // Mock: Completed files have high integrity, failed ones low
  return props.exportFile.status === 'completed' ? 98 : 45;
});

const estimatedTime = computed(() => {
  if (!props.exportFile) return 0;
  // Mock: Random time based on size
  return Math.floor(Math.random() * 120) + 10;
});

const insights = computed(() => {
  const list = [];
  
  if (props.exportFile.status === 'completed') {
    list.push({
      variant: 'positive',
      icon: 'fa-check-circle',
      text: 'Dosya başarıyla oluşturuldu ve virüs taramasından geçti.'
    });
    
    if (props.exportFile.size.includes('MB')) {
       list.push({
        variant: 'warning',
        icon: 'fa-compress-arrows-alt',
        text: 'Dosya boyutu büyük. Sıkıştırma (zip) kullanılması önerilir.'
      });
    }
  } else if (props.exportFile.status === 'failed') {
    list.push({
      variant: 'negative',
      icon: 'fa-times-circle',
      text: 'Zaman aşımı hatası tespit edildi. Sorgu süresi 300sn sınırını aştı.'
    });
  } else {
    list.push({
      variant: 'warning',
      icon: 'fa-hourglass-half',
      text: 'İşlem devam ediyor. Sunucu yükü normal seviyede.'
    });
  }
  
  return list;
});
</script>
