<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       🥗 SAĞLIKLI YAŞAM & SPORCU MUTFAĞI - Yeni Özellik
       ═══════════════════════════════════════════════════════════════════ -->
  <section class="healthy-living py-6">
    <div class="grid md:grid-cols-2 gap-6">
      <!-- Sporcu Mutfağı (Dynamic Banner) -->
      <div v-if="healthyLivingBanner" class="relative overflow-hidden rounded-2xl text-white p-8 group cursor-pointer" :style="{ backgroundImage: `url(${healthyLivingBanner.image_url})`, backgroundSize: 'cover', backgroundPosition: 'center' }">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/80 to-teal-900/80"></div>
        <div class="relative z-10">
          <div class="flex items-center gap-3 mb-4">
            <span class="text-4xl bg-white/20 p-3 rounded-xl backdrop-blur-sm">🥗</span>
            <div>
              <h3 class="text-2xl font-black">{{ healthyLivingBanner.title }}</h3>
              <p class="text-emerald-100 font-medium">{{ healthyLivingBanner.description }}</p>
            </div>
          </div>
          <button class="bg-white text-emerald-600 px-6 py-3 rounded-xl font-bold hover:bg-emerald-50 transition-colors shadow-lg mt-8">
            İncele
          </button>
        </div>
      </div>
      <!-- Fallback if no banner -->
      <div v-else class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white p-8 group cursor-pointer">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-16 -mt-16 blur-3xl transition-all group-hover:bg-white/20"></div>
        <div class="relative z-10">
          <div class="flex items-center gap-3 mb-4">
            <span class="text-4xl bg-white/20 p-3 rounded-xl backdrop-blur-sm">🥗</span>
            <div>
              <h3 class="text-2xl font-black">Sporcu Mutfağı</h3>
              <p class="text-emerald-100 font-medium">Hedefine uygun beslenme paketleri</p>
            </div>
          </div>
          <p class="text-emerald-50 mb-6">Sağlıklı yaşam için ihtiyacınız olan her şey burada.</p>
          <button class="bg-white text-emerald-600 px-6 py-3 rounded-xl font-bold hover:bg-emerald-50 transition-colors shadow-lg">
            Menüleri İncele
          </button>
        </div>
      </div>

      <!-- Diyet Market -->
      <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-400 to-red-500 text-white p-8 group cursor-pointer">
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full -ml-16 -mb-16 blur-3xl transition-all group-hover:bg-white/20"></div>
        <div class="relative z-10">
          <div class="flex items-center gap-3 mb-4">
            <span class="text-4xl bg-white/20 p-3 rounded-xl backdrop-blur-sm">🥑</span>
            <div>
              <h3 class="text-2xl font-black">Diyet Market</h3>
              <p class="text-orange-100 font-medium">Sağlıklı atıştırmalıklar ve takviyeler</p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm hover:bg-white/20 transition-colors">
              <span class="text-2xl block mb-1">🥜</span>
              <span class="text-sm font-bold">Protein Barlar</span>
            </div>
            <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm hover:bg-white/20 transition-colors">
              <span class="text-2xl block mb-1">💊</span>
              <span class="text-sm font-bold">Vitaminler</span>
            </div>
            <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm hover:bg-white/20 transition-colors">
              <span class="text-2xl block mb-1">🥣</span>
              <span class="text-sm font-bold">Granola</span>
            </div>
            <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm hover:bg-white/20 transition-colors">
              <span class="text-2xl block mb-1">🥤</span>
              <span class="text-sm font-bold">Detoks Suları</span>
            </div>
          </div>
          <button class="w-full bg-white text-orange-600 px-6 py-3 rounded-xl font-bold hover:bg-orange-50 transition-colors shadow-lg">
            Market'e Git
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
const healthyLivingBanner = computed(() => marketplaceStore.healthyLivingBanner)

const fetchBanners = async () => {
  if (marketplaceStore.banners.length === 0) {
    await marketplaceStore.fetchBanners()
  }
}

onMounted(() => {
  fetchBanners()
})
</script>
