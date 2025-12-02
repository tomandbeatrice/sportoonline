<!-- 
  MarketplaceHome.vue - Ana Marketplace Sayfası
  Hiyerarşi:
  1. Header (Logo, Auth, Dil, Sesli Asistan)
  2. Hizmet Sekmeleri (Yemek, Otel, Rides, Hizmet, Kariyer)
  3. Özel Hizmetler (Sporcu Mutfağı, Sanal Deneme Kabini)
  4. Kampanya Slider
  5. Aktif Siparişler
  6. Bundle Teklifleri
  7. Rating & Yorumlar
-->
<template>
  <div class="marketplace-home min-h-screen bg-slate-50">
    
    <!-- ═══════════════════════════════════════════════════════════════════
         🔗 HİZMET SEKMELERİ - Yemek, Otel, Rides, Hizmet, Kariyer
         ═══════════════════════════════════════════════════════════════════ -->
    <nav class="bg-white border-b border-slate-200 sticky top-[120px] sm:top-[140px] lg:top-[130px] z-40 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide py-3">
          <!-- Ana Mağaza -->
          <router-link 
            to="/"
            class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
            :class="$route.path === '/' 
              ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg shadow-indigo-200' 
              : 'bg-white text-slate-700 border-slate-200 hover:border-indigo-300 hover:bg-indigo-50'"
          >
            <span class="text-xl">🛒</span>
            <span>Mağaza</span>
          </router-link>

          <!-- Yemek -->
          <router-link 
            to="/food"
            class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
            :class="$route.path === '/food' 
              ? 'bg-orange-500 text-white border-orange-500 shadow-lg shadow-orange-200' 
              : 'bg-white text-slate-700 border-slate-200 hover:border-orange-300 hover:bg-orange-50'"
          >
            <span class="text-xl">🍔</span>
            <span>Yemek</span>
            <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-600 font-bold">Yeni</span>
          </router-link>

          <!-- Otel -->
          <router-link 
            to="/hotels"
            class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
            :class="$route.path.startsWith('/hotels') 
              ? 'bg-blue-500 text-white border-blue-500 shadow-lg shadow-blue-200' 
              : 'bg-white text-slate-700 border-slate-200 hover:border-blue-300 hover:bg-blue-50'"
          >
            <span class="text-xl">🏨</span>
            <span>Otel</span>
          </router-link>

          <!-- Yolculuk/Rides -->
          <router-link 
            to="/rides"
            class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
            :class="$route.path === '/rides' 
              ? 'bg-green-500 text-white border-green-500 shadow-lg shadow-green-200' 
              : 'bg-white text-slate-700 border-slate-200 hover:border-green-300 hover:bg-green-50'"
          >
            <span class="text-xl">🚗</span>
            <span>Ulaşım</span>
          </router-link>

          <!-- Hizmet -->
          <router-link 
            to="/services"
            class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
            :class="$route.path === '/services' 
              ? 'bg-purple-500 text-white border-purple-500 shadow-lg shadow-purple-200' 
              : 'bg-white text-slate-700 border-slate-200 hover:border-purple-300 hover:bg-purple-50'"
          >
            <span class="text-xl">🔧</span>
            <span>Hizmet</span>
          </router-link>

          <!-- Kariyer -->
          <router-link 
            to="/career"
            class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border-2"
            :class="$route.path === '/career' 
              ? 'bg-amber-500 text-white border-amber-500 shadow-lg shadow-amber-200' 
              : 'bg-white text-slate-700 border-slate-200 hover:border-amber-300 hover:bg-amber-50'"
          >
            <span class="text-xl">💼</span>
            <span>Kariyer</span>
            <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-600 font-bold">12</span>
          </router-link>
        </div>
      </div>
    </nav>

    <!-- ═══════════════════════════════════════════════════════════════════
         🦸 HERO SECTION - Sade, Açık Arka Plan
         ═══════════════════════════════════════════════════════════════════ -->
    <section class="relative bg-gradient-to-b from-slate-50 to-white py-16 md:py-24">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">
          <!-- Content -->
          <div class="space-y-8">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 border border-indigo-100 rounded-full text-sm font-medium text-indigo-600">
              <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
              {{ heroContent.badge }}
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-tight text-slate-900">
              {{ heroContent.title }}
            </h1>
            
            <p class="text-lg md:text-xl text-slate-600 max-w-lg leading-relaxed">
              {{ heroContent.subtitle }}
            </p>
            
            <div class="flex flex-wrap gap-4">
              <router-link 
                :to="heroContent.primaryCta.to"
                class="inline-flex items-center gap-2 px-8 py-4 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-0.5 transition-all"
              >
                {{ heroContent.primaryCta.label }}
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
              </router-link>
              <router-link 
                :to="heroContent.secondaryCta.to"
                class="inline-flex items-center gap-2 px-8 py-4 border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50 transition-all"
              >
                {{ heroContent.secondaryCta.label }}
              </router-link>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-4">
              <div v-for="stat in heroContent.stats" :key="stat.label" class="text-center md:text-left">
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
              <p class="text-xl font-bold text-slate-900 mb-6">{{ heroContent.dailyDeal }}</p>
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

    <!-- ═══════════════════════════════════════════════════════════════════
         🎯 MODÜL GRİD - 6 Ana Hizmet
         ═══════════════════════════════════════════════════════════════════ -->
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
          <router-link to="/food" class="group bg-white rounded-2xl p-6 border-2 border-slate-100 hover:border-orange-200 hover:shadow-lg hover:-translate-y-1 transition-all">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-orange-200 group-hover:scale-110 transition-transform">
              <span class="text-3xl">🍔</span>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-orange-600 transition-colors">Yemek Siparişi</h3>
            <p class="text-slate-500 mb-4">150+ restoran, hızlı teslimat</p>
            <span class="inline-flex items-center text-sm font-medium text-orange-600 group-hover:gap-2 transition-all">
              Restoranlara Göz At
              <svg class="w-4 h-4 opacity-0 -ml-4 group-hover:opacity-100 group-hover:ml-0 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </span>
          </router-link>

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

    <!-- ═══════════════════════════════════════════════════════════════════
         🏷️ KAMPANYA SLIDER
         ═══════════════════════════════════════════════════════════════════ -->
    <section 
      v-if="campaigns.length > 0"
      class="campaign-slider relative"
      @mouseenter="pauseCampaignAutoPlay"
      @mouseleave="resumeCampaignAutoPlay"
      @touchstart="handleCampaignTouchStart"
      @touchend="handleCampaignTouchEnd"
    >
      <div 
        class="relative overflow-hidden"
        :style="currentCampaignGradient"
      >
        <!-- Campaign Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 md:py-8">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Campaign Info -->
            <div class="flex-1 text-white">
              <span class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 rounded-full text-sm mb-3">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                {{ t('campaign.live') }}
              </span>
              <h2 class="text-2xl md:text-3xl font-bold mb-2">
                🎉 {{ currentCampaign.title }}
              </h2>
              <p class="text-white/90 text-sm md:text-base max-w-xl">
                {{ currentCampaign.description }}
              </p>
            </div>

            <!-- Countdown Timer -->
            <div v-if="campaignCountdown" class="flex items-center gap-3 md:gap-4">
              <div 
                v-for="(unit, key) in countdownUnits" 
                :key="key"
                class="text-center"
              >
                <div class="bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2 md:px-4 md:py-3 min-w-[60px]">
                  <span class="text-2xl md:text-3xl font-bold text-white">
                    {{ String(campaignCountdown[key]).padStart(2, '0') }}
                  </span>
                </div>
                <span class="text-xs text-white/70 mt-1 block">{{ unit }}</span>
              </div>
            </div>
          </div>

          <!-- CTA Button -->
          <div class="mt-4">
            <button 
              @click="goToCampaign(currentCampaign)"
              class="inline-flex items-center gap-2 px-6 py-3 bg-white text-indigo-600 font-semibold rounded-lg shadow-lg hover:bg-slate-50 transition-all"
            >
              {{ t('campaign.shopNow') }}
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="absolute bottom-0 left-0 w-full h-1 bg-black/20">
          <div 
            class="h-full bg-white/60 transition-all duration-100 ease-linear"
            :style="{ width: campaignProgress + '%' }"
          ></div>
        </div>

        <!-- Navigation Arrows -->
        <template v-if="campaigns.length > 1">
          <button 
            @click="prevCampaign"
            class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-colors"
            :aria-label="t('common.previous')"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <button 
            @click="nextCampaign"
            class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/30 hover:bg-black/50 rounded-full flex items-center justify-center text-white transition-colors"
            :aria-label="t('common.next')"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </template>

        <!-- Dot Indicators -->
        <div v-if="campaigns.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
          <button
            v-for="(_, idx) in campaigns"
            :key="idx"
            @click="goToCampaignSlide(idx)"
            class="w-2.5 h-2.5 rounded-full transition-all"
            :class="idx === currentCampaignIndex 
              ? 'bg-white w-6' 
              : 'bg-white/40 hover:bg-white/60'"
            :aria-label="`Kampanya ${idx + 1}`"
          />
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

      <!-- ═══════════════════════════════════════════════════════════════════
           2. 📦 AKTİF SİPARİŞLER - Kritik Bilgi
           ═══════════════════════════════════════════════════════════════════ -->
      <section v-if="activeOrders.length > 0" class="active-orders">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <span class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
              📦
            </span>
            {{ t('order.activeOrders') }}
          </h2>
          <router-link 
            to="/orders" 
            class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
          >
            {{ t('common.viewAll') }} →
          </router-link>
        </div>

        <!-- Mobile: Swipe Tab / Desktop: Grid -->
        <div 
          class="orders-container overflow-x-auto pb-2 -mx-4 px-4 md:mx-0 md:px-0"
          ref="ordersContainer"
          @scroll="handleOrdersScroll"
        >
          <div class="flex gap-4 md:grid md:grid-cols-3 min-w-max md:min-w-0">
            <div 
              v-for="order in activeOrders.slice(0, 6)"
              :key="order.id"
              class="order-card flex-shrink-0 w-72 md:w-auto bg-white rounded-xl shadow-sm border border-slate-100 p-4 hover:shadow-md transition-shadow"
            >
              <div class="flex gap-3">
                <!-- Product Image -->
                <div class="relative">
                  <img 
                    :src="order.product?.image || '/placeholder.jpg'" 
                    :alt="order.product?.title"
                    class="w-16 h-16 rounded-lg object-cover"
                    loading="lazy"
                  />
                  <span 
                    v-if="order.itemCount > 1"
                    class="absolute -top-1 -right-1 w-5 h-5 bg-indigo-600 text-white text-xs rounded-full flex items-center justify-center"
                  >
                    +{{ order.itemCount - 1 }}
                  </span>
                </div>

                <!-- Order Info -->
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-slate-900 truncate">
                    {{ order.product?.title || `Sipariş #${order.id}` }}
                  </p>
                  <p class="text-sm text-slate-500">
                    {{ formatDate(order.createdAt) }}
                  </p>
                  
                  <!-- Status Badge -->
                  <div class="mt-2 flex items-center gap-2">
                    <span 
                      class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium"
                      :class="getOrderStatusClass(order.status)"
                    >
                      <span class="w-1.5 h-1.5 rounded-full" :class="getOrderStatusDotClass(order.status)"></span>
                      {{ getOrderStatusText(order.status) }}
                    </span>
                  </div>
                </div>

                <!-- Track Button -->
                <button 
                  @click="trackOrder(order)"
                  class="self-start p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                  :aria-label="t('order.track')"
                >
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                </button>
              </div>

              <!-- Delivery Progress -->
              <div v-if="order.deliveryProgress" class="mt-3">
                <div class="flex justify-between text-xs text-slate-500 mb-1">
                  <span>{{ t('order.delivery') }}</span>
                  <span>{{ order.deliveryProgress }}%</span>
                </div>
                <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                  <div 
                    class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all"
                    :style="{ width: order.deliveryProgress + '%' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Scroll Indicator (Mobile) -->
        <div class="flex justify-center gap-1.5 mt-3 md:hidden">
          <span 
            v-for="i in Math.ceil(activeOrders.length / 2)"
            :key="i"
            class="w-1.5 h-1.5 rounded-full transition-colors"
            :class="currentOrderPage === i - 1 ? 'bg-indigo-600' : 'bg-slate-300'"
          />
        </div>
      </section>

      <!-- ═══════════════════════════════════════════════════════════════════
           3. 🎯 ANA CTA ALANI - Sepete Ekle, Bundle Teklifleri
           ═══════════════════════════════════════════════════════════════════ -->
      <section class="main-cta bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-2xl p-6 md:p-8 text-white">
        <div class="text-center max-w-2xl mx-auto">
          <h2 class="text-2xl md:text-3xl font-bold mb-3">
            {{ t('cta.specialOffers') }} 🎁
          </h2>
          <p class="text-white/80 mb-6">
            {{ t('cta.specialOffersDesc') }}
          </p>
          
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button 
              @click="scrollToBundles"
              class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-indigo-600 font-bold rounded-xl shadow-lg hover:bg-slate-50 transition-all transform hover:scale-105"
            >
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
              </svg>
              {{ t('cta.viewBundles') }}
            </button>
            
            <router-link 
              to="/products"
              class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/20 backdrop-blur-sm text-white font-bold rounded-xl hover:bg-white/30 transition-all"
            >
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
              {{ t('cta.browseProducts') }}
            </router-link>
          </div>
        </div>

        <!-- Floating Elements -->
        <div class="absolute top-4 left-4 text-4xl opacity-20 animate-bounce">🏀</div>
        <div class="absolute bottom-4 right-4 text-4xl opacity-20 animate-bounce delay-300">⚽</div>
      </section>

      <!-- ═══════════════════════════════════════════════════════════════════
           4. 🛒 BUNDLE TEKLİFLERİ - Grid Layout
           ═══════════════════════════════════════════════════════════════════ -->
      <section ref="bundlesSection" class="bundle-offers">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <span class="w-10 h-10 bg-gradient-to-br from-orange-400 to-pink-500 rounded-xl flex items-center justify-center text-white">
              🎁
            </span>
            {{ t('product.bundleOffers') }}
          </h2>
          <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-red-100 text-red-600 text-sm font-medium rounded-full">
              {{ t('common.limitedTime') }}
            </span>
            <div class="hidden md:flex gap-2">
              <button 
                @click="scrollBundles('left')"
                class="p-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 transition-colors"
              >
                <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
              </button>
              <button 
                @click="scrollBundles('right')"
                class="p-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 transition-colors"
              >
                <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loadingBundles" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="n in 4" :key="n" class="animate-pulse">
            <div class="bg-white rounded-2xl p-6 h-48">
              <div class="flex gap-4">
                <div class="w-24 h-24 bg-slate-200 rounded-xl"></div>
                <div class="flex-1 space-y-3">
                  <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                  <div class="h-4 bg-slate-200 rounded w-1/2"></div>
                  <div class="h-8 bg-slate-200 rounded w-full mt-4"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bundle Cards - Horizontal Carousel -->
        <div 
          v-else-if="bundles.length > 0" 
          ref="bundleCarousel"
          class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-hide"
          style="scroll-behavior: smooth;"
        >
          <div 
            v-for="bundle in bundles"
            :key="bundle.id"
            class="bundle-card flex-shrink-0 w-[320px] md:w-[380px] snap-start bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-all group"
          >
            <!-- Bundle Header -->
            <div class="relative bg-gradient-to-r from-indigo-50 to-purple-50 p-4">
              <span class="absolute top-3 right-3 px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                %{{ bundle.discount }} {{ t('product.discount') }}
              </span>
              
              <div class="flex items-center gap-3">
                <img 
                  :src="bundle.main?.image" 
                  :alt="bundle.main?.title"
                  class="w-20 h-20 rounded-xl object-cover shadow-md group-hover:scale-105 transition-transform"
                  loading="lazy"
                />
                <div class="text-2xl font-bold text-indigo-600">+</div>
                <img 
                  :src="bundle.extra?.image" 
                  :alt="bundle.extra?.title"
                  class="w-20 h-20 rounded-xl object-cover shadow-md group-hover:scale-105 transition-transform"
                  loading="lazy"
                />
              </div>
            </div>

            <!-- Bundle Content -->
            <div class="p-4">
              <h3 class="font-semibold text-slate-900 mb-1">
                {{ bundle.main?.title }} + {{ bundle.extra?.title }}
              </h3>
              <p class="text-sm text-slate-500 mb-3">
                {{ bundle.description || t('bundle.saveMoreTogether') }}
              </p>

              <div class="flex items-center justify-between">
                <div>
                  <span class="text-2xl font-bold text-indigo-600">
                    {{ formatCurrency(bundle.totalPrice) }}
                  </span>
                  <span class="text-sm text-slate-400 line-through ml-2">
                    {{ formatCurrency(bundle.originalPrice) }}
                  </span>
                </div>
                
                <button 
                  @click="addBundleToCart(bundle)"
                  :disabled="addingBundleId === bundle.id"
                  class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                >
                  <svg v-if="addingBundleId === bundle.id" class="w-4 h-4 animate-spin" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                  {{ t('product.addBundle') }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12 bg-white rounded-2xl">
          <div class="text-4xl mb-3">📦</div>
          <p class="text-slate-500">{{ t('bundle.noBundles') }}</p>
        </div>
      </section>

    </main>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="fixed bottom-0 left-0 right-0 md:hidden z-50 bg-white border-t border-slate-200 safe-area-bottom">
      <div class="flex items-center justify-around h-16">
        <!-- Home -->
        <router-link 
          to="/"
          class="flex flex-col items-center justify-center flex-1 py-2 transition-colors"
          :class="$route.path === '/' ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          <span class="text-xs mt-1 font-medium">Ana Sayfa</span>
        </router-link>

        <!-- Search -->
        <button 
          @click="showMobileSearch = true"
          class="flex flex-col items-center justify-center flex-1 py-2 text-slate-400 hover:text-slate-600 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <span class="text-xs mt-1 font-medium">Ara</span>
        </button>

        <!-- Cart (Center - Larger) -->
        <router-link 
          to="/cart"
          class="relative flex flex-col items-center justify-center flex-1 py-2"
          :class="$route.path === '/cart' ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'"
        >
          <div class="relative">
            <div class="w-12 h-12 -mt-6 bg-indigo-600 rounded-full flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform">
              <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <span 
              v-if="cartItemCount > 0"
              class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse"
            >
              {{ cartItemCount > 9 ? '9+' : cartItemCount }}
            </span>
          </div>
          <span class="text-xs mt-1 font-medium">Sepet</span>
        </router-link>

        <!-- Orders -->
        <router-link 
          to="/orders"
          class="flex flex-col items-center justify-center flex-1 py-2 transition-colors"
          :class="$route.path.startsWith('/orders') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
          </svg>
          <span class="text-xs mt-1 font-medium">Siparişler</span>
        </router-link>

        <!-- Profile -->
        <router-link 
          to="/user"
          class="flex flex-col items-center justify-center flex-1 py-2 transition-colors"
          :class="$route.path === '/user' ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          <span class="text-xs mt-1 font-medium">Profil</span>
        </router-link>
      </div>
    </nav>

    <!-- Mobile Search Modal -->
    <Transition name="fade">
      <div 
        v-if="showMobileSearch" 
        class="fixed inset-0 z-50 bg-black/50 md:hidden"
        @click="showMobileSearch = false"
      >
        <div 
          class="absolute top-0 left-0 right-0 bg-white p-4 safe-area-top"
          @click.stop
        >
          <div class="flex items-center gap-3">
            <div class="flex-1 relative">
              <input
                ref="mobileSearchInput"
                v-model="searchQuery"
                type="text"
                :placeholder="t('search.placeholder')"
                class="w-full pl-10 pr-4 py-3 bg-slate-100 border-0 rounded-xl text-base focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all"
                @keyup.enter="performSearch; showMobileSearch = false"
                autofocus
              />
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
            <button 
              @click="showMobileSearch = false"
              class="px-4 py-3 text-slate-600 font-medium"
            >
              İptal
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Spacer for bottom navigation -->
    <div class="h-16 md:hidden"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

// Simple i18n helpers
const formatCurrency = (amount: number) => `₺${amount.toFixed(2)}`
const formatDate = (date: string) => new Date(date).toLocaleDateString('tr-TR')
const t = (key: string) => key // Simplified translation

// Types
interface Campaign {
  id: number
  title: string
  description: string
  endDate?: string
  colorFrom?: string
  colorTo?: string
  link?: string
}

interface Order {
  id: number
  status: 'pending' | 'confirmed' | 'processing' | 'shipped' | 'delivered'
  createdAt: string
  product?: { title: string; image: string }
  itemCount: number
  deliveryProgress?: number
}

interface Bundle {
  id: number
  main: { title: string; image: string; price: number }
  extra: { title: string; image: string; price: number }
  totalPrice: number
  originalPrice: number
  discount: number
  description?: string
}

// Removed: Review, Product, Restaurant, Hotel interfaces - sections removed from template

// Composables
const router = useRouter()

// ═══════════════════════════════════════════════════════════════════
// State
// ═══════════════════════════════════════════════════════════════════

// Header & Auth
const searchQuery = ref('')
const isAuthenticated = ref(false) // TODO: Connect to auth store
const userInitials = ref('MK')
const voiceAssistantActive = ref(false)
const showLanguageMenu = ref(false)
const currentLanguage = ref('tr')

// Languages
const languages = [
  { code: 'tr', name: 'Türkçe', flag: '🇹🇷' },
  { code: 'en', name: 'English', flag: '🇬🇧' },
  { code: 'de', name: 'Deutsch', flag: '🇩🇪' },
  { code: 'ar', name: 'العربية', flag: '🇸🇦' }
]

const currentLanguageFlag = computed(() => languages.find(l => l.code === currentLanguage.value)?.flag || '🌐')
const currentLanguageCode = computed(() => currentLanguage.value.toUpperCase())

// Main Services
const activeService = ref('marketplace')
const mainServices = ref([
  { id: 'marketplace', name: 'Mağaza', icon: '🛒', badge: null, badgeClass: '' },
  { id: 'food', name: 'Yemek', icon: '🍔', badge: 'Yeni', badgeClass: 'bg-green-100 text-green-600' },
  { id: 'hotel', name: 'Otel', icon: '🏨', badge: null, badgeClass: '' },
  { id: 'rides', name: 'Ulaşım', icon: '🚗', badge: null, badgeClass: '' },
  { id: 'services', name: 'Hizmet', icon: '🔧', badge: null, badgeClass: '' },
  { id: 'career', name: 'Kariyer', icon: '💼', badge: '12', badgeClass: 'bg-blue-100 text-blue-600' }
])

// Premium Features
const premiumFeatures = ref([
  { id: 'kitchen', name: 'Sporcu Mutfağı', icon: '🥗', isNew: true },
  { id: 'virtual-try', name: 'Sanal Deneme Kabini', icon: '👕', isNew: true },
  { id: 'ai-suggest', name: 'AI Öneri Motoru', icon: '🤖', isNew: false },
  { id: 'nutrition', name: 'Beslenme Danışmanı', icon: '🥑', isNew: false },
  { id: 'trainer', name: 'Kişisel Antrenör', icon: '🏋️', isNew: false },
  { id: 'live-support', name: 'Canlı Destek', icon: '💬', isNew: false }
])

// Announcements (Duyurular)
const announcements = ref([
  '🎉 Yeni Yıl Kampanyası: Tüm ürünlerde %70\'e varan indirimler başladı!',
  '⚡ Flash Sale: Elektronik kategorisinde bugün sınırlı sayıda ürünlerde ekstra %30 indirim',
  '🚚 Ücretsiz Kargo: 500 TL ve üzeri tüm siparişlerde kargo bedava',
  '💳 Taksit Fırsatı: Kredi kartına 12 taksit imkanı',
  '🎁 Hediye Çeki: Her 1000 TL\'lik alışverişe 100 TL hediye çeki kazanın'
])

// Hero Content
const heroContent = ref({
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

// Live Metrics
const liveMetrics = ref([
  { icon: '👥', label: 'Aktif Kullanıcı', value: '12,847', trend: '+8.2%', trendUp: true },
  { icon: '🛒', label: 'Sepetteki Ürün', value: '4,231', trend: '+12%', trendUp: true },
  { icon: '📦', label: 'Günlük Sipariş', value: '892', trend: '+5.1%', trendUp: true },
  { icon: '⭐', label: 'Ortalama Puan', value: '4.8/5', trend: '+0.2', trendUp: true }
])

// Campaigns
const campaigns = ref<Campaign[]>([])
const currentCampaignIndex = ref(0)
const campaignCountdown = ref<{ days: number; hours: number; minutes: number; seconds: number } | null>(null)
const campaignProgress = ref(0)
let campaignAutoPlayInterval: ReturnType<typeof setInterval> | null = null
let campaignProgressInterval: ReturnType<typeof setInterval> | null = null
let campaignCountdownInterval: ReturnType<typeof setInterval> | null = null
let campaignTouchStartX = 0

// Orders
const activeOrders = ref<Order[]>([])
const currentOrderPage = ref(0)
const ordersContainer = ref<HTMLElement | null>(null)

// Bundles - Mock data ile başlat
const bundles = ref<Bundle[]>([
  {
    id: 1,
    discount: 15,
    main: { image: '/images/products/marshall-kulaklik.jpg', title: 'Marshall Kulaklık' },
    extra: { image: '/images/products/yoga-mati.jpg', title: 'Yoga Matı' },
    description: 'Müzik ve spor birlikte!',
    totalPrice: 2420,
    originalPrice: 2848
  },
  {
    id: 2,
    discount: 20,
    main: { image: '/images/products/protein-tozu.jpg', title: 'Protein Tozu' },
    extra: { image: '/images/products/fistoq.jpg', title: 'Fıstık Ezmesi' },
    description: 'Sağlıklı beslenme paketi',
    totalPrice: 870,
    originalPrice: 1088
  }
])
const loadingBundles = ref(false)
const addingBundleId = ref<number | null>(null)
const bundlesSection = ref<HTMLElement | null>(null)
const bundleCarousel = ref<HTMLElement | null>(null)

// Mobile Bottom Navigation
const showMobileSearch = ref(false)
const mobileSearchInput = ref<HTMLInputElement | null>(null)

// Cart
const cartItemCount = ref(0)

// ═══════════════════════════════════════════════════════════════════
// Computed
// ═══════════════════════════════════════════════════════════════════

const currentCampaign = computed(() => campaigns.value[currentCampaignIndex.value] || {})

const currentCampaignGradient = computed(() => {
  const campaign = currentCampaign.value
  const from = campaign.colorFrom || '#4f46e5'
  const to = campaign.colorTo || '#7c3aed'
  return { background: `linear-gradient(135deg, ${from}, ${to})` }
})

const countdownUnits = computed(() => ({
  days: t('time.days'),
  hours: t('time.hours'),
  minutes: t('time.minutes'),
  seconds: t('time.seconds')
}))

// ═══════════════════════════════════════════════════════════════════
// Methods
// ═══════════════════════════════════════════════════════════════════

// Header Methods
const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ path: '/search', query: { q: searchQuery.value } })
  }
}

