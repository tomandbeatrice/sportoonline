<template>
  <div v-if="visible" class="toast">
    <strong>✅ Ödeme Başarılı</strong>
    <p>{{ message }}</p>
    <button @click="visible = false">Kapat</button>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { usePayment } from '@/composables/usePayment'

const { response } = usePayment()

const visible = ref(false)
const message = ref('')

watch(response, (val) => {
  if (val && val.payment_id) {
    visible.value = true
    message.value = `Ödeme ID ${val.payment_id} başarıyla alındı. Kampanya tetiklendi, admin bilgilendirildi.`
  }
})
</script>

<style scoped>
.toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: #e0ffe0;
  border: 1px solid #00aa00;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
button {
  margin-top: 0.5rem;
  background: transparent;
  border: none;
  color: #007700;
  cursor: pointer;
}
</style>