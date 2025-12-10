<template>
  <div class="min-h-screen bg-slate-50 pb-20">
    <!-- Hero Section with Bento Grid -->
    <div class="mx-auto max-w-6xl px-4 py-6">
      <h1 class="mb-6 text-2xl font-bold text-slate-800">
        Merhaba, bugün neye ihtiyacın var?
      </h1>

      <div class="grid grid-cols-2 gap-4 md:grid-cols-4 md:grid-rows-3 md:gap-6 h-[600px]">
        
        <!-- Food Module (Large Square) -->
        <router-link 
          to="/food"
          class="group relative col-span-2 row-span-2 overflow-hidden rounded-3xl bg-orange-500 p-6 text-white shadow-lg transition-transform hover:scale-[1.02] hover:shadow-orange-500/30"
        >
          <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-white/10 blur-3xl transition-all group-hover:bg-white/20"></div>
          <div class="relative z-10 flex h-full flex-col justify-between">
            <div>
              <span class="mb-2 inline-block rounded-full bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur-md">
                30-45 dk Teslimat
              </span>
              <h2 class="text-3xl font-black leading-tight md:text-4xl">
                Acıktın mı?<br />
                <span class="text-orange-100">Sıcak yemek kapında.</span>
              </h2>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium opacity-90">Restoranları Keşfet &rarr;</span>
              <span class="text-6xl">🍔</span>
            </div>
          </div>
          <!-- Decorative Image/Icon -->
          <img 
            src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&q=80" 
            alt="Burger" 
            class="absolute bottom-0 right-0 w-48 translate-x-10 translate-y-10 rotate-12 object-cover opacity-90 transition-transform group-hover:rotate-6 group-hover:scale-110"
          />
        </router-link>

        <!-- Marketplace Module (Wide Rectangle) -->
        <router-link 
          to="/market"
          class="group relative col-span-2 row-span-1 overflow-hidden rounded-3xl bg-blue-600 p-6 text-white shadow-lg transition-transform hover:scale-[1.02] hover:shadow-blue-600/30"
        >
          <div class="absolute -left-10 -bottom-10 h-48 w-48 rounded-full bg-white/10 blur-3xl transition-all group-hover:bg-white/20"></div>
          <div class="relative z-10 flex h-full items-center justify-between">
            <div>
              <span class="mb-2 inline-block rounded-full bg-white/20 px-3 py-1 text-xs font-bold backdrop-blur-md">
                %50'ye varan indirimler
              </span>
              <h2 class="text-2xl font-black md:text-3xl">
                Süper Market<br />
                <span class="text-blue-200">Haftanın Fırsatları</span>
              </h2>
            </div>
            <span class="text-5xl">🛍️</span>
          </div>
        </router-link>

        <!-- Transport Module (Small Square) -->
        <router-link 
          to="/transport"
          class="group relative col-span-1 row-span-1 overflow-hidden rounded-3xl bg-emerald-600 p-5 text-white shadow-lg transition-transform hover:scale-[1.02] hover:shadow-emerald-600/30"
        >
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-emerald-700 opacity-100"></div>
          <div class="relative z-10 flex h-full flex-col justify-between">
            <h2 class="text-xl font-bold">Ulaşım</h2>
            <div class="flex items-end justify-between">
              <span class="text-xs opacity-80">Araç Çağır</span>
              <span class="text-4xl">🚖</span>
            </div>
          </div>
        </router-link>

        <!-- Hotel Module (Small Square) -->
        <router-link 
          to="/hotel"
          class="group relative col-span-1 row-span-1 overflow-hidden rounded-3xl bg-purple-600 p-5 text-white shadow-lg transition-transform hover:scale-[1.02] hover:shadow-purple-600/30"
        >
          <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-purple-700 opacity-100"></div>
          <div class="relative z-10 flex h-full flex-col justify-between">
            <h2 class="text-xl font-bold">Seyahat</h2>
            <div class="flex items-end justify-between">
              <span class="text-xs opacity-80">Otel & Tatil</span>
              <span class="text-4xl">🏨</span>
            </div>
          </div>
        </router-link>

        <!-- Career Module (Wide Rectangle at Bottom) -->
        <router-link 
          to="/career"
          class="group relative col-span-2 row-span-1 overflow-hidden rounded-3xl bg-cyan-600 p-6 text-white shadow-lg transition-transform hover:scale-[1.02] hover:shadow-cyan-600/30"
        >
          <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-black/10 to-transparent"></div>
          <div class="relative z-10 flex items-center justify-between">
            <div>
              <h2 class="text-xl font-bold">Kariyer Fırsatları</h2>
              <p class="text-sm text-cyan-100">Hayalindeki işi veya çalışanı bul.</p>
            </div>
            <span class="text-4xl">💼</span>
          </div>
        </router-link>

        <!-- Services Module (Remaining Space) -->
        <router-link 
          to="/services"
          class="group relative col-span-2 row-span-1 overflow-hidden rounded-3xl bg-slate-800 p-6 text-white shadow-lg transition-transform hover:scale-[1.02] hover:shadow-slate-800/30"
        >
           <div class="relative z-10 flex items-center justify-between">
            <div>
              <h2 class="text-xl font-bold">Hizmetler</h2>
              <p class="text-sm text-slate-300">Temizlik, Tamirat ve daha fazlası.</p>
            </div>
            <span class="text-4xl">🔧</span>
          </div>
        </router-link>

      </div>
    </div>

    <!-- Quick Actions / Recent -->
    <div class="mx-auto max-w-6xl px-4 py-6">
      <h3 class="mb-4 text-lg font-bold text-slate-800">Sizin İçin Önerilenler</h3>
      <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
        <div v-for="i in 5" :key="i" class="min-w-[160px] rounded-xl bg-white p-3 shadow-sm border border-slate-100">
          <div class="mb-2 h-24 w-full rounded-lg bg-slate-100"></div>
          <div class="h-4 w-3/4 rounded bg-slate-200 mb-2"></div>
          <div class="h-3 w-1/2 rounded bg-slate-100"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// No script needed for now, purely layout
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