const toggleVoiceAssistant = () => {
  voiceAssistantActive.value = !voiceAssistantActive.value
  if (voiceAssistantActive.value) {
    // TODO: Start voice recognition
    console.log('Voice assistant activated')
  }
}

const changeLanguage = (code: string) => {
  currentLanguage.value = code
  showLanguageMenu.value = false
  // TODO: Update i18n locale
}

const selectService = (serviceId: string) => {
  activeService.value = serviceId
  
  // Navigate to service page
  const serviceRoutes: Record<string, string> = {
    marketplace: '/',
    food: '/food',
    hotel: '/hotels',
    rides: '/rides',
    services: '/services',
    career: '/career'
  }
  
  if (serviceRoutes[serviceId] && serviceId !== 'marketplace') {
    router.push(serviceRoutes[serviceId])
  }
}

const openFeature = (featureId: string) => {
  const featureRoutes: Record<string, string> = {
    'kitchen': '/sport-kitchen',
    'virtual-try': '/virtual-fitting',
    'ai-suggest': '/ai-recommendations',
    'nutrition': '/nutrition-consultant',
    'trainer': '/personal-trainer',
    'live-support': '/support'
  }
  
  if (featureRoutes[featureId]) {
    router.push(featureRoutes[featureId])
  }
}

// Admin navigation
const goToAdmin = () => {
  const userRole = localStorage.getItem('role')
  if (userRole === 'admin') {
    router.push('/admin/marketplace')
  } else {
    alert('Admin erişimi gereklidir')
  }
}

