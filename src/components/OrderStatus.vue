<template>
  <div class="order-status">
    <h2>Siparişlerim</h2>

    <div v-if="orders.length === 0">Henüz siparişiniz yok.</div>

    <div v-else>
      <div v-for="order in orders" :key="order.id" class="order-card">
        <p><strong>Sipariş No:</strong> {{ order.id }}</p>
        <p><strong>Tarih:</strong> {{ formatDate(order.created_at) }}</p>
        <p><strong>Durum:</strong> {{ statusLabel(order.status) }}</p>
        <p><strong>Toplam:</strong> {{ order.total.toFixed(2) }} ₺</p>
        <button @click="viewExportLog(order.id)">Export Log</button>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

interface Order {
  id: number
  created_at: string
  status: string
  total: number
}

const orders = ref<Order[]>([])

const formatDate = (dateStr: string) => {
  return new Date(dateStr).toLocaleDateString('tr-TR')
}

const statusLabel = (status: string) => {
  switch (status) {
    case 'pending': return 'Hazırlanıyor'
    case 'shipped': return 'Kargoda'
    case 'delivered': return 'Teslim Edildi'
    default: return 'Bilinmiyor'
  }
}

const viewExportLog = async (orderId: number) => {
  const res = await axios.get(`/api/export-logs?order_id=${orderId}`)
  console.log('Export log:', res.data)
}

onMounted(async () => {
  const res = await axios.get('/api/orders')
  orders.value = res.data
})
</script>

<style scoped>
.order-status {
  padding: 2rem;
}
.order-card {
  border: 1px solid #ccc;
  padding: 1rem;
  margin-bottom: 1rem;
}
</style>