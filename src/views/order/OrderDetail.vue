<template>
  <div v-if="order">
    <h2>Sipariş #{{ order.id }}</h2>
    <p>Durum: {{ order.status }}</p>
    <p>Tarih: {{ formatDate(order.created_at) }}</p>
    <p>Toplam: {{ order.price }}₺</p>
    <p>Kargo Takip: {{ order.tracking_code }}</p>
    <p>Ödeme: {{ order.paid_at ? 'Ödendi' : 'Bekliyor' }}</p>

    <h3>Ürünler</h3>
    <ul>
      <li v-for="item in order.order_details" :key="item.id">
        {{ item.product.name }} — {{ item.quantity }} adet — {{ item.price }} ₺
      </li>
    </ul>

    <h3>Log Geçmişi</h3>
    <ul>
      <li v-for="log in order.logs" :key="log.id">
        {{ formatDate(log.created_at) }} — {{ log.message }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const order = ref<any>(null)

onMounted(async () => {
  const res = await axios.get(`/api/orders/${route.params.id}`)
  order.value = res.data
})

function formatDate(date: string) {
  return new Date(date).toLocaleString('tr-TR')
}
</script>