// Campaign Methods
const startCampaignAutoPlay = () => {
  if (campaigns.value.length <= 1) return
  
  pauseCampaignAutoPlay()
  campaignProgress.value = 0

  campaignAutoPlayInterval = setInterval(() => {
    nextCampaign()
  }, 5000)

  campaignProgressInterval = setInterval(() => {
    if (campaignProgress.value < 100) {
      campaignProgress.value += 2
    }
  }, 100)
}

const pauseCampaignAutoPlay = () => {
  if (campaignAutoPlayInterval) clearInterval(campaignAutoPlayInterval)
  if (campaignProgressInterval) clearInterval(campaignProgressInterval)
}

const resumeCampaignAutoPlay = () => {
  if (campaigns.value.length > 1) {
    startCampaignAutoPlay()
  }
}

const nextCampaign = () => {
  currentCampaignIndex.value = (currentCampaignIndex.value + 1) % campaigns.value.length
  campaignProgress.value = 0
  updateCampaignCountdown()
}

const prevCampaign = () => {
  currentCampaignIndex.value = (currentCampaignIndex.value - 1 + campaigns.value.length) % campaigns.value.length
  campaignProgress.value = 0
  updateCampaignCountdown()
}

const goToCampaignSlide = (index: number) => {
  currentCampaignIndex.value = index
  campaignProgress.value = 0
  updateCampaignCountdown()
}

