<template>
  <section class="relative bg-gradient-to-b from-slate-50 to-white py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="grid md:grid-cols-2 gap-12 items-center">
        <!-- Content -->
        <div class="space-y-8">
          <div class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 border border-indigo-100 rounded-full text-sm font-medium text-indigo-600">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            {{ content.badge }}
          </div>
          
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight text-slate-900">
            {{ content.title }}
          </h1>
          
          <p class="text-lg md:text-xl text-slate-600 max-w-lg leading-relaxed">
            {{ content.subtitle }}
          </p>
          
          <div class="flex flex-wrap gap-4">
            <router-link 
              :to="content.primaryCta.to"
              class="inline-flex items-center gap-2 px-8 py-4 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-all"
            >
              {{ content.primaryCta.label }}
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </router-link>
            <router-link 
              :to="content.secondaryCta.to"
              class="inline-flex items-center gap-2 px-8 py-4 border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50 transition-all"
            >
              {{ content.secondaryCta.label }}
            </router-link>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-4">
            <div v-for="stat in content.stats" :key="stat.label" class="text-center md:text-left">
              <p class="text-3xl md:text-4xl font-bold text-indigo-600">{{ stat.value }}</p>
              <p class="text-sm text-slate-500 mt-1">{{ stat.label }}</p>
            </div>
          </div>
        </div>

        <!-- Daily Deal Card -->
        <div class="hidden md:block">
          <div class="relative bg-white rounded-3xl p-8 shadow-xl border border-slate-100">
            <div class="absolute -top-4 -right-4 px-4 py-2 bg-red-500 text-white text-sm font-bold rounded-full shadow-lg animate-bounce">
              🔥 Günün Fırsatı
            </div>
            <p class="text-xl font-bold text-slate-900 mb-6">{{ content.dailyDeal }}</p>
            <div class="aspect-video bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl flex items-center justify-center mb-6">
              <span class="text-8xl">🎁</span>
            </div>
            <button class="w-full py-4 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
              Fırsatı Yakala
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// Default content if not provided via props (though we'll use internal state for now as it was static)
const content = ref({
  badge: '🚀 Hızlı Teslimat',
  title: 'En İyi Fiyatlarla Alışveriş',
  subtitle: 'Binlerce ürün, güvenli ödeme ve hızlı kargo ile kapınızda. Ücretsiz kargo fırsatını kaçırmayın!',
  primaryCta: { label: 'Alışverişe Başla', to: '/search' },
  secondaryCta: { label: 'Kampanyalar', to: '/campaigns' },
  stats: [
    { label: 'Aktif Kullanıcı', value: '12.4K' },
    { label: 'Günlük Sipariş', value: '840' },
    { label: 'Memnuniyet', value: '%98' },
    { label: 'Ürün Çeşidi', value: '25K+' }
  ],
  dailyDeal: '🔥 Bugünün Fırsatı: Elektronik kategorisinde %40 indirim!'
})
</script>
