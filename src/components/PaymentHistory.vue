<template>
  <section class="payment-history">
    <h2>Ödeme Geçmişi</h2>

    <button @click="fetchHistory" :disabled="loading">Yenile</button>

    <p v-if="errorMessage" class="error">Hata: {{ errorMessage }}</p>

    <table v-if="payments.length">
      <thead>
        <tr>
          <th>ID</th>
          <th>Sipariş</th>
          <th>Yöntem</th>
          <th>Tutar</th>
          <th>Durum</th>
          <th>Ödendiği Zaman</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in payments" :key="p.id">
          <td>{{ p.id }}</td>
          <td>{{ p.order_id }}</td>
          <td>{{ p.method }}</td>
          <td>{{ p.amount.toFixed(2) }} ₺</td>
          <td>{{ p.status }}</td>
          <td>{{ formatDate(p.paid_at) }}</td>
        </tr>
      </tbody>
    </table>

    <p v-else>Henüz ödeme kaydı bulunamadı.</p>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { usePayment } from '@/composables/usePayment'

const { payments, fetchHistory, loading, error } = usePayment()
fetchHistory()

const formatDate = (date: string | null) => {
  if (!date) return '—'
  return new Date(date).toLocaleString('tr-TR')
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
.payment-history {
  max-width: 800px;
  margin: auto;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}
th, td {
  padding: 0.5rem;
  border: 1px solid #ccc;
  text-align: left;
}
.error {
  color: red;
}
</style>