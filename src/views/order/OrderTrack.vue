<template>
  <div class="order-track" v-if="order">
    <h2>Sipariş Takibi</h2>
    <p>Sipariş ID: {{ order.id }}</p>
    <p>Durum: {{ order.status }}</p>
    <p>Kargo Takip: {{ order.tracking_code }}</p>
    <p>Tarih: {{ formatDate(order.created_at) }}</p>
  </div>
  <div v-else>
    <h2>Sipariş Takibi</h2>
    <p>Sipariş bulunamadı veya yükleniyor...</p>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const order = ref<any>(null)

onMounted(async () => {
  // Örnek API isteği (gerçek API ile değiştirilebilir)
  // const res = await fetch(`/api/orders/${route.params.id}/track`)
  // order.value = await res.json()

  // Demo veri (API yoksa)
  if (route.params.id === '1') {
    order.value = {
      id: 1,
      status: 'Kargoya verildi',
      tracking_code: 'ABC123456',
      created_at: '2025-09-18T10:00:00'
    }
  } else {
    order.value = null
  }
})

function formatDate(date: string) {
  return new Date(date).toLocaleString('tr-TR')
}
</script>

<style scoped>
.order-track { max-width: 600px; margin: 2rem auto; }
</style>