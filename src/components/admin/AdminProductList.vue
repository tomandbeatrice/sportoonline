<template>
  <div class="admin-product-list">
    <h2>Ürünler</h2>
    <ul>
      <li v-for="product in products" :key="product.id">
        <span>{{ product.title }}</span> - <span>{{ product.price }}₺</span>
      </li>
    </ul>
  </div>
</template>
<script setup>
import { ref } from 'vue';
const products = ref([
  { id: 1, title: 'Ürün 1', price: 100 },
  { id: 2, title: 'Ürün 2', price: 200 }
]);
</script>
<style scoped>
.admin-product-list { max-width: 600px; margin: auto; }
</style>