const goToCampaign = (campaign: Campaign) => {
  if (campaign.link) {
    router.push(campaign.link)
  } else {
    router.push(`/campaigns/${campaign.id}`)
  }
}

const updateCampaignCountdown = () => {
  if (campaignCountdownInterval) clearInterval(campaignCountdownInterval)
  
  const campaign = currentCampaign.value
  if (!campaign.endDate) {
    campaignCountdown.value = null
    return
  }

  const endDate = new Date(campaign.endDate)
  
  campaignCountdownInterval = setInterval(() => {
    const now = new Date()
    const diff = endDate.getTime() - now.getTime()
    
    if (diff <= 0) {
      campaignCountdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 }
      if (campaignCountdownInterval) clearInterval(campaignCountdownInterval)
      return
    }
    
    campaignCountdown.value = {
      days: Math.floor(diff / (1000 * 60 * 60 * 24)),
      hours: Math.floor((diff / (1000 * 60 * 60)) % 24),
      minutes: Math.floor((diff / (1000 * 60)) % 60),
      seconds: Math.floor((diff / 1000) % 60)
    }
  }, 1000)
}

const handleCampaignTouchStart = (e: TouchEvent) => {
  campaignTouchStartX = e.changedTouches[0].clientX
}

const handleCampaignTouchEnd = (e: TouchEvent) => {
  const touchEndX = e.changedTouches[0].clientX
  const diff = touchEndX - campaignTouchStartX
  
  if (diff < -50) nextCampaign()
  else if (diff > 50) prevCampaign()
}

