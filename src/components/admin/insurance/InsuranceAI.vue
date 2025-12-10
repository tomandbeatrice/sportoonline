<template>
  <div class="space-y-6">
    <!-- AI Header -->
    <div class="rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 p-6 text-white shadow-lg">
      <div class="flex items-start justify-between">
        <div>
          <div class="flex items-center gap-2">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 backdrop-blur-sm">
              <i class="fas fa-robot text-sm"></i>
            </div>
            <h2 class="text-lg font-bold">Sigorta Zekası</h2>
          </div>
          <p class="mt-1 text-sm text-teal-100">
            {{ policy ? policy.type : 'Genel' }} için risk ve prim analizi
          </p>
        </div>
        <div class="rounded-lg bg-white/10 px-3 py-1 text-xs font-medium backdrop-blur-sm">
          Canlı Analiz
        </div>
      </div>

      <!-- Key Metrics -->
      <div class="mt-6 grid grid-cols-2 gap-4">
        <div class="rounded-lg bg-white/10 p-3 backdrop-blur-sm">
          <div class="text-xs text-teal-100">Risk Skoru</div>
          <div class="mt-1 flex items-end gap-2">
            <span class="text-2xl font-bold">{{ aiMetrics.riskScore }}/100</span>
            <span class="mb-1 text-xs text-emerald-300">
              <i class="fas fa-arrow-down"></i> Düşük
            </span>
          </div>
        </div>
        <div class="rounded-lg bg-white/10 p-3 backdrop-blur-sm">
          <div class="text-xs text-teal-100">Hasar Olasılığı</div>
          <div class="mt-1 flex items-end gap-2">
            <span class="text-2xl font-bold">%{{ aiMetrics.claimProbability }}</span>
            <span class="mb-1 text-xs text-emerald-300">
              <i class="fas fa-shield-alt"></i> Güvenli
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
                class="mt-2 text-xs font-medium text-teal-600 hover:text-teal-700"
              >
                {{ insight.action }} →
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Coverage Analysis (Mock) -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
      <h3 class="mb-3 text-sm font-semibold text-slate-900">Kapsam Analizi</h3>
      <div class="space-y-3">
        <div v-for="(item, index) in coverageAnalysis" :key="index">
          <div class="mb-1 flex justify-between text-xs">
            <span class="font-medium text-slate-700">{{ item.label }}</span>
            <span class="text-slate-500">{{ item.value }}</span>
          </div>
          <div class="h-1.5 w-full rounded-full bg-slate-100">
            <div 
              class="h-1.5 rounded-full transition-all"
              :class="item.score > 70 ? 'bg-emerald-500' : (item.score > 40 ? 'bg-amber-500' : 'bg-red-500')"
              :style="{ width: item.score + '%' }"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Claim Trend Chart (Mock) -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-900">Hasar Trendi</h3>
        <select class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs">
          <option>Son 6 Ay</option>
          <option>Son 1 Yıl</option>
        </select>
      </div>
      <div class="flex h-32 items-end justify-between gap-1">
        <div 
          v-for="(month, i) in claimTrend" 
          :key="i"
          class="w-full rounded-t bg-teal-100 transition-all hover:bg-teal-200"
          :style="{ height: month.value + '%' }"
        ></div>
      </div>
      <div class="mt-2 flex justify-between text-[10px] text-slate-400">
        <span>Oca</span>
        <span>Mar</span>
        <span>May</span>
        <span>Tem</span>
        <span>Eyl</span>
        <span>Kas</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  policy: {
    type: Object,
    default: null
  }
});

const aiMetrics = ref({
  riskScore: 15,
  claimProbability: 5
});

const insights = ref([
  {
    title: 'Düşük Risk Profili',
    description: 'Müşterinin geçmiş verileri ve demografik özellikleri düşük risk gösteriyor.',
    icon: 'fa-check-circle',
    bgClass: 'bg-emerald-100',
    textClass: 'text-emerald-600',
    action: 'İndirim Uygula',
    actionType: 'apply_discount'
  },
  {
    title: 'Çapraz Satış Fırsatı',
    description: 'Seyahat sigortası alan müşteriler genellikle sağlık sigortası da alıyor.',
    icon: 'fa-exchange-alt',
    bgClass: 'bg-blue-100',
    textClass: 'text-blue-600',
    action: 'Teklif Oluştur',
    actionType: 'create_offer'
  }
]);

const coverageAnalysis = [
  { label: 'Teminat Limiti', value: 'Yeterli', score: 85 },
  { label: 'Muafiyet Oranı', value: 'Standart', score: 60 },
  { label: 'Ek Hizmetler', value: 'Kapsamlı', score: 90 },
];

const claimTrend = Array.from({ length: 12 }, () => ({
  value: Math.floor(Math.random() * 40) + 10
}));

// Watch for policy changes to update mock data
watch(() => props.policy, (newPolicy) => {
  if (newPolicy) {
    // Simulate data update based on policy
    aiMetrics.value.riskScore = Math.floor(Math.random() * 30) + 10;
    aiMetrics.value.claimProbability = Math.floor(Math.random() * 15) + 2;
  }
});
</script>
