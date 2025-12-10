<template>
  <div class="space-y-6">
    <!-- AI Analysis Header -->
    <div class="rounded-xl border border-orange-100 bg-orange-50/50 p-4">
      <div class="flex items-start gap-3">
        <div class="rounded-lg bg-orange-100 p-2 text-orange-600">
          <i class="fas fa-robot text-lg"></i>
        </div>
        <div>
          <h3 class="font-semibold text-orange-900">Restoran Analizi</h3>
          <p class="text-sm text-orange-700/80">
            Menü performansı, müşteri yorumları ve doluluk oranları analiz edildi.
          </p>
        </div>
      </div>
    </div>

    <!-- Performance Score -->
    <div class="grid gap-4 sm:grid-cols-2">
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Lezzet Skoru</span>
          <i class="fas fa-utensils text-orange-500" v-if="tasteScore > 80"></i>
          <i class="fas fa-utensils text-amber-500" v-else-if="tasteScore > 50"></i>
          <i class="fas fa-utensils text-red-500" v-else></i>
        </div>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-slate-900">{{ tasteScore }}</span>
          <span class="mb-1 text-sm font-medium text-slate-400">/ 100</span>
        </div>
        <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
          <div
            class="h-full rounded-full transition-all duration-1000"
            :class="scoreColorClass"
            :style="{ width: `${tasteScore}%` }"
          ></div>
        </div>
      </div>

      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Doluluk</span>
          <i class="fas fa-chair text-blue-500"></i>
        </div>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-slate-900">%{{ occupancyRate }}</span>
        </div>
        <p class="mt-2 text-xs text-slate-500">Yoğun saatlerde tam kapasite.</p>
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
          @click="$emit('action', 'suggest_menu')"
        >
          <i class="fas fa-hamburger text-orange-500"></i>
          Menü Önerisi Oluştur
        </button>
        
        <button 
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-white border border-slate-300 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
          @click="$emit('action', 'boost_visibility')"
        >
          <i class="fas fa-rocket text-purple-500"></i>
          Görünürlüğü Artır
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  restaurant: {
    type: Object,
    required: true
  }
});

defineEmits(['action']);

// Mock AI Logic
const tasteScore = computed(() => {
  if (!props.restaurant) return 0;
  // Mock score based on rating
  return Math.floor(props.restaurant.rating * 20);
});

const occupancyRate = computed(() => {
  return Math.floor(Math.random() * 40) + 60; // 60-100%
});

const scoreColorClass = computed(() => {
  if (tasteScore.value >= 80) return 'bg-emerald-500';
  if (tasteScore.value >= 60) return 'bg-amber-500';
  return 'bg-red-500';
});

const insights = computed(() => {
  const list = [];
  
  if (props.restaurant.rating >= 4.5) {
    list.push({
      variant: 'positive',
      icon: 'fa-check-circle',
      text: 'Müşteri memnuniyeti çok yüksek, "Günün Seçimi" listesine eklenebilir.'
    });
  } else if (props.restaurant.rating >= 3.5) {
    list.push({
      variant: 'warning',
      icon: 'fa-exclamation-circle',
      text: 'Servis hızıyla ilgili bazı olumsuz yorumlar tespit edildi.'
    });
  } else {
    list.push({
      variant: 'negative',
      icon: 'fa-times-circle',
      text: 'Hijyen puanı düşük, acil denetim önerilir.'
    });
  }

  if (occupancyRate.value > 90) {
    list.push({
      variant: 'positive',
      icon: 'fa-fire',
      text: 'Popülarite artışta, rezervasyon sistemi kapasite uyarısı veriyor.'
    });
  }
  
  return list;
});
</script>
