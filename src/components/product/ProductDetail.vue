<template>
  <div class="product-detail">
    <h2>{{ product.title }}</h2>
    <p>{{ product.description }}</p>
    <p>Fiyat: {{ product.price }}₺</p>
    <p>Kategori: {{ product.category }}</p>
    <p>Stok: {{ product.stock }}</p>
  </div>
</template>
<script setup>
import { ref } from 'vue';
const product = ref({
  title: 'Ürün 1',
  description: 'Açıklama',
  price: 100,
  category: 'Kategori',
  stock: 10
});
</script>
<style scoped>
.product-detail { max-width: 500px; margin: auto; }
</style>