// Order Methods
const handleOrdersScroll = () => {
  if (!ordersContainer.value) return
  const scrollLeft = ordersContainer.value.scrollLeft
  const cardWidth = 288 + 16 // card width + gap
  currentOrderPage.value = Math.round(scrollLeft / (cardWidth * 2))
}

const getOrderStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-700',
    confirmed: 'bg-blue-100 text-blue-700',
    processing: 'bg-purple-100 text-purple-700',
    shipped: 'bg-indigo-100 text-indigo-700',
    delivered: 'bg-green-100 text-green-700'
  }
  return classes[status] || 'bg-slate-100 text-slate-700'
}

const getOrderStatusDotClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-500',
    confirmed: 'bg-blue-500',
    processing: 'bg-purple-500',
    shipped: 'bg-indigo-500',
    delivered: 'bg-green-500'
  }
  return classes[status] || 'bg-slate-500'
}

const getOrderStatusText = (status: string) => {
  return t(`order.statuses.${status}`)
}

const trackOrder = (order: Order) => {
  router.push(`/orders/${order.id}/track`)
}

// Bundle Methods
const scrollToBundles = () => {
  bundlesSection.value?.scrollIntoView({ behavior: 'smooth' })
}

const scrollBundles = (direction: 'left' | 'right') => {
  if (!bundleCarousel.value) return
  const scrollAmount = 400
  bundleCarousel.value.scrollBy({
    left: direction === 'right' ? scrollAmount : -scrollAmount,
    behavior: 'smooth'
  })
}

