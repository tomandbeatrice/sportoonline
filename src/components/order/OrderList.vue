<template>
  <div class="order-list">
    <h2>Siparişlerim</h2>
    <ul>
      <li v-for="order in orders" :key="order.id">
        <span>{{ order.product }}</span> - <span>{{ order.status }}</span>
      </li>
    </ul>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const orders = ref([]);

onMounted(async () => {
  try {
    const res = await axios.get('/api/orders');
    orders.value = res.data;
  } catch (e) {
    console.error('Siparişler yüklenemedi:', e);
  }
});
</script>
<style scoped>
.order-list { max-width: 600px; margin: auto; }
</style>