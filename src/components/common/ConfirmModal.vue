<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="modelValue" class="modal-overlay" @click.self="cancel">
        <div class="modal-container">
          <div class="modal-content">
            <!-- Icon -->
            <div v-if="type" class="modal-icon" :class="`modal-icon-${type}`">
              <svg v-if="type === 'danger'" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <svg v-else-if="type === 'warning'" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <svg v-else-if="type === 'info'" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <svg v-else-if="type === 'success'" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            
            <!-- Title -->
            <h3 class="modal-title">{{ title }}</h3>
            
            <!-- Message -->
            <p class="modal-message">{{ message }}</p>
            
            <!-- Buttons -->
            <div class="modal-actions">
              <button 
                type="button" 
                class="btn btn-cancel" 
                @click="cancel"
              >
                {{ cancelText }}
              </button>
              <button 
                type="button" 
                class="btn btn-confirm" 
                :class="`btn-${type}`"
                @click="confirm"
              >
                {{ confirmText }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
interface Props {
  modelValue: boolean
  title?: string
  message: string
  confirmText?: string
  cancelText?: string
  type?: 'danger' | 'warning' | 'info' | 'success'
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Onay',
  confirmText: 'Evet',
  cancelText: 'Hayır',
  type: 'warning'
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  'confirm': []
  'cancel': []
}>()

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
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

.modal-container {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-width: 28rem;
  width: 100%;
}

.modal-content {
  padding: 1.5rem;
  text-align: center;
}

.modal-icon {
  margin: 0 auto 1rem;
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-icon-danger {
  background-color: #fee2e2;
  color: #dc2626;
}

.modal-icon-warning {
  background-color: #fef3c7;
  color: #d97706;
}

.modal-icon-info {
  background-color: #dbeafe;
  color: #2563eb;
}

.modal-icon-success {
  background-color: #d1fae5;
  color: #059669;
}

.icon {
  width: 1.5rem;
  height: 1.5rem;
}

.modal-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.5rem;
}

.modal-message {
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 1.5rem;
}

.modal-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: center;
}

.btn {
  padding: 0.5rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  font-size: 0.875rem;
  border: 1px solid;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel {
  background: white;
  border-color: #d1d5db;
  color: #374151;
}

.btn-cancel:hover {
  background-color: #f9fafb;
}

.btn-confirm {
  color: white;
}

.btn-danger {
  background-color: #dc2626;
  border-color: #dc2626;
}

.btn-danger:hover {
  background-color: #b91c1c;
}

.btn-warning {
  background-color: #d97706;
  border-color: #d97706;
}

.btn-warning:hover {
  background-color: #b45309;
}

.btn-info {
  background-color: #2563eb;
  border-color: #2563eb;
}

.btn-info:hover {
  background-color: #1d4ed8;
}

.btn-success {
  background-color: #059669;
  border-color: #059669;
}

.btn-success:hover {
  background-color: #047857;
}

/* Transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-container,
.modal-leave-active .modal-container {
  transition: transform 0.3s ease;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
  transform: scale(0.9);
}
</style>
