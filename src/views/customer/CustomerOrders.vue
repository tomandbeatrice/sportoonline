<template>
  <div class="customer-orders">
    <h2>Sipariş Geçmişim</h2>
    <ul>
      <li v-for="order in orders" :key="order.id">Sipariş #{{ order.id }} - {{ order.status }}</li>
    </ul>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
const orders = ref([
  { id: 1, status: 'Teslim edildi' },
  { id: 2, status: 'Hazırlanıyor' }
])
</script>
<style scoped>
.customer-orders { max-width: 600px; margin: 2rem auto; }
ul { list-style: none; padding: 0; }
li { margin-bottom: 8px; }
</style>