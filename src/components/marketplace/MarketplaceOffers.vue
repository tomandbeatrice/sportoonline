<template>
  <!-- ═══════════════════════════════════════════════════════════════════
       3. 🎯 ÖZEL TEKLİFLER - Tablı Yapı
       ═══════════════════════════════════════════════════════════════════ -->
  <section class="special-offers bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 md:p-8 bg-slate-50/50 border-b border-slate-100">
      <div class="text-center max-w-2xl mx-auto mb-8">
        <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">
          {{ t('cta.specialOffers') }} 🎁
        </h2>
        <p class="text-slate-500">
          {{ t('cta.specialOffersDesc') }}
        </p>
      </div>

      <!-- Tabs -->
      <div class="flex justify-center gap-2 md:gap-4 overflow-x-auto pb-2 scrollbar-hide">
        <button 
          v-for="tab in offerTabs" 
          :key="tab.id"
          @click="activeOfferTab = tab.id"
          class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all whitespace-nowrap flex items-center gap-2"
          :class="activeOfferTab === tab.id 
            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200 scale-105' 
            : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
        >
          <span>{{ tab.icon }}</span>
          {{ tab.name }}
        </button>
      </div>
    </div>

    <!-- Content Area -->
    <div class="p-6 md:p-10">
      <Transition name="fade" mode="out-in">
        <div :key="activeOfferTab" class="grid md:grid-cols-2 gap-8 items-center">
          <!-- Text Content -->
          <div class="space-y-6 text-center md:text-left">
            <div>
              <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold mb-3">
                <span class="w-1.5 h-1.5 bg-red-600 rounded-full animate-pulse"></span>
                Sınırlı Süre
              </span>
              <h3 class="text-3xl md:text-4xl font-black text-slate-900 leading-tight">
                {{ currentOffer.title }}
              </h3>
            </div>
            
            <p class="text-lg text-slate-600 leading-relaxed">
              {{ currentOffer.description }}
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
              <button class="px-8 py-4 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all">
                {{ currentOffer.cta }}
              </button>
              <button class="px-8 py-4 border-2 border-slate-200 text-slate-700 font-bold rounded-xl hover:border-indigo-200 hover:bg-indigo-50 transition-all">
                Detayları Gör
              </button>
            </div>
          </div>

          <!-- Visual Content -->
          <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-[2rem] transform rotate-3 group-hover:rotate-6 transition-transform duration-500"></div>
            <div class="relative bg-white rounded-[2rem] p-8 shadow-xl border border-slate-100 transform group-hover:-translate-y-2 transition-transform duration-500 flex items-center justify-center min-h-[300px]">
              <span class="text-9xl filter drop-shadow-2xl transform group-hover:scale-110 transition-transform duration-500">
                {{ currentOffer.image }}
              </span>
              
              <!-- Floating Badge -->
              <div class="absolute -bottom-6 -right-6 bg-white p-4 rounded-2xl shadow-xl border border-slate-100 animate-bounce delay-700">
                <p class="text-xs text-slate-500 font-bold uppercase">İndirim</p>
                <p class="text-2xl font-black text-indigo-600">%{{ currentOffer.discount }}</p>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const activeOfferTab = ref('marketplace')
const offerTabs = [
  { id: 'marketplace', name: 'Mağaza', icon: '🛒' },
  { id: 'food', name: 'Yemek', icon: '🍔' },
  { id: 'hotel', name: 'Otel', icon: '🏨' },
  { id: 'rides', name: 'Ulaşım', icon: '🚗' },
  { id: 'career', name: 'Kariyer', icon: '💼' }
]

const specialOffers = {
  marketplace: {
    title: 'Elektronik Festivali',
    description: 'Seçili elektronik ürünlerde %40\'a varan indirimler sizi bekliyor. Kulaklıklardan akıllı saatlere kadar binlerce üründe geçerli.',
    image: '🎧',
    discount: 40,
    cta: 'Ürünleri İncele'
  },
  food: {
    title: 'Acıktıran Fırsatlar',
    description: 'Favori restoranlarınızda geçerli %50 indirim kuponu! İlk siparişinize özel ekstra indirimler.',
    image: '🍕',
    discount: 50,
    cta: 'Sipariş Ver'
  },
  hotel: {
    title: 'Erken Rezervasyon',
    description: 'Yaz tatilinizi şimdiden planlayın, %35 indirim kazanın. Ücretsiz iptal seçeneği ile.',
    image: '🏖️',
    discount: 35,
    cta: 'Otel Ara'
  },
  rides: {
    title: 'İlk Yolculuk Bedava',
    description: 'Şehir içi ulaşımda konforlu ve ekonomik çözüm. İlk yolculuğunuz bizden hediye!',
    image: '🚕',
    discount: 100,
    cta: 'Araç Çağır'
  },
  career: {
    title: 'Kariyer Fırsatları',
    description: 'Hayalindeki işe bir adım daha yaklaş. Premium üyelik ile başvurularını öne çıkar.',
    image: '🚀',
    discount: 20,
    cta: 'İlanlara Bak'
  }
}

const currentOffer = computed(() => specialOffers[activeOfferTab.value as keyof typeof specialOffers])
</script>

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
