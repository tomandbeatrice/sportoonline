<template>
  <!-- Cross-Promotion Modal/Card: Hotel to Transfer -->
  <Transition name="modal-fade">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
      @click.self="handleDecline"
    >
      <div
        class="relative w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl"
        @click.stop
      >
        <!-- Decorative Background Elements -->
        <div class="pointer-events-none absolute inset-0">
          <div class="absolute -left-8 top-4 h-32 w-32 rounded-full bg-blue-500/10 blur-3xl"></div>
          <div class="absolute -right-8 bottom-4 h-32 w-32 rounded-full bg-green-500/10 blur-3xl"></div>
        </div>

        <!-- Content -->
        <div class="relative p-8">
          <!-- Success Icon -->
          <div class="mb-6 flex justify-center">
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 shadow-lg">
              <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
          </div>

          <!-- Success Message -->
          <div class="mb-6 text-center">
            <h2 class="mb-2 text-2xl font-black text-slate-900">
              🏨 Otel Rezervasyonu Alındı!
            </h2>
            <p class="text-slate-600">
              Rezervasyonunuz başarıyla oluşturuldu.
            </p>
          </div>

          <!-- Cross-Sell Offer -->
          <div class="mb-6 rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 p-6 text-center">
            <div class="mb-3 text-4xl">🚗</div>
            <h3 class="mb-2 text-lg font-bold text-slate-900">
              Otele ulaşım için transfer ister misiniz?
            </h3>
            <p class="text-sm text-slate-600">
              Konforlu ve güvenli transfer hizmeti ile otelinize rahatça ulaşın. Özel indirimli fiyatlarla!
            </p>
          </div>

          <!-- Benefits List -->
          <div class="mb-6 space-y-3">
            <div class="flex items-start gap-3">
              <div class="mt-0.5 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-green-100">
                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
              <div class="text-sm text-slate-700">
                <span class="font-semibold">7/24 Hizmet:</span> İstediğiniz saatte kapıdan kapıya ulaşım
              </div>
            </div>
            <div class="flex items-start gap-3">
              <div class="mt-0.5 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-green-100">
                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
              <div class="text-sm text-slate-700">
                <span class="font-semibold">Özel İndirim:</span> Otel rezervasyonu ile %15 indirim
              </div>
            </div>
            <div class="flex items-start gap-3">
              <div class="mt-0.5 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-green-100">
                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
              <div class="text-sm text-slate-700">
                <span class="font-semibold">Güvenli Yolculuk:</span> Sigortalı ve deneyimli şoförler
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col gap-3">
            <button
              @click="handleAccept"
              class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 px-6 py-4 text-sm font-bold text-white shadow-lg shadow-green-200 transition-all hover:from-green-600 hover:to-emerald-600 hover:shadow-xl hover:-translate-y-0.5"
            >
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              Evet, Transfer Ekle
            </button>
            <button
              @click="handleDecline"
              class="inline-flex w-full items-center justify-center gap-2 rounded-xl border-2 border-slate-200 px-6 py-4 text-sm font-semibold text-slate-700 transition-all hover:border-slate-300 hover:bg-slate-50"
            >
              Hayır, Teşekkürler
            </button>
          </div>

          <!-- Close Button -->
          <button
            @click="handleDecline"
            class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600"
            aria-label="Kapat"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { watch } from 'vue'

// Props
const props = defineProps<{
  show: boolean
}>()

// Emits
const emit = defineEmits<{
  accept: []
  decline: []
  close: []
}>()

// Methods
const handleAccept = () => {
  emit('accept')
  emit('close')
}

const handleDecline = () => {
  emit('decline')
  emit('close')
}

// Prevent body scroll when modal is open
watch(() => props.show, (newShow) => {
  if (newShow) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})
</script>

<style scoped>
/* Modal fade transition */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-active .relative,
.modal-fade-leave-active .relative {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-fade-enter-from .relative {
  transform: scale(0.95);
  opacity: 0;
}

.modal-fade-leave-to .relative {
  transform: scale(0.95);
  opacity: 0;
}
</style>
