<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       💎 AYRICALIKLI HİZMETLER - Premium Features
       ═══════════════════════════════════════════════════════════════════ -->
  <section class="premium-services py-6 mb-8">
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl p-8 text-white overflow-hidden relative">
      <!-- Background Pattern -->
      <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl -mr-20 -mt-20"></div>
      <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl -ml-10 -mb-10"></div>
      
      <div class="relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-10">
          <h2 class="text-2xl md:text-3xl font-black mb-4">SportoOnline Ekosistemi</h2>
          <p class="text-slate-300">Sadece alışveriş değil, yaşam tarzı. Premium üyelerimize özel ayrıcalıklı hizmetler.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
          <div v-for="feature in premiumFeatures" :key="feature.id" class="flex flex-col items-center gap-3 group cursor-pointer">
            <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center text-3xl group-hover:bg-white/20 group-hover:scale-110 transition-all border border-white/5">
              {{ feature.icon }}
            </div>
            <span class="text-sm font-medium text-slate-300 group-hover:text-white text-center">{{ feature.name }}</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const premiumFeatures = ref([
  { id: 'kitchen', name: 'Sporcu Mutfağı', icon: '🥗', isNew: true },
  { id: 'virtual-try', name: 'Sanal Deneme Kabini', icon: '👕', isNew: true },
  { id: 'ai-suggest', name: 'AI Öneri Motoru', icon: '🤖', isNew: false },
  { id: 'nutrition', name: 'Beslenme Danışmanı', icon: '🥑', isNew: false },
  { id: 'trainer', name: 'Kişisel Antrenör', icon: '🏋️', isNew: false },
  { id: 'live-support', name: 'Canlı Destek', icon: '💬', isNew: false }
])
</script>
