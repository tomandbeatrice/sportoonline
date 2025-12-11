<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        @click.self="cancel"
      >
        <div
          class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
          @click.stop
        >
          <!-- Header -->
          <div :class="['px-6 py-4', headerClass]">
            <div class="flex items-center gap-3">
              <div :class="['w-12 h-12 rounded-full flex items-center justify-center', iconClass]">
                <slot name="icon">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                    />
                  </svg>
                </slot>
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900">
                  {{ title }}
                </h3>
              </div>
            </div>
          </div>

          <!-- Body -->
          <div class="px-6 py-4">
            <p class="text-gray-600">
              <slot>{{ message }}</slot>
            </p>
          </div>

          <!-- Footer -->
          <div class="px-6 py-4 bg-gray-50 flex gap-3 justify-end">
            <button
              @click="cancel"
              class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 text-gray-700 font-medium transition-colors"
            >
              {{ cancelText }}
            </button>
            <button
              @click="confirm"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-colors',
                confirmClass
              ]"
            >
              {{ confirmText }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  modelValue: boolean
  title?: string
  message?: string
  confirmText?: string
  cancelText?: string
  type?: 'warning' | 'danger' | 'info' | 'success'
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Onay',
  message: 'Bu işlemi yapmak istediğinizden emin misiniz?',
  confirmText: 'Onayla',
  cancelText: 'İptal',
  type: 'warning'
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
  (e: 'confirm'): void
  (e: 'cancel'): void
}>()

const headerClass = computed(() => {
  const classes = {
    warning: 'bg-amber-50',
    danger: 'bg-red-50',
    info: 'bg-blue-50',
    success: 'bg-green-50'
  }
  return classes[props.type]
})

const iconClass = computed(() => {
  const classes = {
    warning: 'bg-amber-100 text-amber-600',
    danger: 'bg-red-100 text-red-600',
    info: 'bg-blue-100 text-blue-600',
    success: 'bg-green-100 text-green-600'
  }
  return classes[props.type]
})

const confirmClass = computed(() => {
  const classes = {
    warning: 'bg-amber-500 hover:bg-amber-600 text-white',
    danger: 'bg-red-500 hover:bg-red-600 text-white',
    info: 'bg-blue-500 hover:bg-blue-600 text-white',
    success: 'bg-green-500 hover:bg-green-600 text-white'
  }
  return classes[props.type]
})

const confirm = () => {
  emit('confirm')
  emit('update:modelValue', false)
}

const cancel = () => {
  emit('cancel')
  emit('update:modelValue', false)
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

.modal-enter-active > div,
.modal-leave-active > div {
  transition: transform 0.3s ease;
}

.modal-enter-from > div,
.modal-leave-to > div {
  transform: scale(0.9);
}
</style>
