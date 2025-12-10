<template>
  <div class="space-y-6">
    <!-- Security Insight -->
    <div class="rounded-xl bg-gradient-to-r from-slate-800 to-slate-900 p-6 text-white shadow-lg">
      <div class="flex items-start justify-between">
        <div>
          <h3 class="text-lg font-bold">Güvenlik Analizi</h3>
          <p class="mt-1 text-sm text-slate-300">Yönetici hesapları ve yetki dağılımı analizi.</p>
        </div>
        <div class="rounded-lg bg-white/10 p-2 backdrop-blur-sm">
          <i class="fas fa-user-shield text-xl"></i>
        </div>
      </div>
      
      <div class="mt-6 grid grid-cols-2 gap-4">
        <div class="rounded-lg bg-white/5 p-3">
          <span class="block text-xs text-slate-400">Risk Skoru</span>
          <span class="text-2xl font-bold text-emerald-400">92/100</span>
        </div>
        <div class="rounded-lg bg-white/5 p-3">
          <span class="block text-xs text-slate-400">Aktif Oturum</span>
          <span class="text-2xl font-bold text-blue-400">4</span>
        </div>
      </div>
    </div>

    <!-- Role Optimization -->
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <h4 class="mb-4 text-sm font-semibold text-slate-900">Yetki Optimizasyonu</h4>
      <div class="space-y-4">
        <div v-for="suggestion in suggestions" :key="suggestion.id" class="rounded-lg bg-slate-50 p-4">
          <div class="mb-2 flex items-center gap-2">
            <i class="fas fa-lightbulb text-amber-500"></i>
            <span class="text-sm font-medium text-slate-900">{{ suggestion.title }}</span>
          </div>
          <p class="text-xs text-slate-600">{{ suggestion.description }}</p>
          <button 
            @click="$emit('action', suggestion.action)"
            class="mt-3 w-full rounded border border-slate-200 bg-white py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50"
          >
            İncele
          </button>
        </div>
      </div>
    </div>

    <!-- Activity Anomaly -->
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <h4 class="mb-4 text-sm font-semibold text-slate-900">Aktivite Anomalileri</h4>
      <div class="space-y-3">
        <div class="flex items-start gap-3 rounded-lg border border-red-100 bg-red-50 p-3">
          <i class="fas fa-exclamation-triangle mt-0.5 text-red-500"></i>
          <div>
            <p class="text-xs font-medium text-red-800">Şüpheli Giriş Denemesi</p>
            <p class="text-[10px] text-red-600 mt-1">Admin #4 hesabı için farklı lokasyonlardan çoklu giriş denemesi tespit edildi.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const suggestions = ref([
  {
    id: 1,
    title: 'Gereksiz Yetki Tespiti',
    description: '"Editör" rolündeki kullanıcıların %80\'i "Silme" yetkisini son 3 aydır kullanmadı. Bu yetkiyi kaldırmayı düşünün.',
    action: 'review_permissions'
  },
  {
    id: 2,
    title: 'Atıl Hesap Uyarısı',
    description: 'Son 60 gündür giriş yapmayan 2 yönetici hesabı tespit edildi. Güvenlik için askıya alınabilir.',
    action: 'review_inactive'
  }
]);

defineEmits(['action']);
</script>
