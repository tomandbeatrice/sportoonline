<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        <!-- Modal Card -->
        <div
          ref="modalRef"
          class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all"
          role="dialog"
          aria-modal="true"
          aria-labelledby="transfer-promo-title"
        >
          <!-- Header with gradient -->
          <div class="bg-gradient-to-r from-green-500 to-emerald-500 px-6 py-6 text-white">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                  <span class="text-2xl">🚗</span>
                </div>
                <h3 id="transfer-promo-title" class="text-xl font-bold">
                  Seyahatinizi Tamamlayın
                </h3>
              </div>
              <button
                @click="close"
                class="text-white/80 hover:text-white transition-colors"
                aria-label="Kapat"
              >
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <p class="text-white/90 text-sm">
              Otel rezervasyonunuz için özel transfer fırsatı!
            </p>
          </div>

          <!-- Body -->
          <div class="px-6 py-6">
            <div class="space-y-4">
              <!-- Success checkmark -->
              <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold text-slate-900">Otel Rezervasyonunuz Alındı</p>
                  <p class="text-sm text-slate-500">Rezervasyon onayı e-posta ile gönderildi</p>
                </div>
              </div>

              <!-- Transfer offer -->
              <div class="space-y-3">
                <p class="text-slate-700 leading-relaxed">
                  Otele ulaşım için <strong class="text-green-600">transfer (Yolculuk)</strong> ister misiniz?
                </p>
                
                <!-- Benefits -->
                <div class="space-y-2">
                  <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Konforlu ve güvenli ulaşım</span>
                  </div>
                  <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Havalimanı ve şehir içi transferler</span>
                  </div>
                  <div class="flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Özel indirimli fiyatlar</span>
                  </div>
                </div>

                <!-- Special offer badge -->
                <div class="inline-flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg">
                  <span class="text-lg">🎁</span>
                  <span class="text-sm font-medium text-amber-700">
                    Otel misafirlerimize %15 indirim!
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="flex flex-col gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
            <button
              @click="accept"
              class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-200 hover:shadow-xl hover:-translate-y-0.5"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Transfer Ekle
            </button>
            <button
              @click="close"
              class="w-full px-6 py-3 text-slate-600 font-medium rounded-xl hover:bg-slate-100 transition-colors"
            >
              Teşekkürler
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

interface Props {
  modelValue: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  accept: []
  close: []
}>()

const modalRef = ref<HTMLElement | null>(null)

// Prevent body scroll when modal is open
watch(() => props.modelValue, (isOpen) => {
  if (isOpen) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

const close = () => {
  emit('update:modelValue', false)
  emit('close')
}

const accept = () => {
  emit('accept')
  close()
}

const handleBackdropClick = () => {
  close()
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.95) translateY(20px);
}
</style>
