<template>
  <div class="space-y-6">
    <!-- AI Assistant Header -->
    <div class="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 p-6 text-white shadow-lg">
      <div class="flex items-start justify-between">
        <div>
          <h3 class="text-lg font-bold">İçerik Asistanı</h3>
          <p class="mt-1 text-sm text-indigo-100">Blog yazılarınız için SEO ve içerik önerileri.</p>
        </div>
        <div class="rounded-lg bg-white/20 p-2 backdrop-blur-sm">
          <i class="fas fa-magic text-xl"></i>
        </div>
      </div>
    </div>

    <!-- SEO Score -->
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <h4 class="mb-4 text-sm font-semibold text-slate-900">SEO Skoru</h4>
      <div class="mb-4 flex items-center justify-center">
        <div class="relative flex h-32 w-32 items-center justify-center rounded-full border-8 border-slate-100">
          <svg class="absolute h-full w-full -rotate-90 transform" viewBox="0 0 100 100">
            <circle
              cx="50"
              cy="50"
              r="46"
              fill="none"
              stroke="currentColor"
              stroke-width="8"
              class="text-indigo-600 transition-all duration-1000"
              :stroke-dasharray="circumference"
              :stroke-dashoffset="dashOffset"
            />
          </svg>
          <div class="text-center">
            <span class="block text-3xl font-bold text-slate-900">{{ seoScore }}</span>
            <span class="text-xs text-slate-500">/100</span>
          </div>
        </div>
      </div>
      
      <div class="space-y-3">
        <div v-for="(check, index) in seoChecks" :key="index" class="flex items-center gap-3">
          <div 
            class="flex h-5 w-5 items-center justify-center rounded-full text-[10px]"
            :class="check.passed ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'"
          >
            <i class="fas" :class="check.passed ? 'fa-check' : 'fa-times'"></i>
          </div>
          <span class="text-sm text-slate-600">{{ check.label }}</span>
        </div>
      </div>
    </div>

    <!-- Content Suggestions -->
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <h4 class="mb-4 text-sm font-semibold text-slate-900">İçerik Önerileri</h4>
      <div class="space-y-4">
        <div class="rounded-lg bg-slate-50 p-4">
          <div class="mb-2 flex items-center gap-2">
            <i class="fas fa-heading text-indigo-600"></i>
            <span class="text-sm font-medium text-slate-900">Başlık Alternatifleri</span>
          </div>
          <ul class="space-y-2 pl-6">
            <li v-for="(title, idx) in suggestions.titles" :key="idx" class="list-disc text-xs text-slate-600">
              {{ title }}
            </li>
          </ul>
        </div>

        <div class="rounded-lg bg-slate-50 p-4">
          <div class="mb-2 flex items-center gap-2">
            <i class="fas fa-tags text-indigo-600"></i>
            <span class="text-sm font-medium text-slate-900">Önerilen Etiketler</span>
          </div>
          <div class="flex flex-wrap gap-2">
            <span 
              v-for="tag in suggestions.tags" 
              :key="tag"
              class="rounded-full bg-white px-2 py-1 text-xs font-medium text-slate-600 shadow-sm border border-slate-200"
            >
              #{{ tag }}
            </span>
          </div>
        </div>
      </div>
      
      <button 
        @click="$emit('action', 'optimize')"
        class="mt-4 w-full rounded-lg bg-indigo-50 py-2 text-sm font-medium text-indigo-600 transition hover:bg-indigo-100"
      >
        İçeriği Optimize Et
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  post: {
    type: Object,
    required: true
  }
});

defineEmits(['action']);

// SEO Score Calculation
const circumference = 2 * Math.PI * 46;
const seoScore = ref(75);

const dashOffset = computed(() => {
  return circumference - (seoScore.value / 100) * circumference;
});

const seoChecks = ref([
  { label: 'Başlık uzunluğu uygun (40-60 karakter)', passed: true },
  { label: 'Meta açıklama mevcut', passed: true },
  { label: 'Anahtar kelime yoğunluğu (%1.5)', passed: true },
  { label: 'Görsel alt etiketleri eksik', passed: false },
  { label: 'Okunabilirlik skoru yüksek', passed: true }
]);

const suggestions = ref({
  titles: [
    'Doğru Koşu Ayakkabısı Nasıl Seçilir? 5 İpucu',
    'Koşu Performansınızı Artıracak Ayakkabı Rehberi',
    '2025 Yılının En İyi Koşu Ayakkabıları'
  ],
  tags: ['koşu', 'spor ayakkabı', 'sağlık', 'antrenman', 'maraton']
});

// Watch for post changes to update analysis
watch(() => props.post, (newPost) => {
  if (newPost) {
    // Simulate AI analysis update
    seoScore.value = Math.floor(Math.random() * 30) + 70;
    // Update checks randomly for demo
    seoChecks.value.forEach(check => check.passed = Math.random() > 0.2);
  }
}, { immediate: true });
</script>
