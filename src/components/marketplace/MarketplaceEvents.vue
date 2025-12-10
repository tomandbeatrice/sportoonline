<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       📅 ALIŞVERİŞ ETKİNLİKLERİ - Yeni Özellik
       ═══════════════════════════════════════════════════════════════════ -->
  <section class="shopping-events py-6">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
        <span class="text-2xl">📅</span> Etkinlik Takvimi
      </h2>
    </div>
    
    <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
      <div v-for="event in shoppingEvents" :key="event.id" 
        class="min-w-[280px] bg-white rounded-xl border border-slate-100 p-5 hover:shadow-md transition-all group"
      >
        <div class="flex items-start justify-between mb-4">
          <div :class="`w-12 h-12 rounded-xl flex items-center justify-center text-2xl ${event.color.split(' ')[0]}`">
            {{ event.icon }}
          </div>
          <span :class="`px-3 py-1 rounded-full text-xs font-bold ${event.color}`">
            {{ event.status }}
          </span>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">{{ event.title }}</h3>
        <p class="text-slate-500 text-sm flex items-center gap-1">
          <svg class="w-4 h-4 min-w-[1rem]" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          {{ event.date }}
        </p>
        <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between">
          <span class="text-xs text-slate-400">Hatırlatıcı Ekle</span>
          <button class="w-8 h-8 rounded-full bg-slate-50 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-center transition-colors">
            <svg class="w-5 h-5 min-w-[1.25rem]" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useMarketplaceStore } from '@/stores/marketplaceStore'

const marketplaceStore = useMarketplaceStore()
const shoppingEvents = computed(() => marketplaceStore.campaigns)

const fetchActiveCampaigns = async () => {
  if (shoppingEvents.value.length === 0) {
    await marketplaceStore.fetchCampaigns()
  }
}

onMounted(() => {
  fetchActiveCampaigns()
})
</script>

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
