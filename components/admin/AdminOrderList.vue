<template>
  <div class="admin-order-list">
    <h2>Siparişler</h2>
    <ul>
      <li v-for="order in orders" :key="order.id">
        <span>{{ order.product }}</span> - <span>{{ order.status }}</span>
      </li>
    </ul>
  </div>
</template>
<script setup>
import { ref } from 'vue';
const orders = ref([
  { id: 1, product: 'Ürün 1', status: 'Beklemede' },
  { id: 2, product: 'Ürün 2', status: 'Kargoda' }
]);
</script>
<style scoped>
.admin-order-list { max-width: 600px; margin: auto; }
</style>