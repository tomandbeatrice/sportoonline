<template>
  <div>
    <h2>Satış Performansı</h2>
    <ul>
      <li>Toplam Sipariş: {{ stats.totalOrders }}</li>
      <li>Tamamlanan Sipariş: {{ stats.completedOrders }}</li>
      <li>Dönüşüm Oranı: {{ stats.completionRate }}%</li>
      <li>Toplam Gelir: {{ stats.revenue }}₺</li>
    </ul>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'

const stats = ref({})

onMounted(async () => {
  const res = await axios.get('/api/stats/seller/123') // örnek satıcı ID
  stats.value = res.data
})
</script>