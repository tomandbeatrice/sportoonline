<template>
  <section class="py-12 md:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
      <div class="text-center mb-10">
        <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-3">Tüm Hizmetlerimiz</h2>
        <p class="text-slate-500">İhtiyacınıza uygun hizmeti seçin</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Mağaza -->
        <router-link to="/search" class="group bg-white rounded-2xl p-6 border-2 border-slate-100 hover:border-indigo-200 hover:shadow-lg hover:-translate-y-1 transition-all">
          <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-indigo-200 group-hover:scale-110 transition-transform">
            <span class="text-3xl">🛒</span>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors">Mağaza</h3>
          <p class="text-slate-500 mb-4">50.000+ ürün, güvenli alışveriş</p>
          <span class="inline-flex items-center text-sm font-medium text-indigo-600 group-hover:gap-2 transition-all">
            Alışverişe Başla
            <svg class="w-4 h-4 opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </span>
        </router-link>

        <!-- Yemek -->
        <div class="group bg-white rounded-2xl p-6 border-2 border-slate-100 hover:border-orange-200 hover:shadow-lg hover:-translate-y-1 transition-all">
          <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-orange-200 group-hover:scale-110 transition-transform">
            <span class="text-3xl">🍔</span>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-orange-600 transition-colors">Yemek Siparişi</h3>
          <p class="text-slate-500 mb-4">150+ restoran, hızlı teslimat</p>
          
          <div class="space-y-2">
            <router-link 
              to="/food"
              class="inline-flex items-center text-sm font-medium text-orange-600 group-hover:gap-2 transition-all"
            >
              Restoranlara Göz At
              <svg class="w-4 h-4 opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </router-link>
            
            <button
              @click="$emit('toggle-group-order')"
              class="w-full mt-3 px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-sm font-semibold rounded-lg hover:from-orange-600 hover:to-red-600 transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2"
            >
              <span>👥</span>
              Grup Siparişi
            </button>
          </div>
        </div>

        <!-- Otel -->
        <router-link to="/hotels" class="group bg-white rounded-2xl p-6 border-2 border-slate-100 hover:border-blue-200 hover:shadow-lg hover:-translate-y-1 transition-all">
          <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-blue-200 group-hover:scale-110 transition-transform">
            <span class="text-3xl">🏨</span>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors">Otel Rezervasyonu</h3>
          <p class="text-slate-500 mb-4">En iyi fiyat garantisi</p>
          <span class="inline-flex items-center text-sm font-medium text-blue-600 group-hover:gap-2 transition-all">
            Otel Ara
            <svg class="w-4 h-4 opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </span>
        </router-link>

        <!-- Ulaşım -->
        <router-link to="/rides" class="group bg-white rounded-2xl p-6 border-2 border-slate-100 hover:border-green-200 hover:shadow-lg hover:-translate-y-1 transition-all">
          <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-green-200 group-hover:scale-110 transition-transform">
            <span class="text-3xl">🚗</span>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-green-600 transition-colors">Yolculuk Paylaşımı</h3>
          <p class="text-slate-500 mb-4">Ekonomik ve çevreci ulaşım</p>
          <span class="inline-flex items-center text-sm font-medium text-green-600 group-hover:gap-2 transition-all">
            Yolculuk Bul
            <svg class="w-4 h-4 opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </span>
        </router-link>

        <!-- Hizmet -->
        <router-link to="/services" class="group bg-white rounded-2xl p-6 border-2 border-slate-100 hover:border-purple-200 hover:shadow-lg hover:-translate-y-1 transition-all">
          <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-purple-200 group-hover:scale-110 transition-transform">
            <span class="text-3xl">🔧</span>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-purple-600 transition-colors">Hizmet Marketplace</h3>
          <p class="text-slate-500 mb-4">Masaj, koçluk, eğitim</p>
          <span class="inline-flex items-center text-sm font-medium text-purple-600 group-hover:gap-2 transition-all">
            Hizmetleri Keşfet
            <svg class="w-4 h-4 opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </span>
        </router-link>

        <!-- Kariyer -->
        <router-link to="/career" class="group bg-white rounded-2xl p-6 border-2 border-slate-100 hover:border-amber-200 hover:shadow-lg hover:-translate-y-1 transition-all">
          <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform">
            <span class="text-3xl">💼</span>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-amber-600 transition-colors">Kariyer Fırsatları</h3>
          <p class="text-slate-500 mb-4">12 açık pozisyon</p>
          <span class="inline-flex items-center text-sm font-medium text-amber-600 group-hover:gap-2 transition-all">
            İş İlanlarını Gör
            <svg class="w-4 h-4 opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </span>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
defineEmits(['toggle-group-order'])
</script>
