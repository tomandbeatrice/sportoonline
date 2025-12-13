<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        <!-- Modal -->
        <div
          class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 transform transition-all"
          role="dialog"
          aria-modal="true"
          :aria-labelledby="titleId"
        >
          <!-- Icon -->
          <div
            v-if="type !== 'custom'"
            class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4"
            :class="iconBackgroundClass"
          >
            <span class="text-2xl">{{ icon }}</span>
          </div>

          <!-- Title -->
          <h3 :id="titleId" class="text-lg font-semibold text-gray-900 text-center mb-2">
            {{ title }}
          </h3>

          <!-- Message -->
          <div class="text-sm text-gray-600 text-center mb-6">
            {{ message }}
          </div>

          <!-- Actions -->
          <div class="flex gap-3">
            <button
              v-if="showCancel"
              type="button"
              class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
              @click="handleCancel"
            >
              {{ cancelText }}
            </button>
            <button
              type="button"
              class="flex-1 px-4 py-2 rounded-lg transition-colors"
              :class="confirmButtonClass"
              @click="handleConfirm"
              :autofocus="true"
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
import { computed } from "vue";

export interface ModalProps {
  isOpen: boolean;
  title: string;
  message: string;
  type?: "confirm" | "alert" | "warning" | "error" | "success" | "custom";
  confirmText?: string;
  cancelText?: string;
  showCancel?: boolean;
  closeOnBackdrop?: boolean;
}

const props = withDefaults(defineProps<ModalProps>(), {
  type: "confirm",
  confirmText: "Tamam",
  cancelText: "İptal",
  showCancel: true,
  closeOnBackdrop: true,
});

const emit = defineEmits<{
  (e: "confirm"): void;
  (e: "cancel"): void;
  (e: "update:isOpen", value: boolean): void;
}>();

const titleId = computed(() => `modal-title-${Math.random().toString(36).substr(2, 9)}`);

const icon = computed(() => {
  switch (props.type) {
    case "confirm":
      return "❓";
    case "alert":
      return "ℹ️";
    case "warning":
      return "⚠️";
    case "error":
      return "❌";
    case "success":
      return "✅";
    default:
      return "";
  }
});

const iconBackgroundClass = computed(() => {
  switch (props.type) {
    case "confirm":
      return "bg-blue-100";
    case "alert":
      return "bg-blue-100";
    case "warning":
      return "bg-yellow-100";
    case "error":
      return "bg-red-100";
    case "success":
      return "bg-green-100";
    default:
      return "bg-gray-100";
  }
});

const confirmButtonClass = computed(() => {
  switch (props.type) {
    case "error":
    case "warning":
      return "bg-red-600 text-white hover:bg-red-700";
    case "success":
      return "bg-green-600 text-white hover:bg-green-700";
    default:
      return "bg-blue-600 text-white hover:bg-blue-700";
  }
});

const handleConfirm = () => {
  emit("confirm");
  emit("update:isOpen", false);
};

const handleCancel = () => {
  emit("cancel");
  emit("update:isOpen", false);
};

const handleBackdropClick = () => {
  if (props.closeOnBackdrop) {
    handleCancel();
  }
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active > div,
.modal-leave-active > div {
  transition: transform 0.2s ease;
}

.modal-enter-from > div,
.modal-leave-to > div {
  transform: scale(0.95);
}
</style>
