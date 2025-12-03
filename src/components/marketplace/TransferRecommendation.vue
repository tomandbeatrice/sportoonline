<!-- 
  TransferRecommendation.vue - Cross-Promotion Component for Hotel -> Transfer flow
  Displays a recommendation card suggesting transfer service after hotel booking
-->
<template>
  <div 
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
    role="dialog"
    aria-modal="true"
    aria-labelledby="transfer-modal-title"
    @keydown.esc="handleDecline"
    @click.self="handleDecline"
  >
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all animate-scale-in">
      <!-- Header with icon -->
      <div class="bg-gradient-to-br from-blue-500 to-cyan-500 p-6 text-white">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-3xl" aria-hidden="true">
            🚗
          </div>
          <div>
            <h2 id="transfer-modal-title" class="text-2xl font-bold mb-1">Transfer Hizmeti</h2>
            <p class="text-white/90 text-sm">Seyahatinizi tamamlayın</p>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6 space-y-4">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0" aria-hidden="true">
            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-slate-900 mb-2">
              Otel rezervasyonunuz alındı. Transfer ister misiniz?
            </h3>
            <p class="text-slate-600 text-sm leading-relaxed">
              Havaalanı veya şehir merkezinden otelinize konforlu ve güvenli ulaşım için transfer hizmetimizi kullanın. 
              Rezervasyonunuza özel %15 indirim fırsatı!
            </p>
          </div>
        </div>

        <!-- Features -->
        <div class="grid grid-cols-2 gap-3 pt-2">
          <div class="flex items-center gap-2 text-sm text-slate-600">
            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>7/24 Hizmet</span>
          </div>
          <div class="flex items-center gap-2 text-sm text-slate-600">
            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Güvenli Sürüş</span>
          </div>
          <div class="flex items-center gap-2 text-sm text-slate-600">
            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Uygun Fiyat</span>
          </div>
          <div class="flex items-center gap-2 text-sm text-slate-600">
            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
            <span>Konforlu Araçlar</span>
          </div>
        </div>

        <!-- Discount Badge -->
        <div class="bg-gradient-to-r from-orange-50 to-red-50 border border-orange-200 rounded-xl p-3 flex items-center gap-3">
          <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0" aria-hidden="true">
            %15
          </div>
          <div>
            <p class="text-sm font-semibold text-orange-900">Özel İndirim!</p>
            <p class="text-xs text-orange-700">Otel rezervasyonuna özel transfer indirimi</p>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="p-6 pt-0 flex flex-col sm:flex-row gap-3">
        <button 
          @click="handleAccept"
          aria-label="Transfer hizmetini sepete ekle ve %15 indirimden faydalanın"
          class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-green-200 hover:from-green-600 hover:to-emerald-600 hover:shadow-xl hover:-translate-y-0.5 transition-all"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Evet, Transfer Ekle
        </button>
        <button 
          @click="handleDecline"
          aria-label="Transfer önerisini reddet ve modalı kapat"
          class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:border-slate-300 hover:bg-slate-50 transition-all"
        >
          Hayır, Teşekkürler
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Emits
const emit = defineEmits<{
  accept: []
  decline: []
}>()

// Methods
const handleAccept = () => {
  emit('accept')
}

const handleDecline = () => {
  emit('decline')
}
</script>

<style scoped>
/* Scale-in animation for modal */
@keyframes scale-in {
  0% {
    opacity: 0;
    transform: scale(0.9);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-scale-in {
  animation: scale-in 0.3s ease-out;
}
</style>
