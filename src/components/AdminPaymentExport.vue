<template>
  <section class="admin-payment-export">
    <h2>Ödeme Logları Export</h2>

    <form @submit.prevent="exportCsv">
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

      <button :disabled="loading">CSV Dışa Aktar</button>
    </form>

    <p v-if="error" class="error">Hata: {{ errorMessage }}</p>
  </section>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import axios from 'axios'

const loading = ref(false)
const error = ref<{ message?: string } | string | null>(null)

const filters = reactive({
  status: '',
  method: '',
  date_from: '',
  date_to: ''
})

const exportCsv = async () => {
  loading.value = true
  error.value = null
  try {
    const params = new URLSearchParams(filters as any).toString()
    const url = `/api/admin/payment-logs/export?${params}`
    const res = await axios.get(url, { responseType: 'blob' })

    const blob = new Blob([res.data], { type: 'text/csv' })
    const link = document.createElement('a')
    link.href = URL.createObjectURL(blob)
    link.download = `payment_logs_${new Date().toISOString()}.csv`
    link.click()
    URL.revokeObjectURL(link.href)
  } catch (err: any) {
    error.value = err.response?.data || err.message
  } finally {
    loading.value = false
  }
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
.admin-payment-export {
  max-width: 800px;
  margin: auto;
}
form {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}
button {
  padding: 0.5rem 1rem;
}
.error {
  color: red;
}
</style>