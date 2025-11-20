<template>
  <form @submit.prevent="submitPayment" class="payment-form">
    <div>
      <label>Sipariş ID</label>
      <input v-model="form.order_id" type="number" required />
    </div>

    <div>
      <label>Yöntem</label>
      <select v-model="form.method" required>
        <option value="stripe">Stripe</option>
        <option value="iyzico">Iyzico</option>
        <option value="test">Test</option>
      </select>
    </div>

    <div>
      <label>Tutar</label>
      <input v-model="form.amount" type="number" step="0.01" required />
    </div>

    <div>
      <label>Transaction ID (opsiyonel)</label>
      <input v-model="form.transaction_id" type="text" />
    </div>

    <button :disabled="loading">Ödemeyi Gönder</button>

    <p v-if="errorMessage" class="error">Hata: {{ errorMessage }}</p>
    <p v-if="response" class="success">Başarılı: Ödeme ID {{ response.payment_id }}</p>
  </form>
</template>

<script setup lang="ts">
import { reactive, computed } from 'vue'
import { usePayment } from '@/composables/usePayment'

type PaymentMethod = 'stripe' | 'iyzico' | 'test'

interface PaymentForm {
  order_id: number
  method: PaymentMethod
  amount: number
  transaction_id?: string
}

interface PaymentResponse {
  payment_id: number
  message?: string
}

const form = reactive<PaymentForm>({
  order_id: 0,
  method: 'stripe',
  amount: 0,
  transaction_id: ''
})

const {
  createPayment,
  loading,
  error,
  response: rawResponse
} = usePayment()

// Tip güvenli erişim için local ref tanımı
const response = computed(() => rawResponse.value as PaymentResponse | null)

const submitPayment = () => {
  createPayment(form)
}

const errorMessage = computed(() => {
  if (typeof error.value === 'string') return error.value
  if (error.value && typeof error.value === 'object' && 'message' in error.value) {
    return (error.value as any).message
  }
  return ''
})
</script>

<style scoped>
.payment-form {
  display: grid;
  gap: 1rem;
  max-width: 400px;
}
.error {
  color: red;
}
.success {
  color: green;
}
</style>