const addBundleToCart = async (bundle: Bundle) => {
  addingBundleId.value = bundle.id
  try {
    await fetch('/api/cart/bundle', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ bundle_id: bundle.id })
    })
    cartItemCount.value += 2
    // Show success toast
  } catch (error) {
    console.error('Bundle eklenemedi:', error)
  } finally {
    addingBundleId.value = null
  }
}

// ═══════════════════════════════════════════════════════════════════
// Data Fetching
// ═══════════════════════════════════════════════════════════════════

const loadCampaigns = async () => {
  try {
    const response = await fetch('/api/campaigns/active')
    campaigns.value = response.ok ? await response.json() : []
    if (campaigns.value.length > 0) {
      updateCampaignCountdown()
      startCampaignAutoPlay()
    }
  } catch (error) {
    console.error('Kampanyalar yüklenemedi:', error)
  }
}

const loadActiveOrders = async () => {
  try {
    const response = await fetch('/api/orders/active')
    activeOrders.value = response.ok ? await response.json() : []
  } catch (error) {
    console.error('Siparişler yüklenemedi:', error)
  }
}

const loadBundles = async () => {
  // Mock data zaten yüklendi, API çağrısı isteğe bağlı
  loadingBundles.value = false
  // try {
  //   const data = await fetchWithCache<Bundle[]>('/api/bundles/featured')
  //   if (data && data.length > 0) {
  //     bundles.value = data
  //   }
  // } catch (error) {
  //   console.error('Bundle teklifleri yüklenemedi, mock data kullanılıyor:', error)
  // } finally {
  //   loadingBundles.value = false
  // }
}

