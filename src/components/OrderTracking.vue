<template>
  <div v-if="order">
    <h2>Sipariş Takibi — #{{ order.id }}</h2>

    <p>Durum: {{ order.status }}</p>

    <div v-if="order.tracking_code">
      <p>Takip No: {{ order.tracking_code }}</p>
      <button @click="trackShipment">Kargo Durumunu Sorgula</button>
      <p v-if="trackingStatus">📦 {{ trackingStatus }}</p>
    </div>
    <div v-else>
      <p>Kargo henüz oluşturulmadı.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const order = ref(null)
const trackingStatus = ref('')

onMounted(async () => {
  const res = await fetch(`/api/orders/${route.params.id}`)
  order.value = await res.json()
})

async function trackShipment() {
  const res = await fetch(`/api/shipping/track/${order.value.tracking_code}`)
  const result = await res.json()
  trackingStatus.value = result.status || 'Durum alınamadı'
}
</script>