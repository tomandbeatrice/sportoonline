<template>
  <section class="admin-payment-logs">
    <h2>Ödeme Logları</h2>

    <form @submit.prevent="fetchLogs">
      <select v-model="filters.status">
        <option value="">Durum Seç</option>
        <option value="pending">Bekliyor</option>
        <option value="paid">Ödendi</option>
        <option value="failed">Başarısız</option>
      </select>

      <select v-model="filters.method">
        <option value="">Yöntem Seç</option>
        <option value="stripe">Stripe</option>
        <option value="iyzico">Iyzico</option>
        <option value="test">Test</option>
      </select>

      <input type="date" v-model="filters.date_from" />
      <input type="date" v-model="filters.date_to" />

      <button :disabled="loading">Filtrele</button>
    </form>

    <table v-if="logs.length">
      <thead>
        <tr>
          <th>ID</th>
          <th>Kullanıcı</th>
          <th>Sipariş</th>
          <th>Yöntem</th>
          <th>Tutar</th>
          <th>Durum</th>
          <th>Oluşturulma</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="log in logs" :key="log.id">
          <td>{{ log.id }}</td>
          <td>{{ log.user_id }}</td>
          <td>{{ log.order_id }}</td>
          <td>{{ log.method }}</td>
          <td>{{ log.amount.toFixed(2) }} ₺</td>
          <td>{{ log.status }}</td>
          <td>{{ formatDate(log.created_at) }}</td>
        </tr>
      </tbody>
    </table>

    <p v-if="!logs.length && !loading">Kayıt bulunamadı.</p>
    <p v-if="error" class="error">Hata: {{ errorMessage }}</p>
  </section>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import axios from 'axios'

interface PaymentLog {
  id: number
  user_id: number
  order_id: number
  method: string
  amount: number
  status: string
  created_at: string
}

const logs = ref<PaymentLog[]>([])
const loading = ref(false)
const error = ref<{ message?: string } | string | null>(null)

const filters = reactive({
  status: '',
  method: '',
  date_from: '',
  date_to: ''
})

const fetchLogs = async () => {
  loading.value = true
  error.value = null
  try {
    const res = await axios.get('/api/admin/payment-logs', { params: filters })
    logs.value = res.data.data
  } catch (err: any) {
    error.value = err.response?.data || err.message
  } finally {
    loading.value = false
  }
}

const formatDate = (date: string) => new Date(date).toLocaleString('tr-TR')

const errorMessage = computed(() => {
  if (typeof error.value === 'string') return error.value
  if (error.value && typeof error.value === 'object' && 'message' in error.value) {
    return (error.value as any).message
  }
  return ''
})
</script>

<style scoped>
.admin-payment-logs {
  max-width: 1000px;
  margin: auto;
}
form {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 0.5rem;
  border: 1px solid #ccc;
}
.error {
  color: red;
}
</style>