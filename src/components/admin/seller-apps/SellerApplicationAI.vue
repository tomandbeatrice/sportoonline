<template>
  <div class="space-y-6">
    <!-- AI Analysis Header -->
    <div class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-4">
      <div class="flex items-start gap-3">
        <div class="rounded-lg bg-indigo-100 p-2 text-indigo-600">
          <i class="fas fa-robot text-lg"></i>
        </div>
        <div>
          <h3 class="font-semibold text-indigo-900">Yapay Zeka Değerlendirmesi</h3>
          <p class="text-sm text-indigo-700/80">
            Başvuru verileri ve belgeler analiz edilerek risk ve güvenilirlik skoru hesaplanmıştır.
          </p>
        </div>
      </div>
    </div>

    <!-- Risk Score & Confidence -->
    <div class="grid gap-4 sm:grid-cols-2">
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Güven Skoru</span>
          <i class="fas fa-shield-alt text-emerald-500" v-if="trustScore > 70"></i>
          <i class="fas fa-exclamation-triangle text-amber-500" v-else-if="trustScore > 40"></i>
          <i class="fas fa-times-circle text-red-500" v-else></i>
        </div>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-slate-900">{{ trustScore }}</span>
          <span class="mb-1 text-sm font-medium text-slate-400">/ 100</span>
        </div>
        <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
          <div
            class="h-full rounded-full transition-all duration-1000"
            :class="scoreColorClass"
            :style="{ width: `${trustScore}%` }"
          ></div>
        </div>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Belge Doğrulama</span>
          <i class="fas fa-file-contract text-blue-500"></i>
        </div>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-slate-900">{{ documentMatch }}%</span>
          <span class="mb-1 text-sm font-medium text-slate-400">Eşleşme</span>
        </div>
        <p class="mt-2 text-xs text-slate-500">Vergi levhası ve kimlik bilgileri tutarlılığı.</p>
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

    <!-- Recommended Action -->
    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
      <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Önerilen Aksiyon</h4>
      <div class="flex flex-col gap-2">
        <button 
          v-if="trustScore >= 80"
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
          @click="$emit('action', 'approve')"
        >
          <i class="fas fa-check"></i>
          Otomatik Onayla
        </button>
        
        <button 
          v-else-if="trustScore < 50"
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-red-600 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700"
          @click="$emit('action', 'reject')"
        >
          <i class="fas fa-ban"></i>
          Reddet (Riskli)
        </button>

        <button 
          v-else
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-white border border-slate-300 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
          @click="$emit('action', 'request_info')"
        >
          <i class="fas fa-question-circle"></i>
          Ek Belge İste
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  application: {
    type: Object,
    required: true
  }
});

defineEmits(['action']);

// Mock AI Logic based on application data
const trustScore = computed(() => {
  if (!props.application) return 0;
  let score = 60; // Base score
  
  // Email domain check (mock)
  if (props.application.user.email.endsWith('@gmail.com') || props.application.user.email.endsWith('@hotmail.com')) {
    score -= 10; // Generic domains are slightly less trusted for business
  } else {
    score += 15; // Corporate domain
  }

  // Name length check (mock)
  if (props.application.store_name && props.application.store_name.length > 5) {
    score += 10;
  }

  // Random fluctuation for demo variety
  score += (props.application.id % 20) - 10;

  return Math.min(100, Math.max(0, score));
});

const documentMatch = computed(() => {
  // Mock document verification score
  return Math.min(100, Math.max(40, trustScore.value + 10));
});

const scoreColorClass = computed(() => {
  if (trustScore.value >= 80) return 'bg-emerald-500';
  if (trustScore.value >= 50) return 'bg-amber-500';
  return 'bg-red-500';
});

const insights = computed(() => {
  const list = [];
  
  if (trustScore.value >= 80) {
    list.push({
      variant: 'positive',
      icon: 'fa-check-circle',
      text: 'Vergi numarası ve ticari sicil kaydı doğrulandı.'
    });
    list.push({
      variant: 'positive',
      icon: 'fa-check-circle',
      text: 'E-posta adresi kurumsal ve aktif görünüyor.'
    });
  } else if (trustScore.value >= 50) {
    list.push({
      variant: 'warning',
      icon: 'fa-exclamation-circle',
      text: 'Vergi levhası görüntüsü düşük çözünürlüklü, manuel kontrol önerilir.'
    });
    list.push({
      variant: 'positive',
      icon: 'fa-check-circle',
      text: 'İletişim bilgileri formatı doğru.'
    });
  } else {
    list.push({
      variant: 'negative',
      icon: 'fa-times-circle',
      text: 'Şüpheli IP adresi veya mükerrer başvuru tespiti.'
    });
    list.push({
      variant: 'negative',
      icon: 'fa-times-circle',
      text: 'Mağaza adı politikalarla uyumsuz olabilir.'
    });
  }
  
  return list;
});
</script>