const loadCartCount = async () => {
  try {
    const response = await fetch('/api/cart/count')
    const data = response.ok ? await response.json() : null
    cartItemCount.value = data?.count || 0
  } catch (error) {
    console.error('Sepet sayısı alınamadı:', error)
  }
}

// ═══════════════════════════════════════════════════════════════════
// Lifecycle
// ═══════════════════════════════════════════════════════════════════

onMounted(async () => {
  // Load all data in parallel
  await Promise.all([
    loadCampaigns(),
    loadActiveOrders(),
    loadBundles(),
    loadCartCount()
  ])
})

onUnmounted(() => {
  pauseCampaignAutoPlay()
  if (campaignCountdownInterval) clearInterval(campaignCountdownInterval)
})
</script>

<style scoped>
/* Smooth scrolling for orders container */
.orders-container {
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}

.orders-container::-webkit-scrollbar {
  display: none;
}

.order-card {
  scroll-snap-align: start;
}

/* Hide scrollbar for bundle carousel */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Animation delays */
.delay-300 {
  animation-delay: 300ms;
}

/* Gradient text */
.gradient-text {
  background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Line clamp for reviews */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Dropdown animation */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* Fade animation for mobile search */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Safe area for iPhone notch */
.safe-area-top {
  padding-top: env(safe-area-inset-top);
}

.safe-area-bottom {
  padding-bottom: env(safe-area-inset-bottom);
}

/* Touch scroll optimization */
.touch-pan-x {
  touch-action: pan-x;
}

/* Skeleton animation */
@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

/* Marquee animation for announcements */
@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.animate-marquee {
  animation: marquee 30s linear infinite;
}

.animate-marquee:hover {
  animation-play-state: paused;
}

/* Smooth image loading */
img {
  opacity: 1;
  transition: opacity 0.3s ease;
}

img[loading="lazy"] {
  opacity: 0;
}

img[loading="lazy"].loaded {
  opacity: 1;
}

/* Bottom navigation shadow */
nav.fixed.bottom-0 {
  box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
}

/* Main content padding for bottom nav */
main {
  padding-bottom: 1rem;
}

@media (max-width: 768px) {
  main {
    padding-bottom: 5rem;
  }
}
</style>
