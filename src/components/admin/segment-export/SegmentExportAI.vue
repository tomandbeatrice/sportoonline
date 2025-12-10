<template>
  <div class="space-y-6">
    <!-- AI Analysis Header -->
    <div class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-4">
      <div class="flex items-start gap-3">
        <div class="rounded-lg bg-indigo-100 p-2 text-indigo-600">
          <i class="fas fa-robot text-lg"></i>
        </div>
        <div>
          <h3 class="font-semibold text-indigo-900">Segment Analizi</h3>
          <p class="text-sm text-indigo-700/80">
            Seçilen filtreler ve alanlar yapay zeka tarafından analiz edildi.
          </p>
        </div>
      </div>
    </div>

    <!-- Estimates -->
    <div class="grid gap-4 sm:grid-cols-2">
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tahmini Kayıt</span>
          <i class="fas fa-users text-blue-500"></i>
        </div>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-slate-900">{{ formattedCount }}</span>
        </div>
        <p class="mt-2 text-xs text-slate-500">Filtrelere uyan yaklaşık kişi sayısı.</p>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tahmini Boyut</span>
          <i class="fas fa-file-alt text-indigo-500"></i>
        </div>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-slate-900">{{ estimatedSize }}</span>
          <span class="mb-1 text-sm font-medium text-slate-400">MB</span>
        </div>
        <p class="mt-2 text-xs text-slate-500">Seçilen alanlara göre hesaplandı.</p>
      </div>
    </div>

    <!-- AI Insights -->
    <div class="space-y-3">
      <h4 class="text-sm font-semibold text-slate-900">Tespitler & Uyarılar</h4>
      
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
      <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Önerilen İşlemler</h4>
      <div class="flex flex-col gap-2">
        <button 
          v-if="hasSensitiveData"
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-amber-100 border border-amber-200 py-2.5 text-sm font-semibold text-amber-800 shadow-sm transition hover:bg-amber-200"
          @click="$emit('action', 'mask_pii')"
        >
          <i class="fas fa-user-secret"></i>
          Kişisel Verileri Maskele
        </button>
        
        <button 
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-white border border-slate-300 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
          @click="$emit('action', 'save_template')"
        >
          <i class="fas fa-save text-indigo-500"></i>
          Şablon Olarak Kaydet
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  config: {
    type: Object,
    required: true
  }
});

defineEmits(['action']);

// Mock AI Logic
const estimatedCount = computed(() => {
  if (!props.config.segment) return 0;
  // Mock: Random count based on segment ID
  return (props.config.segment.length * 1234) % 50000 + 500;
});

const formattedCount = computed(() => {
  return new Intl.NumberFormat('tr-TR').format(estimatedCount.value);
});

const estimatedSize = computed(() => {
  // Mock: Count * fields * constant
  const fieldCount = props.config.fields ? props.config.fields.length : 0;
  return ((estimatedCount.value * fieldCount * 0.0005) / 1024).toFixed(2);
});

const hasSensitiveData = computed(() => {
  if (!props.config.fields) return false;
  return props.config.fields.includes('email') || props.config.fields.includes('phone') || props.config.fields.includes('tckn');
});

const insights = computed(() => {
  const list = [];
  
  if (hasSensitiveData.value) {
    list.push({
      variant: 'warning',
      icon: 'fa-exclamation-triangle',
      text: 'Seçilen alanlar KVKK kapsamında hassas veri (E-posta, Telefon vb.) içeriyor.'
    });
  } else {
    list.push({
      variant: 'positive',
      icon: 'fa-check-circle',
      text: 'Seçilen alanlar genel veri kategorisinde, paylaşım riski düşük.'
    });
  }

  if (estimatedCount.value > 10000) {
    list.push({
      variant: 'warning',
      icon: 'fa-clock',
      text: 'Kayıt sayısı yüksek (>10k). İşlem 2-3 dakika sürebilir.'
    });
  } else {
    list.push({
      variant: 'positive',
      icon: 'fa-bolt',
      text: 'Tahmini işlem süresi < 10 saniye.'
    });
  }
  
  return list;
});
</script>
