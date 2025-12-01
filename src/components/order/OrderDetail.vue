<template>
  <div class="order-detail">
    <h2>Sipariş Detayı</h2>
    <p>Ürün: {{ order.product }}</p>
    <p>Durum: {{ order.status }}</p>
    <p>Tarih: {{ order.date }}</p>
    <p>Toplam: {{ order.total }}₺</p>
  </div>
</template>
<script setup>
import { ref } from 'vue';
const order = ref({
  product: 'Ürün 1',
  status: 'Beklemede',
  date: '2025-11-11',
  total: 100
});
</script>
<style scoped>
.order-detail { max-width: 500px; margin: auto; }
</style>