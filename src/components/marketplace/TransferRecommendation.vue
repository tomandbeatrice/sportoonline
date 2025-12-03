<template>
  <Transition name="modal">
    <div 
      v-if="show" 
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click="handleNo"
    >
      <div 
        class="bg-white rounded-2xl shadow-lg max-w-md w-full p-6 transform transition-all"
        @click.stop
      >
        <!-- Icon -->
        <div class="flex justify-center mb-4">
          <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-green-200">
            <span class="text-3xl">🚗</span>
          </div>
        </div>

        <!-- Title -->
        <h2 class="text-2xl font-bold text-slate-900 text-center mb-3">
          Otel Rezervasyonu Tamamlandı! ✅
        </h2>

        <!-- Message -->
        <p class="text-slate-600 text-center mb-6">
          Otel rezervasyonunuz alındı. Havalimanından otele transfer ister misiniz?
        </p>

        <!-- Benefits -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 mb-6">
          <ul class="space-y-2 text-sm text-slate-700">
            <li class="flex items-center gap-2">
              <span class="text-green-600">✓</span>
              <span>Konforlu ve güvenli ulaşım</span>
            </li>
            <li class="flex items-center gap-2">
              <span class="text-green-600">✓</span>
              <span>Özel transfer hizmeti</span>
            </li>
            <li class="flex items-center gap-2">
              <span class="text-green-600">✓</span>
              <span>Kapıdan kapıya hizmet</span>
            </li>
          </ul>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
          <button 
            @click="handleYes"
            class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 text-white font-bold rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 hover:shadow-xl hover:-translate-y-0.5 transition-all"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Evet, Transfer Ekle
          </button>
          <button 
            @click="handleNo"
            class="flex-1 px-6 py-3 border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:border-slate-300 hover:bg-slate-50 transition-all"
          >
            Hayır, Teşekkürler
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { watch, onBeforeUnmount } from 'vue'

// Props
interface Props {
  show: boolean
}

const props = defineProps<Props>()

// Emits
const emit = defineEmits<{
  close: []
  addTransfer: []
}>()

// Methods
const handleYes = () => {
  console.log('Transfer added to Global Cart')
  emit('addTransfer')
  emit('close')
}

const handleNo = () => {
  emit('close')
}

// Prevent body scroll when modal is open
watch(() => props.show, (isOpen) => {
  if (isOpen) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

// Cleanup body scroll lock on unmount
onBeforeUnmount(() => {
  document.body.style.overflow = ''
})
</script>

<style scoped>
/* Modal transition animations */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
  transition: transform 0.3s ease;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
  transform: scale(0.9);
}
</style>
